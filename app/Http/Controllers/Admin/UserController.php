<?php

namespace App\Http\Controllers\Admin;

use App\ContactRoleEnum;
use App\Enum\ExplorerRegistrationStatusEnum;
use App\Models\ExplorerRegistration;
use App\Models\Workspace;
use App\Models\ExplorerCode;
use App\Models\Contact;
use Image;
use App\ContactStatusEnum;
use Illuminate\Support\Facades\DB;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use DateTime;

class UserController extends Controller
{
    public function profile()
    {
        return view('profile', array('user'=> \Auth::user() ));
    }

    public function avatarUpdate(Request $request)
    {
        if ($request->hasFile('avatar'))
        {
            $avatar = $request-> file('avatar');
            $filename = time() . '.' . $avatar->getClientOriginalExtension();
            Image::make($avatar)->resize(300,300)->save(public_path('/upload/avatars/'. $filename));
            $user = Auth::user();
            $user->avatar = $filename;
            $user->save();
        }

        return view('profile',array('user' => \Auth::user()));
    }

    /// ADMIN APPROVAL SECTION

    public function newUserCreated(Contact $contact)
    {
        $contact = Contact::findOrFail($contact->id);
        $user = new User();
        $contact_email = $contact->email;
        $contact_workspace = strtolower(trim($contact->workspace));
        $is_exist = false;

        if( $contact->status == ContactStatusEnum::WAITING)
        {
            //ask for a demo contact list
            $email= $contact->email;
            $name = $contact->firstname;
            $status = $contact->contact_status;
            $lastname = $contact->lastname;
            $phone  =  $contact->phone;
            $token = $contact->access_token;
            //sendinblue params
            $config = \SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', (env('SENDINBLUE_KEY')));
            $apiContact = new \SendinBlue\Client\Api\ContactsApi(new \GuzzleHttp\Client(), $config);
            $updateContact = new \SendinBlue\Client\Model\UpdateContact();
            $createContact = new \SendinBlue\Client\Model\CreateContact();
            $createContact['email'] = $email;
            $createContact['firstname'] = $name;
            $createContact['phone'] =$phone;
            $createContact['listIds'] = [5];
            $updateContact['listIds'] = [5];
            $limit = 50;
            $offset = 0;

            //new user incoming from admin validation
            $user->name = ucfirst(strtolower($contact->firstname)) . ' ' . ucfirst(strtolower($contact->lastname));
            $user->email = $email;
            $user->lastname = ucfirst(strtolower($lastname));
            $user->firstname = ucfirst(strtolower($contact->firstname));
            $user->phone = $phone;
            $user->token_login = $token;
            $user->user_status = $status;
            $user->password = $this->UnlockAccess();
            $user->is_user = true;
            $contacts=$apiContact->getContacts($limit, $offset);
            $contacts= $contacts["contacts"] ?? false;
            if ($contacts) {
                foreach($contacts as $is_contact){
                    if (!empty($is_contact["email"]) && $is_contact["email"] == $email) {
                        $is_exist = true;
                    }
                }
            }

            if($status === ContactRoleEnum::freelance){
                $user->assignRole("talent");
                $user->is_user = true;
            } elseif ($status === ContactRoleEnum::business || ContactRoleEnum::producer) {
                $user->assignRole("client");
            } else {
                throw new \Exception("This role does not exist");
                return redirect()->back();
            }
            $contact->save();
        }

        $check = DB::table('users')->where('email',"=",$contact_email)->first();
        if (empty($check)) {
            $contact->update(["approved_at" => now()]);
            $contact->update(["status" => ContactStatusEnum::ACCEPTED]);
            $user->save();

            //create workspace
            $checkWorkspace = DB::table('workspaces')->where('name', "LIKE", '%' . $contact_workspace . '%')->first();
            $workspaceId = false;
            if (empty($checkWorkspace)) {
                $workspace = new Workspace();
                $workspace->name = $contact_workspace;
                $workspace->owner_id = $user->id;
                $workspace->save();
                $workspaceId = $workspace->id;
            } else {
                $workspaceId = $checkWorkspace->id;
            }

            DB::table('workspace_members')->insert([
                'user_id' => $user->id,
                'workspace_id' => $workspaceId,
            ]);

            if (!$is_exist) {
                $createContact['attributes'] = [
                    "ID"    => $user->id,
                    'EMAIL' => $email,
                    'FIRSTNAME' => $name,
                    'LASTNAME' => $lastname,
                    'PHONE' => $phone,
                    'TOKEN' => $contact->access_token,
                    'STATUS' => $status];
                $apiContact->createContact($createContact);
            } else {
                $identifier = $user->email;
                $createContact['attributes'] = [
                    "ID"    => $user->id,
                    'EMAIL' => $email,
                    'FIRSTNAME' => $name,
                    'LASTNAME' => $lastname,
                    'PHONE' => $phone,
                    'TOKEN' => $contact->access_token,
                    'STATUS' => $status];
                /*$updateContact['attributes'] = [
                    "ID"    => $user->id,
                    'EMAIL' => $user->email,
                    'FIRSTNAME' => $user->firstname,
                    'LASTNAME' => $user->lastname,
                    'PHONE' => $user->phone,
                    'TOKEN' => $user->token_login,
                    'STATUS' => $user->user_status
                ];*/
                $apiContact->deleteContact($identifier);
                $apiContact->createContact($createContact);
                //$apiContact->updateContact($identifier, $updateContact);
            }

            return redirect()->route('unlock')->withMessage('User has been accepted');
        } else {
            $identifier = $check->email;
            $createContact['attributes'] = [
                "ID"    => $user->id,
                'EMAIL' => $email,
                'FIRSTNAME' => $name,
                'LASTNAME' => $lastname,
                'PHONE' => $phone,
                'TOKEN' => $contact->access_token,
                'STATUS' => $status];
            $updateContact['attributes'] = [
                "ID"    => $check->id,
                'EMAIL' => $check->email,
                'FIRSTNAME' => $check->firstname,
                'LASTNAME' => $check->lastname,
                'PHONE' => $check->phone,
                'TOKEN' => $check->token_login,
                'STATUS' => $check->user_status
            ];

            if ($is_exist) {
                $apiContact->updateContact($identifier, $updateContact);
            } else{
                $apiContact->createContact($createContact);
            }

            return redirect()->route('unlock')->withMessage('User has already been accepted');
        }
    }
    public function getCertification(User $user)
    {
        $user= User::findOrFail($user->id);
        $user->update(["certified" => true]);
        $user->save();
        return redirect()->route('unlock')->withMessage('User has been certified');
    }

