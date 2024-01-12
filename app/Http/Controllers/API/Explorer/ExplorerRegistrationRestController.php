<?php

namespace App\Http\Controllers\API\Explorer;

use App\ContactRoleEnum;
use App\ContactStatusEnum;
use App\Enum\ExplorerRegistrationStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Services\Explorer\ExplorerRegistrationService;
use App\Models\ExplorerRegistration;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use SendinBlue\Client\ApiException;
use function React\Promise\Stream\first;
use function response;

class ExplorerRegistrationRestController extends Controller
{
    //
    public function create(Request $request, ExplorerRegistrationService $explorerRegistrationService)
    {
        $user = \Auth::user();
        if($user->user_status == ContactRoleEnum::business || $user->hasRole("admin") || $user->user_status == ContactRoleEnum::producer) {
            $validator = Validator::make($request->all(), [
                "budget" => ['required'],
                "project_description" => ['required']
            ]);
            if ($validator->fails()) {
                return response()->json($validator->messages(), 400);
            }
        }else if($user->user_status == ContactRoleEnum::freelance){
            if ($request->all()["firstname"]){
                $validation = Validator::make($request->all(), [
                    "skills" => ['required'],
                    "firstname"=>['required'],
                    "lastname"=>['required'],
                    "email"=>['required'],
                    "phone"=>['required'],
                    "job"=>['required'],
                ]);
                if ($validation->fails()) {
                    return response()->json($validation->messages(), 400);
                }
            }
        }
        try {
            return $explorerRegistrationService->register($user, $request->all());
        } catch (\Exception $exception) {
            return response($exception->getMessage(), 500);
        }
    }
    //giver user access to explorer function on admin panel
    public function newExplorerCreated($id, ExplorerRegistrationService $explorerRegistrationService)
    {
        try {

            $explorerRegistrationService->grantExplorerAccess($id);
            return redirect()->route('unlock')->withMessage('Explorer Access has been granted');
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
        }
    }
    // refuse user access to explorer function on admin panel
    public function newExplorerDelete($id, ExplorerRegistrationService $explorerRegistrationService)
    {
        try{

            $explorerRegistrationService->refuseExplorerAccess($id);
            return redirect()->route('unlock')->withMessage('Explorer Access has been denied');

        }catch (\Exception $e){
            throw new \Exception($e->getMessage());
        }

    }

    public function unlockAccess(Request $request, ExplorerRegistrationService $explorerRegistrationService)
    {
        $user = \Auth::user();

        $requestParams = $request->all()['params'];
        try {
            $explorerRegistrationService->unlockExplorer($user, $requestParams['access_code']);
            return response(true, 200);
        } catch (\Exception $exception) {
            return response($exception->getMessage(), 403);
        }
    }

}
