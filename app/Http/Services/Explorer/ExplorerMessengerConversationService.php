<?php

namespace App\Http\Services\Explorer;

use App\Enum\ExplorerMissionMessagesTypesEnum;
use App\Events\ExplorerFile;
use App\Events\ExplorerMessage;
use App\Events\MessageAdded;
use App\Events\Notifications;
use App\Events\UpdateMissionStatusExplorer;
use App\Http\Controllers\Admin\DashboardController;
use App\Models\ExplorerDriveLink;
use App\Models\ExplorerMissionConversation;
use App\Models\ExplorerMissionConversationMessage;
use App\Models\ExplorerMissionProposition;
use App\Models\ExplorerMissionQuote;
use App\Models\ExplorerMission;
use App\Notifications\ExplorerClientCloseMission;
use App\User;
use Carbon\Carbon;
use Exception;

class
ExplorerMessengerConversationService
{
    public function createConversation(ExplorerMissionProposition $missionProposition): ExplorerMissionConversation
    {
        $conversation = new ExplorerMissionConversation();
        $user = $missionProposition->created_by;

        $conversation->id_proposition = $missionProposition->id;
        $conversation->created_by = $missionProposition->created_by;

        $conversation->save();
        $this->initConversation($conversation,$missionProposition);
        return $conversation;
    }

    /**
     * @throws Exception
     */
    public function updateUserLastCheckDate(ExplorerMissionConversation $explorerMissionConversation, User $user)
    {
        $this->checkUserAccess($explorerMissionConversation, $user);
        if ($user->hasRole('talent')) {
            $explorerMissionConversation->last_freelancer_check = new \DateTime();
        } else {
            $explorerMissionConversation->last_client_check = new \DateTime();
        }

        $explorerMissionConversation->save();
    }

    /**
     * Init new conversation
     * @param ExplorerMissionConversation $conversation
     * @return void
     */
    private function initConversation(ExplorerMissionConversation $conversation, $user)
    {

        $this->addMissionSummaryMessage($conversation,$user->client_id);
        $this->addCreateQuoteSystemMessage($conversation,$user->client_id);
    }

    /**
     * Add Mission summary system message into conversation
     * @param ExplorerMissionConversation $conversation
     * @return void
     */
    private function addMissionSummaryMessage(ExplorerMissionConversation $conversation, $user)
    {
        $this->newConversationMessage($conversation->id, ExplorerMissionMessagesTypesEnum::USER_MISSION_PROPOSAL,'',$user,$quote=null);
    }

    /**
     * @param $conversationId
     * @param $messageType
     * @param string|null $message
     * @param ExplorerMissionQuote|null $quote
     * @return bool
     */
    public function newConversationMessage($conversationId, $messageType, ?string $message = '',$user, ExplorerMissionQuote $quote = null): bool
    {
        $conversationMessage = new ExplorerMissionConversationMessage();
        $conversation = ExplorerMissionConversation::findOrFail($conversationId);
        $markAsRead = [0 => 0, 1 => 0];
        $readFreelance = 0;
        $readClient = 0;
        $propositionId = $conversation->id_proposition;
        $props = ExplorerMissionProposition::findOrFail($propositionId);
        if($props)
        {
            $clientId = $props->client_id;
            $freelanceId = $props->freelance_id;
            if ($clientId && $clientId > 0 && $freelanceId && $freelanceId > 0) {
                $markAsRead = [$clientId => $readClient, $freelanceId => $readFreelance];
            }
        }

        $conversationMessage->message = $message;
        $conversationMessage->id_conversation = $conversationId;
        $conversationMessage->type = $messageType;
        $conversationMessage->mark_as_read = json_encode($markAsRead);
        if($user)
        {
            $conversationMessage->created_by = $user;
        }
        if ($quote !== null) {
            $conversationMessage->id_quote = $quote->id;
        }
        //checking if type has already been send
        if($conversationMessage->id_conversation){
            $systemMessage = $conversation->getSystemMessagesAttribute();
            if($systemMessage)
            {
                if($systemMessage->contains('type',$messageType))
                {
                    return false;
                }
                event(new ExplorerMessage());
                //event((new Notifications($message)));
                return $conversationMessage->save();
            }
        }
    }

    public function newMessage($conversationId, $messageType, $message, $user = null, $contactId = false)
    {
        $conversationMessage = new ExplorerMissionConversationMessage();
        $conversation = ExplorerMissionConversation::findOrFail($conversationId);
        $propositionId = $conversation->id_proposition;
        $markAsRead = [0 => 0, 1 => 0];
        $readFreelance = 0;
        $readClient = 0;
        if ($propositionId && $propositionId > 0) {
            $proposition = ExplorerMissionProposition::findOrFail($propositionId);
            if ($proposition) {
                $clientId = $proposition->client_id;
                $freelanceId = $proposition->freelance_id;
                if ($user && $clientId == $user->id) {
                    $readClient = 1;
                } else if ($user && $freelanceId == $user->id) {
                    $readFreelance = 1;
                }
                if ($clientId && $clientId > 0 && $freelanceId && $freelanceId > 0) {
                    $markAsRead = [$clientId => $readClient, $freelanceId => $readFreelance];
                }
            }
        }
        $conversationMessage->mark_as_read = json_encode($markAsRead);
        $conversationMessage->message = $message;
        $conversationMessage->id_conversation = $conversationId;
        $conversationMessage->type = $messageType;
        if ($user) {
            $conversationMessage->created_by = $user->id;
        }
        if (!$user && $contactId) {
            $conversationMessage->contact_id = $contactId;
        }
        if($conversationMessage->id_conversation){
            $systemMessage = $conversation->getSystemMessagesAttribute();
            if($systemMessage->contains($messageType))
            {
                return false;
            }
        }

        //event(new ExplorerMessage());
        //event((new Notifications($message)));
        $conversationMessage->save();

        return $conversationMessage;
    }

    public function cancelMissionPropositionMessage(ExplorerMissionConversation $missionConversation,$user=null)
    {
        $this->newConversationMessage($missionConversation->id, ExplorerMissionMessagesTypesEnum::SYSTEM_MISSION_CANCELED,'',$user=null,$quote=null);
    }

    public function closeMissionMessage(ExplorerMissionConversation $missionConversation,$user=null)
    {
        $this->newConversationMessage($missionConversation->id, ExplorerMissionMessagesTypesEnum::SYSTEM_MISSION_CLOSED,'',$user=null,$quote=null);
    }

    public function notifyMissionClosed(ExplorerMissionProposition $proposition) {
        if ($proposition) {
            $clientId = $proposition->client_id;
            $freelanceId = $proposition->freelance_id;
            $client = User::findOrFail($clientId);
            $freelance = User::findOrFail($freelanceId);
            if ($client && $freelance) {
                $missionId = $proposition->id_mission;
                $mission = ExplorerMission::findOrFail($missionId);
                $clientName = $client->name ?? '';
                $missionName = $mission->name ?? '';
                $conversationId = $proposition->conversationId ?? false;
                $quoteId = $proposition->quoteId ?? false;
                $quote = ExplorerMissionQuote::findOrFail($quoteId);
                $quoteTotalHt = $quote->price - $quote->service_fee ?? 0;
                $freelanceSubscription = $quote->freelanceSubscription ?? false;
                if ($freelanceSubscription) {
                    $freelance->notify(new ExplorerClientCloseMission($clientName, $missionName, $conversationId, $quoteTotalHt, $freelanceSubscription));
                }
            }
        }
    }

    /**
     * Add a message linked to à Link
     * @param $conversationId
     * @param ExplorerDriveLink $explorerDriveLink
     * @return bool
     */
    public function driveLinkMessage($conversationId, ExplorerDriveLink $explorerDriveLink, $link = null, $user = null, $isShared = false, $contactId = false): bool
    {
        $conversationMessage = new ExplorerMissionConversationMessage();

        $conversationMessage->message = ($link) ? $link : '';
        $conversationMessage->id_conversation = $conversationId;
        $conversationMessage->type = ExplorerMissionMessagesTypesEnum::USER_DRIVE_LINK;
        if ($user) {
            $conversationMessage->created_by = $user->id;
        } else if ($isShared && $contactId) {
            $conversationMessage->contact_id = $contactId;
        }
        $conversationMessage->id_drive_link = $explorerDriveLink->id;
        event(new ExplorerFile());
        return $conversationMessage->save();
    }

    public function addCreateQuoteSystemMessage(ExplorerMissionConversation $conversation,$user)
    {
        $this->newConversationMessage($conversation->id, ExplorerMissionMessagesTypesEnum::SYSTEM_QUOTE_CREATION,'',$user,$quote=null);
    }

    public function addQuotePaidSystemMessage(ExplorerMissionConversation $conversation,$user,$quote)
    {
        $this->newConversationMessage($conversation->id, ExplorerMissionMessagesTypesEnum::SYSTEM_MISSION_QUOTE_PAID,'',$user,$quote);
    }

    public function addQuotePaidSystemMessageDouble(ExplorerMissionConversation $conversation,$user,$quote)
    {
        $this->newConversationMessage($conversation->id, ExplorerMissionMessagesTypesEnum::SYSTEM_MISSION_QUOTE_PAID_NOT_FILLED,'',$user,$quote);
    }

    public function addMissionFinishedSystemMessage(ExplorerMissionConversation $conversation,$user=null)
    {
        $this->newConversationMessage($conversation->id, ExplorerMissionMessagesTypesEnum::SYSTEM_MISSION_FINISHED,'',$user=null,$quote=null);
    }
    public function addMissionFreelanceFilledInformationSystemMessage(ExplorerMissionConversation $conversation,$user=null,$quote)
    {
        $this->newConversationMessage($conversation->id, ExplorerMissionMessagesTypesEnum::SYSTEM_INFO_FILLED,'',$user=null,$quote);
    }

    public function addMissionCanceledSystemMessage(ExplorerMissionConversation $conversation,$user=null)
    {
        $this->newConversationMessage($conversation->id, ExplorerMissionMessagesTypesEnum::SYSTEM_MISSION_CANCELED,'',$quote=null,$user=null);
    }

    public function checkUserAccess(ExplorerMissionConversation $explorerMissionConversation, User $user)
    {
        $proposition = $explorerMissionConversation->proposition;
        if ($proposition->client_id != $user->id && $proposition->freelance_id != $user->id) {
            throw new Exception("Access denied to this conversation");
        }
    }
}
