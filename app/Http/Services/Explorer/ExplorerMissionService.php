<?php

namespace App\Http\Services\Explorer;


use App\Enum\ExplorerMissionPropositionStatusEnum;
use App\Enum\ExplorerMissionQuoteStatusEnum;
use App\Enum\ExplorerMissionStatusEnum;
use App\Events\ExplorerConversation;
use App\Events\ExplorerMessage;
use App\Events\UpdateMissionStatusExplorer;
use App\Models\ExplorerDrive;
use App\Models\ExplorerMission;
use App\Models\ExplorerMissionConversation;
use App\Models\ExplorerMissionProposition;
use App\Models\ExplorerMissionQuote;
use App\Models\TaskType;
use App\User;
use Carbon\Carbon;
use DateTime;
use SendinBlue\Client\ApiException;
use App\Models\Task;
use App\Events\TaskEvent;

class ExplorerMissionService
{


    /**
     * @var ExplorerMessengerConversationService
     */
    private $explorerMessengerConversationService;

    public function __construct(ExplorerMessengerConversationService $explorerMessengerConversationService)
    {
        $this->explorerMessengerConversationService = $explorerMessengerConversationService;
    }

    /**
     * @param $missionParams
     * @return ExplorerMission
     */
    public function newMission($missionParams): ExplorerMission
    {
        $user = \Auth::user();
        $mission = new ExplorerMission();
        $mission->name = $missionParams['name'];
        $mission->budget = $missionParams['budget'];
        switch ($missionParams['deadline']) {
            case 'Dès maintenant':
                $mission->deadline = DateTime::createFromFormat('Y-m-d', Carbon::now()->add(24,'hours')->format('Y-m-d'))->format('Y-m-d');
                break;
            case 'Dans la semaine':
                $mission->deadline = DateTime::createFromFormat('Y-m-d', Carbon::now()->add(6,'days')->format('Y-m-d'))->format('Y-m-d');
                break;
            case 'Dans 2 semaines':
                $mission->deadline = DateTime::createFromFormat('Y-m-d', Carbon::now()->add(14,'days')->format('Y-m-d'))->format('Y-m-d');
                break;
            case 'Dans le mois':
                $mission->deadline = DateTime::createFromFormat('Y-m-d', Carbon::now()->add(24,'days')->format('Y-m-d'))->format('Y-m-d');
                break;
            default:
                $mission->deadline = $missionParams['deadline'];
                break;
        }
        //$mission->deadline = $missionParams['deadline'];
        $mission->type = $missionParams['type'];
        $mission->description = $missionParams['description'];
        $mission->created_by = $user->id;
        $mission->status = ExplorerMissionStatusEnum::OPEN;

        $mission->save();
        return $mission;
    }

