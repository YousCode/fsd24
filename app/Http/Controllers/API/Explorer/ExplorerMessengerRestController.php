<?php

namespace App\Http\Controllers\API\Explorer;

use App\Enum\ExplorerMissionPropositionStatusEnum;
use App\Enum\ExplorerMissionStatusEnum;
use App\Events\ExplorerConversation;
use App\Events\ExplorerFile;
use App\Events\ExplorerMessage;
use App\Events\Notifications;
use App\Http\Controllers\Controller;
use App\Http\Services\Explorer\ExplorerMessengerConversationService;
use App\Http\Services\Explorer\ExplorerMessengerService;
use App\Http\Services\Explorer\ExplorerMissionService;
use App\Models\ExplorerDrive;
use App\Models\ExplorerMission;
use App\Models\ExplorerMissionConversation;

use App\Models\ExplorerMissionConversationMessage;
use App\Models\ExplorerMissionProposition;
use App\Models\ExplorerMissionQuote;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Events\MessageAdded;
use App\Events\LinkAdded;
use Illuminate\Support\Facades\Validator;

class ExplorerMessengerRestController extends Controller
{
    /*************************
     * Conversations
     ************************/

    public function getConversationsList(Request $request, ExplorerMessengerService $explorerMessengerService)
    {
        $user = \Auth::user();
        return response($explorerMessengerService->getUserConversationsList($user));
    }
    public function getConversationLastMessage(Request $request, ExplorerMessengerService $explorerMessengerService)
    {
        $user = \Auth::user();
        return $explorerMessengerService->allNewMessageConversation($user);
    }

    public function getConversationMessages($conversation_id, Request $request, ExplorerMessengerService $explorerMessengerService)
    {
        $user = \Auth::user();
        try {
            return response($explorerMessengerService->getConversationMessages($user, $conversation_id));
        } catch (\Exception $e) {
            response($e->getMessage(), 500);
        }
    }

    public function markAsRead($conversation_id,Request $request, ExplorerMessengerService $explorerMessengerService){
        $user = \Auth::user();
        if ($user)
        {
            try {
                //event(new ExplorerMessage());
                return response("Conversation ${conversation_id} has been read".$explorerMessengerService->newMessageMarkAsRead($conversation_id),200);
            }catch (\Exception $e){
                return response($e->getMessage(),500);
            }
        }
    }

    public function getProjectConversationMessages($conversation_id, Request $request, ExplorerMessengerService $explorerMessengerService)
    {
        $user = \Auth::user();
        if ($user) {
            try {
                return response($explorerMessengerService->getConversationMessages($user, $conversation_id, true));
            } catch (\Exception $e) {
                response($e->getMessage(), 500);
            }
        }

        return response([], 201);
    }

    public function postConversationMessage($id, Request $request, ExplorerMessengerService $explorerMessengerService)
    {
        $user = \Auth::user();
        $requestParams = $request->all()['params'];
        try {
            $fromProject = $requestParams['fromProject'] ?? false;
            if ($fromProject) {
                event(new MessageAdded());
            } else {
                event((new Notifications($requestParams['message'])));
                event(new ExplorerMessage());
            }
            return response($explorerMessengerService->newUserMessage($id, $requestParams, $user), 201);
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }
    }
    public function getConversationDrive($id, Request $request, ExplorerMessengerService $explorerMessengerService)
    {
        // Output
        $url = ExplorerDrive::where('id_proposition', $id)->latest()->first();

        return $url;

    }

    public function postConversationDrive($id, Request $request, ExplorerMessengerService $explorerMessengerService)
    {
        $user = \Auth::user();
        $requestParams = $request->all()['params'];
        try {
            if ($explorerMessengerService->newProjectDrive($id, $requestParams, $user)) {
                event(new LinkAdded());
                event(new MessageAdded());
                return response('', 201);
            } else {
                return response('no_link', 200);
            }
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }
    }

