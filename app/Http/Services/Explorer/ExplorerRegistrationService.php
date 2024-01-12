<?php

namespace App\Http\Services\Explorer;

use App\ContactRoleEnum;
use App\ContactStatusEnum;
use App\Enum\ExplorerRegistrationStatusEnum;
use App\Enum\ExplorerRegistrationTypeEnum;
use App\Enum\UserRolesEnum;
use App\Http\Helpers\UserHelper;
use App\Models\Portfolio;
use App\Models\Contact;
use App\Models\ExplorerCode;
use App\Models\ExplorerRegistration;
use App\Notifications\ExplorerAdminNotification;
use App\Notifications\NewContactRegistration;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use phpDocumentor\Reflection\Element;
use SendinBlue\Client\ApiException;

class ExplorerRegistrationService
{
    /**
     * @throws Exception
     */
    // user registration to explorer function with code generation
    public function register(User $user, $params)
    {
        $registration = ExplorerRegistration::where('user_id', '=', $user->id)->first();

        if ($registration == null) {
            $registration = new ExplorerRegistration();
            $registration->user_id = $user->id;
            $registration->status = ExplorerRegistrationStatusEnum::WAITING;

            if (!UserHelper::isFreelance($user)) {
                $registration->registration_type = ExplorerRegistrationTypeEnum::CLIENT;
                $registration->budget = $params['budget'];
                $registration->project_description = $params['project_description'];
            } else {
                $registration->registration_type = ExplorerRegistrationTypeEnum::FREELANCE;
                $user->lastname = $user['lastname'];
                $user->firstname = $user['firstname'];
                $user->email = $user['email'];
                $user->phone = $user['phone'];
                $user->job_id = $user['job_id'];
                $user->website = $user['website'];
                $user->description = $user['description'];
            }
            try {
                $registration->save();
                $user->explorer_access_code = $this->generateCode();
                $user->save();
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        } else {
            return response()->json(["Exception"=>"User already registered"],500);
        }

        return $registration;
    }

    public function newTalentAdded($id)
    {
        $user = new User();
        $registration = DB::table('explorer_registrations')->where('user_id', $id)->where('status', ExplorerRegistrationStatusEnum::ACCEPTED)->leftJoin('users', 'user_id', '=', 'users.id')->get();
        $is_exist = false;
        foreach($registration as $register) {
            $user_email = $register->email;
            if ($register->status == ExplorerRegistrationStatusEnum::ACCEPTED) {
                //ask for a demo contact list
                $email = $register->email;
                $name = $register->firstname;
                $status = $register->status;
                $access = $register->explorer_access_code;
                $lastname = $register->lastname;
                $phone = $register->phone;

                //sendinblue params
                $config = \SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', getenv("SENDINBLUE_KEY"));
                $apiContact = new \SendinBlue\Client\Api\ContactsApi(new \GuzzleHttp\Client(), $config);
                $updateContact = new \SendinBlue\Client\Model\UpdateContact();
                $createContact = new \SendinBlue\Client\Model\CreateContact();
                $createContact['email'] = $email;
                $createContact['lastname'] = $lastname;
                $createContact['firstname'] = $name;
                $createContact['phone'] = $phone;
                $createContact["explorer_access"]= $access;
                $createContact['listIds'] = [10];
                $updateContact['listIds'] = [10];
                $limit = 50;
                $offset = 0;

                $contacts = $apiContact->getContacts($limit, $offset);
                $contacts = $contacts["contacts"] ?? false;
                if ($contacts) {
                    foreach ($contacts as $is_contact) {
                        if (!empty($is_contact["email"]) && $is_contact["email"] == $email) {
                            $is_exist = true;
                        }
                    }
                }

            }

            $check = DB::table('users')->where('email', "=", $user_email)->first();
            if (empty($check)) {
                $user->save();
                if (!$is_exist) {
                    $createContact['attributes'] = [
                        "ID" => $user->id,
                        'EMAIL' => $email,
                        'FIRSTNAME' => $name,
                        'LASTNAME' => $lastname,
                        'EXPLORER_ACCESS'=>$access,
                        'PHONE' => $phone,
                        'STATUS' => $status];
                    $apiContact->createContact($createContact);
                } else {
                    $identifier = $user->email;
                    $updateContact['attributes'] = [
                        "ID" => $user->id,
                        'EMAIL' => $user->email,
                        'FIRSTNAME' => $user->firstname,
                        'LASTNAME' => $user->lastname,
                        'EXPLORER_ACCESS'=>$access,
                        'PHONE' => $user->phone,
                        'STATUS' => $user->user_status
                    ];
                    $apiContact->updateContact($identifier, $updateContact);
                }
                return redirect()->route('unlock')->withMessage('User has been accepted');
            } else {
                $identifier = $check->email;
                $createContact['attributes'] = [
                    "ID" => $user->id,
                    'EMAIL' => $email,
                    'FIRSTNAME' => $name,
                    'LASTNAME' => $lastname,
                    'EXPLORER_ACCESS'=>$access,
                    'PHONE' => $phone,
                    'STATUS' => $status];
                $updateContact['attributes'] = [
                    "ID" => $check->id,
                    'EMAIL' => $check->email,
                    'FIRSTNAME' => $check->firstname,
                    'LASTNAME' => $check->lastname,
                    'EXPLORER_ACCESS'=>$access,
                    'PHONE' => $check->phone,
                    'STATUS' => $check->user_status
                ];
                if ($is_exist) {
                    $apiContact->updateContact($identifier, $updateContact);
                } else {
                    $apiContact->createContact($createContact);
                }
                return redirect()->route('unlock')->withMessage('User has already been accepted');
            }
        }

    }

    public function getUserRegistrationCode(User $user)
    {
        return $user->explorer_access_code;
    }

    // tony the function to definitely grant access to the user
    public function markUserAsRegistered(User $user)
    {
        $user->is_explorer = true;
        $user->save();
    }


    /**
     * @throws Exception
     */
    // tony here if the grantExplorerAccess function has been passed successfully this function is called on the controller to unlock the user
    public function unlockExplorer(User $user, $accessCode)
    {
        $explorerCode = ExplorerCode::where('is_active', true)->where('access_code', $accessCode)->first();
        if ($explorerCode) {
            try {
                $user->is_explorer = true;
                $user->save();

                //$explorerCode->is_active = false;
                $explorerCode->users()->attach($user);
                $explorerCode->save();
            } catch (\Exception $exception) {
                throw $exception ($exception->getMessage());
            }
        } else {
            $registration = ExplorerRegistration::where('user_id', '=', $user->id)->first();
            if ($registration->status == ExplorerRegistrationStatusEnum::ACCEPTED) {
                if ($accessCode == $user->explorer_access_code) {
                    $user->is_explorer = true;
                    $user->save();
                } else {
                    throw new Exception("Explorer access code not valid");
                }
    
            } else {
                throw new Exception("Explorer Registration not validated");
            }
        }
    }


    public function grantExplorerAccess($id)
    {
        $user = new User();
        return DB::table('explorer_registrations')->where('user_id', $id)->where('status', ExplorerRegistrationStatusEnum::WAITING)->update(['status' => ExplorerRegistrationStatusEnum::ACCEPTED]) && $this->newTalentAdded($id);
    }

    public function refuseExplorerAccess($id)
    {
        return DB::table('explorer_registrations')->where('user_id', $id)->where('status', ExplorerRegistrationStatusEnum::WAITING)->update(['status' => ExplorerRegistrationStatusEnum::REFUSED]);
    }

    public function getWaitingFreelanceRegistration()
    {
        return ExplorerRegistration::where('status', '=', ExplorerRegistrationStatusEnum::WAITING)
            ->where('registration_type', '=', ExplorerRegistrationTypeEnum::FREELANCE)
            ->get();
    }

    public function getClientWaitingRegistration()
    {
        return ExplorerRegistration::where('status', '=', ExplorerRegistrationStatusEnum::WAITING)
            ->where('registration_type', '=', ExplorerRegistrationTypeEnum::CLIENT)
            ->get();
    }

    private function generateCode()
    {
        return substr(str_shuffle(
            'abcdefghijklmnopqrstuvwxyzABCEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 1, 10);
    }
}
