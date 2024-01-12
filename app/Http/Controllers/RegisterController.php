<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\Skill;
use App\Providers\RouteServiceProvider;
use App\User;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    //use RegistersUsers;

    protected $redirectTo = RouteServiceProvider::HOME;


    public function showRegistrationForm(Request $request,$token)
    {
        $user = DB::table('users')->where('token_login', $token)->first();
        if(!empty($user->email_verified_at)){
            return redirect("/");
        }
        $job = Job::all();
        foreach ($job as $j) {
            $j->name = __('job.' . $j->name);
        }
        $skill = Skill::all();
        $fname = $user->firstname;
        $lname = $user->lastname;
        $result = strtoupper($lname[0]).strtoupper($fname[0]);
        if($user){
            return view('auth.register',compact('user','job','skill',"result",'token'));
        }
        return redirect()->route("contact");
    }
    protected function freelanceValidator(Request $request)

    {
        $token = $request->input('token');
        $user = User::where('token_login', $token)->first();


        $validator = Validator::make($request->all(), [
            'file' => ['max:2000',Rule::dimensions()->minWidth(300)->minHeight(300)],
            'lastname' => ['required','string','max:255','regex:/^[a-zA-ZÀ-ú\s\-]*$/'],
            'firstname' => ['required','string','max:255','regex:/^[a-zA-ZÀ-ú\s\-]*$/'],
            'email' => 'required|string|max:255',
            'password' => ['required', 'string', 'min:8','confirmed','regex:/^(?=.*[A-Za-zÀ-ú])(?=.*[A-ZÀ-ú])(?=.*\d)(?=.*[$@$!"\'%*#?\-\_\`\=\\^\/\/&])[A-Za-zÀ-ú\d$@$!%*"\-\_\`\=\\^\'\/#?&]{8,}$/'],
            'password_confirmation' => ['required','same:password'],
            'phone'     => ['required','regex:/^([0-9\s\-\+]{0,15})$/' ,'min:10', 'max:10'],
            'skills'     =>['required'],
            'description'     =>[],
            'job'       => ['required'],
            'website'   => [],
            'check'     =>  ['required']

        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else{
            try {
                $job = Job::find($request->input('job'));
                $user->lastname = $request->input('lastname');
                $user->firstname = $request->input('firstname');
                $user->avatar = $request->input('file');
                $user->email = $request->input('email');
                $user->password = Hash::make($request->input('password'));
                $user->phone = $request->input('phone');
                $user->job_id = $request->input('job');
                $user->description = $request->input('description');
                $user->skills()->sync(json_decode(json_encode($request->input('skills'))));
                //$user->website = $request->input('website');
                if (!str_contains($request->input('website'), 'https://') && $request->input('website') !== null) {
                    $user->website = 'https://' . $request->input('website');
                } else {
                    $user->website = $request->input('website');
                }
                if (!str_contains($request->input('website'), 'http://') && $request->input('website') !== null) {
                    $user->website = 'http://' . $request->input('website');
                } else {
                    $user->website = $request->input('website');
                }
                if (!empty($avatar)) {
                    $filename = time() . '.' . $avatar->getClientOriginalExtension();
                    $extension = $avatar->getClientOriginalExtension();
                    $location = "upload/avatars/";
                    $filePath = url('/upload/avatars/' . $avatar);
                    $test = $avatar->move($location, $filename);
                    $url = $filename;
                    $user->avatar = $url;
                } else {
                    $user->avatar = 'user.jpg';
                }
                $user->markEmailAsVerified();
                $user->save();
                return redirect("login");
            }catch (Exception $e){
                throw new Exception($e->getMessage().'An Error occured return to the login page',500);
            }
        }
    }

    protected function updateClient(Request $request)
    {
        $token = $request->input('token');
        $user = User::where('token_login', $token)->first();


        $validator = Validator::make($request->all(), [
            'file' => ['max:2000',Rule::dimensions()->minWidth(300)->minHeight(300)],
            'lastname' => ['required','string','max:255','alpha','regex:/^[a-zA-ZÀ-ú\s]*$/'],
            'firstname' => ['required','string','max:255','alpha','regex:/^[a-zA-ZÀ-ú\s]*$/'],
            'email' => 'required|string|max:255',
            'password' => ['required', 'string', 'min:8','confirmed','regex:/^(?=.*[A-Za-zÀ-ú])(?=.*[A-ZÀ-ú])(?=.*\d)(?=.*[$@$!"\'%*#?\-\_\`\=\\^\/\/&])[A-Za-zÀ-ú\d$@$!%*"\-\_\`\=\\^\'\/#?&]{8,}$/'],
            'password_confirmation' => ['required','same:password','regex:/^(?=.*[A-Za-zÀ-ú])(?=.*[A-ZÀ-ú])(?=.*\d)(?=.*[$@$!"\'%*#?\-\_\`\=\\^\/\/&])[A-Za-zÀ-ú\d$@$!%*"\-\_\`\=\\^\'\/#?&]{8,}$/'],
            'phone'     => ['required','regex:/^([0-9\s\-\+]{0,15})$/' ,'min:10'],
            'job'       => ['required','max:255' ],
            'company'   => [],
            'check'     =>  ['required']

        ]);
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        } else{
            $job = Job::find($request->input('job'));
            $user->lastname = $request->input('lastname');
            $user->firstname = $request->input('firstname');
            $user->avatar = $request->input('file');
            $user->email = $request->input('email');
            $user->password = Hash::make($request->input('password'));
            $user->phone = $request->input('phone');
            $user->job_id = $request->input('job');
            $user->company = $request->input('company');
            $avatar = $request->file('file');
            if(!empty($avatar)) {
                $filename = time() . '.' . $avatar->getClientOriginalExtension();
                $extension = $avatar->getClientOriginalExtension();
                $location = "upload/avatars/";
                $filePath = url('/upload/avatars' . $avatar);
                $test = $avatar->move($location, $filename);
                $url = $filename;
                $user->avatar = $url;
            }else{
                $user->avatar = 'user.jpg';
            }
            $user->save();
            $user->markEmailAsVerified();
            return redirect("login");
        }

    }
}