    public function patchConversation($id, Request $request, ExplorerMessengerConversationService $explorerMessengerConversationService)
    {
        $user = \Auth::user();
        $requestParams = $request->all()['params'];

        try {
            $explorerConversation = ExplorerMissionConversation::find($id);

            switch ($requestParams['patch_type']) {
                case 'update_last_check':
                    $explorerMessengerConversationService->updateUserLastCheckDate($explorerConversation, $user);
                    break;
            }
            return response('', 200);
        } catch (\Exception $exception) {
            return response($exception->getMessage(), 500);
        }
    }

    public function getConversations($conversation_id)
    {
        $conversation = ExplorerMissionConversation::findOrFail($conversation_id);

        return $conversation;
    }

    /*************************
     * Missions
     ************************/

    public function getMissionProposition(Request $request, ExplorerMessengerService $explorerMessengerService)
    {
        $user = \Auth::user();
        try {
            //event(new ExplorerFile());
            return response($explorerMessengerService->getMissionProposition($user, $request->get('proposition_id')));
        } catch (\Exception $e) {
            response($e->getMessage(), 500);
        }
    }

    public function getMissionPropositionDrive(Request $request, ExplorerMessengerService $explorerMessengerService)
    {
        $user = \Auth::user();
        try {
            return response($explorerMessengerService->getMissionPropositionDrive($user, $request->get('proposition_id')));
        } catch (\Exception $e) {
            response($e->getMessage(), 500);
        }
    }

    public function postNewMissionProposition(Request $request, ExplorerMissionService $explorerMissionService)
    {
        $user = \Auth::user();
        $requestParams = $request->all()['params'];
        $validator = Validator::make($requestParams['mission_proposition']['mission'], [
            'name' => ['required'],
            'budget' => ['required'],
            'deadline'     => ['required'],
            'type' => ['required'],
            'description' => ['required'],
        ]);
        if($validator->fails()){
            return response()->json($validator->messages(), 400);
        }
        try {
            //event(new ExplorerMessage());
            event(new Notifications($requestParams['mission_proposition']));
            $proposition = $explorerMissionService->newMissionProposition($requestParams['mission_proposition']);
            return response($proposition);
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }
    }

    public function patchMissionProposition($mission_proposition_id, Request $request, ExplorerMissionService $explorerMissionService)
    {
        $user = \Auth::user();
        $requestParams = $request->all()['params'];

        try {
            switch ($requestParams['patch_type']) {
                case 'mission_proposition_cancel':
                    $explorerMissionService->cancelMissionProposition($mission_proposition_id, $user);
                    break;
                case 'mission_proposition_close':
                    $explorerMissionService->closeMissionProposition($mission_proposition_id, $user);
                    break;
            }
            return response('', 200);
        } catch (\Exception $exception) {
            return response($exception->getMessage(), 500);
        }
    }

    public function patchMission($mission_id, Request $request, ExplorerMissionService $explorerMissionService)
    {
        $user = \Auth::user();
        $requestParams = $request->all()['params'];
        try {
            switch ($requestParams['patch_type']) {
                case 'mission_full_cancel':
                    $explorerMissionService->cancelFullMission($mission_id, $user);
                    break;
            }
            return response('', 200);
        } catch (\Exception $exception) {
            return response($exception->getMessage(), 500);
        }
    }


    /*************************
     * Quotes
     ************************/

    public function postNewQuote($conversation_id, Request $request, ExplorerMessengerService $explorerMessengerService)
    {
        $user = \Auth::user();
        $requestParams = $request->all()['params'];
        $validator = Validator::make($requestParams['quote'], [
            'quoteItems' => ['required'],
            'price'     => ['required'],
            'deadline' => ['required'],
            'currency' => ['required'],
            'description' => [''],
        ]);
        if($validator->fails()){
            return response()->json($validator->messages(), 400);
        }
        try {
            //event(new ExplorerMessage());
            $explorerMessengerService->newPropositionQuote($conversation_id, $requestParams['quote']);
            return response('', 201);
        } catch (\Exception $e) {
            return response($e->getMessage(), 500);
        }
    }


}