    public function DeniedUser(Contact $contact){
        $contact = Contact::findOrFail($contact->id);
        $contact->update(["status" => ContactStatusEnum::DENIED]);
        $email = $contact->email;

        $denied = DB::table('users')->where('email',"=",$email)->first();
        if (empty($denied)) {
            $contact->delete();
            return redirect()->route('unlock')->withMessage('User has been denied');
        } else {
            return redirect()->route('unlock')->withMessage('User has already been denied');
        }
    }

    /*------- Access token generator  -------*/
    public function UnlockAccess(): string
    {
        $hash = Hash::make('kolab');
        return str_replace("/",".","$hash");
    }

    public function explorerCodeGenerate(Request $request)
    {
        $explorerCode = new ExplorerCode();
        $explorerCode->access_code = $this->generateCode();
        $explorerCode->usage = 1;
        $explorerCode->expiration = new DateTime();
        $explorerCode->is_active = 1;

        try {
            $explorerCode->save();
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }

        return redirect()->route('unlock')->withMessage('Le nouveau code Explorer a bien été généré');
    }

    public function explorerCodeActions(Request $request)
    {
        $action = $request->input('action');
        $explorerCodeId = $request->input('explorerCodeId');
        $explorerAccessCode = $request->input('accessCode');
        $explorerIsActive = $request->input('isActive');
        $explorerCode = ExplorerCode::findOrFail($explorerCodeId);
        try {
            if ($action == 'DELETE') {
                $explorerCode->delete();
                $message = 'Le code Explorer a bien été supprimé !';
            } elseif ($action == 'UPDATE') {
                if ($explorerIsActive) {
                    $explorerCode->is_active = !$explorerCode->is_active;
                }
                if ($explorerAccessCode) {
                    $explorerCode->access_code = $explorerAccessCode;
                }
                $explorerCode->save();
                $message = 'Le code Explorer a bien été mis à jour !';
            }
            return redirect()->route('unlock')->withMessage($message);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }

    private function generateCode()
    {
        return substr(str_shuffle(
            'abcdefghijklmnopqrstuvwxyzABCEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 1, 10);
    }
}