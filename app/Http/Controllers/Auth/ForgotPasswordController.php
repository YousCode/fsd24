<?php

namespace App\Http\Controllers\Auth;

use App\ContactStatusEnum;
use App\Http\Controllers\Controller;
use App\User;
use http\Env\Request;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use App\Providers\RouteServiceProvider;
use GuzzleHttp\Client;
use App\Mail\Contact;
use Illuminate\Support\Facades\Validator;


class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;
    protected $request;

    public function __construct(\Illuminate\Http\Request $request)
    {
        $this->request = $request;
    }

    public function checkInput()
    {
        //making validator for user request, using lang message to fill each input field error message
        $validator = Validator::make($this->request->all(), [
            'email'     => 'required|string|max:255|regex:/^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/',

        ]);
        //getting user value filling in the input
        $user = new User();
        $user->email = $this->request->input('email');
        //conditionnal for prevent validation form from failure mistake
        if ($validator->fails()) {
            $errors =$validator->errors();
            return response()->json([
                "success"=>false,
                "message"=> $errors
            ]);
        }else {
            return response()->json([
                "success" => true,
                "message" => "All input are valid, you can proceed to submissions"
            ]);
        }

    }



}