    /**
     * @param $missionPropositionParams
     * @return ExplorerMissionProposition
     * @throws \SendinBlue\Client\ApiException
     */
    public function newMissionProposition($missionPropositionParams): ExplorerMissionProposition
    {
        $proposition = new ExplorerMissionProposition();
        $id = $missionPropositionParams['propose_to_user_id'];
        $clientId = \Auth::user()->id;
        $user = User::findOrFail($id);
        $client = User::findOrFail($clientId);
        $mission = $this->findOrCreateMission($missionPropositionParams['mission']);

        $proposition->id_mission = $mission->id;

        $proposition->client_id = \Auth::user()->id; //Only Client can propose mission
        $proposition->created_by = $proposition->client_id;
        $proposition->freelance_id = $missionPropositionParams['propose_to_user_id'];
        $proposition->status = ExplorerMissionPropositionStatusEnum::WAITING_QUOTE;

        //sendinblue mail after a new mission proposition
        $config = \SendinBlue\Client\Configuration::getDefaultConfiguration()->setApiKey('api-key', (env('SENDINBLUE_KEY')));
        $apiContact = new \SendinBlue\Client\Api\ContactsApi(
            new \GuzzleHttp\Client(),
            $config
        );
        $apiInstance = new \SendinBlue\Client\Api\TransactionalEmailsApi(
            new \GuzzleHttp\Client(),
            $config
        );
        $sendSmtpEmail = new \SendinBlue\Client\Model\SendSmtpEmail();
        $createContact = new \SendinBlue\Client\Model\CreateContact();
        $updateContact = new \SendinBlue\Client\Model\UpdateContact();
        $createContact['email'] = $user->email;
        $createContact['firstname'] = $user->name;
        $createContact['phone'] =$user->phone ?? false;
        $createContact['listIds'] = [7];
        $updateContact['listIds'] = [7];
        $limit = 50;
        $offset = 0;
        $exist = false;

        //get all contacts from sendinblue server
        $contacts=$apiContact->getContacts($limit, $offset);
        $contacts= $contacts["contacts"] ?? null;

        //if foreach contact we find an existing contacts $exist variable is update to true
        if ($contacts) {
            foreach($contacts as $is_contact){
                if (!empty($is_contact["email"]) && $is_contact["email"] == $user->email) {
                    $exist = true;
                }
            }
        }
        event(new ExplorerConversation());
        //persist data to DB
        $proposition->save();

        $drive = new ExplorerDrive();
        $drive->id_proposition = $proposition->id;
        $drive->save();

        $conversation = $this->explorerMessengerConversationService->createConversation($proposition);

        //checking if the contact already exist on sendinblue DB
        if(!$exist)
        {
            //create the contact on sendinblue DB
            $createContact['attributes'] = [
                "ID"    => $user->id,
                'LASTNAME' => $user->lastname,
                'EMAIL'=>$user->email,
                'FIRSTNAME'=>$user->firstname,
                'TASK_TYPE'=>$this->getTaskTypeFromId($proposition->explorerMission()->first()->type),
                'TASK_CREATED_BY'=>$client->lastname,
                'CONVERSATION_ID'=>$conversation->id,
            ];
            $apiContact->createContact($createContact);
        }else{
            //remove contact from sendinblue and recreate it if no error on the filling form
            $identifier = $user->email;
            $createContact['attributes'] = [
                "ID"    => $user->id,
                'LASTNAME' => $user->lastname,
                'EMAIL'=>$user->email,
                'FIRSTNAME'=>$user->firstname,
                'TASK_TYPE'=>$this->getTaskTypeFromId($proposition->explorerMission()->first()->type),
                'TASK_CREATED_BY'=>$client->lastname,
                'CONVERSATION_ID'=>$conversation->id,
            ];
            $apiContact->deleteContact($identifier);
            $apiContact->createContact($createContact);
        }
        return $proposition;
    }

    //tasktype name value
    public function getTaskTypeFromId($id)
    {
        $taskType = TaskType::findOrFail($id);
        if ($taskType) {
            return $taskType->name;
        }
        return false;
    }

    public function cancelMissionProposition($missionPropositionId, User $user)
    {
        $missionProposition = ExplorerMissionProposition::find($missionPropositionId);
        $quote = ExplorerMissionQuote::where('explorer_mission_quote.id_proposition','=',$missionPropositionId)->first();
        if ($this->checkUserIsMissionPropositionClient($missionProposition, $user)) {
            $this->updateMissionPropositionStatus($missionProposition, ExplorerMissionPropositionStatusEnum::CANCELED);
            if($quote !== null)
            {
                $this->updateStatusMissionCanceled($missionProposition, $user,ExplorerMissionQuoteStatusEnum::CANCELED);
            }
        }
        $this->explorerMessengerConversationService->addMissionCanceledSystemMessage($missionProposition->conversation);
    }
    public function quoteAddedMissionProposition(ExplorerMissionProposition $proposition)
    {
        $proposition->status = ExplorerMissionPropositionStatusEnum::WAITING_PAYMENT;
        $proposition->save();
    }

    public function quotePaidMissionProposition(ExplorerMissionProposition $proposition)
    {
        $quoting = ExplorerMissionQuote::where("id_proposition",$proposition->id)->first();
        $proposition->status = ExplorerMissionPropositionStatusEnum::WAITING_WORK;
        $proposition->save();
        $this->explorerMessengerConversationService->addQuotePaidSystemMessage($proposition->conversation,$proposition->client_id,$quoting);
        if($quoting->freelanceSubscription !== 1)
        {
            $this->explorerMessengerConversationService->addQuotePaidSystemMessageDouble($proposition->conversation,$proposition->client_id,$quoting);
        }
    }

