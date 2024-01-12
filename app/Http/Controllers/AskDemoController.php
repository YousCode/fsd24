<?php

namespace App\Http\Controllers;


use App\ContactStatusEnum;
use App\Http\Helpers\ContactHelper;
use App\Models\Contact;
use App\Notifications\NewContactRegistration;
use App\User;
use Auth;
use Illuminate\Validation\Rule;
use DB;
use Exception;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use SendinBlue\Client\ApiException;
use function redirect;
use function view;
use Illuminate\Support\Facades\Log;
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\SendSmtpEmail;



class AskDemoController extends Controller
{
    use ValidatesRequests;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /*------- showing the form for the /Contact Page -------*/

    public function ContactViewForm()
    {
        return view ('contact');
    }
    /*------- showing the form for the contactus -------*/


    /**
     *
     * @throws Exception
     */
    /*------- Procedural function for the contact form  -------*/
    public function Create(ContactHelper $contactHelper)
    {
        //making validator for user request, using lang message to fill each input field error message
        $validator = Validator::make($this->request->all(), [
            'workspace' => ['required','string','max:255'],
            'lastname' => ['required','string','max:255'],
            'firstname' => ['required','string','max:255'],
            'email'     => ['required','string','max:255','unique:users',
                'regex:/^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/',
                Rule::unique('contacts','email')
                    ->where('contacts.status', ContactStatusEnum::ACCEPTED)],
            'contact_status' => 'string',
            'phone'     => ['required','regex:/^([0-9\s\-\+]{0,15})$/' ,'min:10','max:10'],
        ]);

        $contact = new Contact();
        $contact->workspace = $this->request->input('workspace');
        $contact->lastname = $this->request->input('lastname');
        $contact->firstname = $this->request->input('firstname');
        $contact->phone = $this->request->input('phone');
        $contact->email = $this->request->input('email');
        $contact->access_token = $this->UnlockAccess();
        $contact->contact_status = $this->request->input('contact_status');
        $contact->status = ContactStatusEnum::WAITING;
        $admin = User::where('is_admin', 1)->first();
        $checkUser = DB::table('users')->where('email',"=",$contact->email)->first();
        $checkContact = DB::table('contacts')->where('email',"=",$contact->email)->first();

        if ($validator->fails()) {
            return $validator->errors()->messages() ? redirect()->back()->withErrors($validator)->withInput() : abort(404);
        }

        try {
            if($checkUser === null)
            {
                if($checkContact === null)
                {
                    $contact->save();
                }else{
                    if(!empty($checkContact->id))
                    {
                        $contacts = Contact::findOrFail($checkContact->id);
                        $contacts->update(['lastname'=>$contact->lastname]);
                        $contacts->update(['firstname'=>$contact->firstname]);
                        $contacts->update(['phone'=>$contact->phone]);
                        $contacts->update(['email'=>$contact->email]);
                        $contacts->update(['access_token'=>$contact->access_token]);
                        $contacts->update(['contact_status'=>$contact->contact_status]);
                        $contacts->update(['status'=>$contact->status]);
                        $contacts->save();
                    }
                }
                $this->sendTransactionEmail($contact);
                $admin->notify(new NewContactRegistration($contact));
                return response()->json([
                    "success" => true,
                    "message" => "Votre demande a bien été envoyé"
                ]);
            }

        } catch (ApiException $e) {
            throw new \Exception($e->getMessage());
        }
    }
    protected function sendTransactionEmail(Contact $contact)
    {
        $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', env('SENDINBLUE_KEY'));
        $apiInstance = new TransactionalEmailsApi(new \GuzzleHttp\Client(), $config);
        $email = new SendSmtpEmail();
        $email->setTemplateId(33);
        $email->setParams(['FIRSTNAME' => $contact->firstname]); 
        $email->setTo([['email' => $contact->email, 'name' => $contact->firstname]]);
        try {
            $apiInstance->sendTransacEmail($email);
        } catch (\Exception $e) {
            throw new \Exception('Erreur lors de l\'envoi de l\'e-mail: ' . $e->getMessage());
        }
    }

    public function checkInputBeforeValidation()
    {
        //making validator for user request, using lang message to fill each input field error message
        $validator = Validator::make($this->request->all(), [
            'workspace' => ['required','string','max:255'],
            'lastname' => ['required','string','max:255'],
            'firstname' => ['required','string','max:255'],
            'email'     => ['required','string','max:255','unique:users',
                'regex:/^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/',
                Rule::unique('contacts','email')
                    ->where('contacts.status', ContactStatusEnum::ACCEPTED)],
            'phone'     => ['required','min:10','regex:/^([0-9\s\-\+]{0,15})$/','max:10'],
            'contact_status' => 'string',
        ]);

        //getting user value filling in the input
        $contact = new Contact();
        $contact->workspace = $this->request->input('workspace');
        $contact->lastname = $this->request->input('lastname');
        $contact->firstname = $this->request->input('firstname');
        $contact->phone = $this->request->input('phone');
        $contact->email = $this->request->input('email');
        $contact->access_token = $this->UnlockAccess();
        $contact->contact_status = $this->request->input('contact_status');
        $contact->status = ContactStatusEnum::WAITING;
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

    //delete user after approval by admin
    public function destroy($id)
    {
        return Contact::where('id', $id)->firstorfail()->delete();

    }

    protected function UnlockAccess(): string
    {
        $hash = Hash::make('kolab');
        $hash = str_replace("/","","$hash");
        $hash = str_replace(".","","$hash");
        return str_replace("/","","$hash");
    }

}