    public function workFinishedMissionProposition(ExplorerMissionProposition $proposition)
    {
        $now = Carbon::now()->format('Y-m-d 00:00:00');
        $proposed = ExplorerMissionProposition::with('explorerMission')->where('status',ExplorerMissionPropositionStatusEnum::INFO_BEEN_FILLED)->get();

        foreach ($proposed as $propositionMessage)
        {
            $deadline = $propositionMessage->explorerMission->get();
            foreach ($deadline as $systemDeadline)
            {
                if($systemDeadline->deadline === $now){
                    $proposition->status = ExplorerMissionPropositionStatusEnum::FINISHED_WAITING_CLOSING;
                    $proposition->save();
                    if($proposition->status ==  ExplorerMissionPropositionStatusEnum::FINISHED_WAITING_CLOSING)
                    {
                        $this->explorerMessengerConversationService->addMissionFinishedSystemMessage($proposition->conversation);
                    }
                }
            }
            return false;
        }
    }
    public function freelanceInformationFilledQuote(ExplorerMissionProposition $proposition)
    {
        $quoting = ExplorerMissionQuote::where("id_proposition",$proposition->id)->first();
        $proposition->status = ExplorerMissionPropositionStatusEnum::INFO_BEEN_FILLED;
        $proposition->save();
        $this->explorerMessengerConversationService->addMissionFreelanceFilledInformationSystemMessage($proposition->conversation,$user=null,$quoting);
    }

    public function closeMissionProposition($missionPropositionId, User $user)
    {
        $missionProposition = ExplorerMissionProposition::find($missionPropositionId);
        $quote = ExplorerMissionQuote::where('id_proposition',$missionPropositionId)->first();
        if($quote->freelanceSubscription !== 0)
        {
            if ($this->checkUserIsMissionPropositionClient($missionProposition, $user)) {
                $this->updateMissionPropositionStatus($missionProposition, ExplorerMissionPropositionStatusEnum::CLOSED);
            }
            if($missionProposition->status === 'closed')
            {
                $this->explorerMessengerConversationService->closeMissionMessage($missionProposition->conversation);
                $propositionId = $missionProposition->id;
                $proposition = ExplorerMissionProposition::findOrFail($propositionId);
                if ($proposition) {
                    $this->explorerMessengerConversationService->notifyMissionClosed($proposition);
                }
            }
        }
        return false;
    }
    public function updateStatusMissionCanceled(ExplorerMissionProposition $proposition , User $user ,$status)
    {
        if ($this->checkUserIsMissionPropositionClient($proposition, $user))
        {
            $quote = ExplorerMissionQuote::where('explorer_mission_quote.id_proposition','=',$proposition->id)->first();
            $quote->status = $status;
            event(new ExplorerMessage());
            $quote->save();
        }
    }
    public function cancelFullMission($missionId, User $user)
    {
        $mission = ExplorerMission::find($missionId);

        $missionPropositions = ExplorerMissionProposition::where('id_mission', "=", $missionId)->get();
        $this->cancelMission($mission);
        foreach ($missionPropositions as $missionProposition) {
            $this->cancelMissionProposition($missionProposition->id, $user);
        }
    }

    private function findOrCreateMission($missionParams)
    {
        if (isset($missionParams['id']) && $missionParams['id'] !== null) {
            $mission = ExplorerMission::find($missionParams['id']);

            if ($mission !== null) {
                return $mission;
            }
        }

        return $this->newMission($missionParams);
    }

    public function updateMissionPropositionStatus(ExplorerMissionProposition $missionProposition, $status)
    {
        $missionProposition->status = $status;
        $missionProposition->save();
        if ($status == ExplorerMissionPropositionStatusEnum::CLOSED) {
            $projectId = $missionProposition->kolab_project_id;
            if ($projectId) {
                $task = Task::where('project_id', $projectId)->first();
                if ($task) {
                    $task->status = strtoupper($status);
                    $task->closed_at = new Datetime();
                    $task->save();
                    event(new TaskEvent());
                }
            }
        }
    }

    private function checkUserIsMissionPropositionClient(ExplorerMissionProposition $explorerMissionProposition, User $user): bool
    {
        return $explorerMissionProposition->client_id === $user->id;
    }

    private function cancelMission(ExplorerMission $explorerMission)
    {
        $explorerMission->status = ExplorerMissionStatusEnum::CANCELED;
        $explorerMission->save();
    }
}
