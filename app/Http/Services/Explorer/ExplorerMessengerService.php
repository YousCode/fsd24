<?php

namespace App\Http\Services\Explorer;

use App\Enum\ExplorerMissionMessagesTypesEnum;
use App\Enum\ExplorerMissionPropositionStatusEnum;
use App\Events\ExplorerConversation;
use App\Events\ExplorerFile;
use App\Events\ExplorerMessage;
use App\Events\MessageAdded;
use App\Events\Notifications;
use App\Events\UpdateMissionStatusExplorer;
use App\Http\Helpers\NotificationHelper;
use App\Models\ExplorerMissionConversation;
use App\Models\ExplorerMissionConversationMessage;
use App\Models\ExplorerMissionProposition;
use App\Models\ExplorerMissionQuote;
use App\Models\Project;
use App\Notifications\NewQuote;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Events\LinkAdded;
use App\Notifications\AddLink;
use Illuminate\Support\Facades\Log;

class ExplorerMessengerService
{
    /**
     * @var ExplorerQuoteService
     */
    private $explorerQuoteService;

    /**
     * @var ExplorerMissionService
     */
    private $explorerMissionService;

    /**
     * @var ExplorerDriveService
     */
    private $explorerDriveService;

    /**
     * @var ExplorerMessengerConversationService
     */
    private $explorerMessengerConversationService;
    private $helpers;

    private $kolabProjectId;
    private $kolabProjectOwnerId;
    private $kolabProjectOwner;

    public function __construct(ExplorerQuoteService $explorerQuoteService, ExplorerMissionService $explorerMissionService, ExplorerDriveService $explorerDriveService, ExplorerMessengerConversationService $explorerMessengerConversationService)
    {
        $this->explorerQuoteService = $explorerQuoteService;
        $this->explorerMissionService = $explorerMissionService;
        $this->explorerDriveService = $explorerDriveService;
        $this->explorerMessengerConversationService = $explorerMessengerConversationService;

        $this->helpers['notification'] = new NotificationHelper();
        $this->kolabProjectId = false;
        $this->kolabProjectOwnerId = false;
        $this->kolabProjectOwner = false;
    }


    ///////////////////////////////
    /// PUBLIC
    ///////////////////////////////

    //CONVERSATION LIST

    /**
     * @param User $user
     * @return array
     */
    public function getUserConversationsList(User $user): array
    {
        $userPropositionList = $this->getUserMissionPropositionsList($user);
        $conversationList = [];
        $unread = $this->unreadConversation($user->id);
        foreach ($userPropositionList as $userProposition) {
            $conversation = $userProposition->conversation;
            //$this->explorerMessengerConversationService->updateUserLastCheckDate($conversation, $user);
            $conversationList[] = $conversation;
        }
        usort($conversationList, [$this, 'sortConv']);
        return $conversationList;
    }

    private function unreadConversation(int $user)
    {
        $messages = ExplorerMissionConversationMessage::query()->select(['explorer_mission_conversation_messages.id as id',
            DB::raw('coalesce(created_by) as fromuser,coalesce(mark_as_read) as mark,coalesce(id_conversation) as convId')])
            ->groupBy('id_conversation')
            ->selectRaw('id_conversation, COUNT(id) as count')
            ->where('mark_as_read','=',0)
            ->where('created_by','<>',$user)
            ->get()
            ->pluck('convId','convId');
        return $messages;
    }
    /**
     * @throws Exception
     */
    function sortConv($a, $b): int
    {
        if(isset($a['lastMessage']['updated_at']) && isset($b['lastMessage']['updated_at']))
        {
            $a = new \DateTime($a['lastMessage']['updated_at']);
            $b = new \DateTime($b['lastMessage']['updated_at']);
        }
        if(isset($a) && isset($b))
        {
            if ($a == $b) {
                return 0;
            }
        }
        return ($a > $b) ? -1 : 1;
    }

//CONVERSATION
    /**
     * @throws Exception
     */
    public function getConversationMessages(User $user, $conversationId, $fromProject = false)
    {
        $conversation = ExplorerMissionConversation::with('proposition')->findOrFail($conversationId);
        $proposition = $conversation->proposition;

        if ($fromProject || $this->checkUserPropositionAccess($proposition, $user)) {

            return ExplorerMissionConversationMessage::where('id_conversation', $conversationId)
                ->orderBy('created_at', 'ASC')->get()->groupBy(function ($date) {
                    return Carbon::parse($date->created_at)->format('Y-m-d');
                });
        } else {
            throw new Exception("User Doesn't have access to the conversation");
        }
    }

    /**
     * @param $conversationId
     * @param $message
     * @return bool
     */
    public function newUserMessage($conversationId, $params, $user = null)
    {
        $conversation = ExplorerMissionConversation::find($conversationId);
        $date = new \DateTime();
        $dateToSend = Carbon::parse($conversation->proposition->mission->deadline)->format('Y-m-d H:i:s');
        $message = $params['message'] ?? false;
        $contactId = $params['contactId'] ?? false;
        $links = $this->searchLinks($message);
        if (count($links) <= 0) {
            if ($user && $conversation->proposition->freelance_id === $user->id && $dateToSend === $date->format('Y-m-d 00:00:00')) {
                //event(new ExplorerMessage());
                $this->explorerMissionService->workFinishedMissionProposition($conversation->proposition);
            }
            $contactId = (!$user && $contactId) ? $contactId : false;
            return $this->explorerMessengerConversationService->newMessage($conversationId, ExplorerMissionMessagesTypesEnum::USER_MEDIA, $message, $user, $contactId);
        } else {

            $drive = $conversation->proposition->drive;
            foreach ($links as $link) {
                $driveLink = $this->explorerDriveService->addLink($drive, $link);
                $this->explorerMessengerConversationService->driveLinkMessage($conversationId, $driveLink, $link, $user);

                if ($conversation->proposition->kolab_project_id && $conversation->proposition->kolab_project_id > 0) {
                    $this->kolabProjectId = $conversation->proposition->kolab_project_id;
                    $this->kolabProject = Project::findOrFail($this->kolabProjectId);
                    if ($this->kolabProject && $this->kolabProject->created_by && $this->kolabProject->created_by > 0) {
                        $this->kolabProjectOwnerId = $this->kolabProject->created_by;
                        $this->kolabProjectOwner = User::findOrFail($this->kolabProjectOwnerId);
                        if ($this->kolabProjectOwner && $this->kolabProjectOwner->id != $user->id) {
                            $this->kolabProjectOwner->notify(new AddLink($this->kolabProject, $driveLink, $user->name));
                        }
                    }
                }
            }
            event(new LinkAdded());

            //system message for freelance finished mission
            if ($user && $conversation->proposition->freelance_id === $user->id && $dateToSend === $date->format('Y-m-d 00:00:00')) {
                $this->explorerMissionService->workFinishedMissionProposition($conversation->proposition);
            }
            return true;
        }
    }
    public function allNewMessageConversation(User $user)
    {
        $conversation = $this->getUserConversationsList($user);
        $conversationList = [];
        foreach ($conversation as $conversationUnread) {
            $propositionId = $conversationUnread->id_proposition;
            $unread = $this->helpers['notification']->getUnreadMessages($propositionId, $user->id, true);
            $conversationList[$conversationUnread->id] = 0;
            if(isset($unread[$conversationUnread->id])){
                $conversationList[$conversationUnread->id] = $conversationUnread->id;
            }
        }
        return $conversationList;
        //$conversationList = ExplorerMissionConversationMessage::with('conversation')->where('mark_as_read', '=', 0)->get();
        //return $conversationList->toArray();
    }
    public function newMessageMarkAsRead($conversationId)
    {
        $user = \Auth::user();
        $conversation = ExplorerMissionConversation::findOrFail($conversationId);
        if($conversation) {
            $messages = $conversation->messages()->get();
            if($messages){
                foreach ($messages as $message) {
                    $markAsRead = json_decode($message->mark_as_read);
                    if (gettype($markAsRead) == "object") {
                        if (isset($markAsRead->{$user->id}) && !$markAsRead->{$user->id}) {
                            $markAsRead->{$user->id} = 1;
                            $message->mark_as_read = json_encode($markAsRead);
                            $message->save();
                        }
                    }
                }
            }
        }
    }


    /**
     * @param $conversationId
     * @param $message
     * @return bool
     */
    public function newProjectDrive($conversationId, $params, $user = null): bool
    {
        $message = $params['message'] ?? '';
        $contactId = $params['contactId'] ?? false;
        $isShared = $params['isShared'] ?? false;
        $conversation = ExplorerMissionConversation::find($conversationId);
        $links = $this->searchLinks($message);
        $drive = $conversation->proposition->drive;

        if (!empty($links)) {
            foreach ($links as $link) {
                $driveLink = $this->explorerDriveService->addLink($drive, $link, $isShared, $contactId);
                $this->explorerMessengerConversationService->driveLinkMessage($conversationId, $driveLink, $link, $user, $isShared, $contactId);

                if ($conversation->proposition->kolab_project_id && $conversation->proposition->kolab_project_id > 0) {
                    $this->kolabProjectId = $conversation->proposition->kolab_project_id;
                    $this->kolabProject = Project::findOrFail($this->kolabProjectId);
                    if ($this->kolabProject && $this->kolabProject->created_by && $this->kolabProject->created_by > 0) {
                        $this->kolabProjectOwnerId = $this->kolabProject->created_by;
                        $this->kolabProjectOwner = User::findOrFail($this->kolabProjectOwnerId);
                        if ($this->kolabProjectOwner) {
                            $linkOwner = '';
                            if ($contactId && $isShared) {
                                $contact = DB::table('contacts')->where('id', $contactId)->first();
                                $linkOwner = $contact->email ?? '';
                            } elseif ($user && $user->id != $this->kolabProjectOwnerId) {
                                $linkOwner = $user->name ?? '';
                            }
                            if ($linkOwner != '') {
                                $this->kolabProjectOwner->notify(new AddLink($this->kolabProject, $driveLink, $linkOwner));
                            }
                        }
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }

// PROPOSITION

    /**
     * @param User $user
     * @param $propositionId
     * @return ExplorerMissionProposition|ExplorerMissionProposition[]|Collection|Model|null
     * @throws Exception
     */
    public
    function getMissionProposition(User $user, $propositionId)
    {
        $proposition = ExplorerMissionProposition::find($propositionId);

        if ($this->checkUserPropositionAccess($proposition, $user)) {
            return $proposition;
        } else {
            throw new Exception("User Doesn't have access to the conversation");
        }
    }

// DRIVE

    /**
     * @throws Exception
     */
    public
    function getMissionPropositionDrive(User $user, $propositionId)
    {
        $proposition = $this->getMissionProposition($user, $propositionId);
        event(new ExplorerFile());
        return $proposition->drive()->getResults();
    }

// QUOTE

    /**
     * @param $conversationId
     * @param $quoteInfos
     * @return bool
     * @throws Exception
     */
    public function newPropositionQuote($conversationId, $quoteInfos): bool
    {
        $conversation = ExplorerMissionConversation::find($conversationId);
        $proposition = $conversation->proposition;
        $user = User::findOrFail($proposition->client_id);
        if (!$this->checkUserHasPropositionFreelanceAccess($proposition, \Auth::user())) {
            throw new Exception('Access Unauthorized for User');
        }
        //event(new ExplorerMessage());
        $quote = $this->explorerQuoteService->newQuote($quoteInfos, $proposition);
        $this->explorerMissionService->quoteAddedMissionProposition($proposition);
        //$proposition->freelance->notify(new NewQuote($conversationId));
        $user->notify(new NewQuote($conversationId));

        return $this->explorerMessengerConversationService->newConversationMessage($conversation->id, ExplorerMissionMessagesTypesEnum::USER_QUOTE, '',$conversation->proposition->freelance_id ,$quote);
    }


///////////////////////////////
/// PRIVATE
///////////////////////////////
    /**
     * @param User $user
     * @return Builder[]|ExplorerMissionProposition[]|Collection
     */
    private function getUserMissionPropositionsList(User $user)
    {
        return ExplorerMissionProposition::select("id","client_id","freelance_id","status","id_mission")->where('client_id', $user->id)
            ->orWhere('freelance_id', $user->id)->get();
        //return ExplorerMissionProposition::where('client_id', $user->id)->orWhere('freelance_id', $user->id)->get();
    }

    /**
     * Check if user is part of the proposition has client or as freelancer
     * @param ExplorerMissionProposition $explorerMissionProposition
     * @param User $user
     * @return bool
     */
    private function checkUserPropositionAccess(ExplorerMissionProposition $explorerMissionProposition, User $user): bool
    {
        return $explorerMissionProposition->freelance_id === $user->id || $explorerMissionProposition->client_id === $user->id;
    }

    /**
     * @param ExplorerMissionProposition $explorerMissionProposition
     * @param User $user
     * @return bool
     */
    private function checkUserHasPropositionFreelanceAccess(ExplorerMissionProposition $explorerMissionProposition, User $user): bool
    {
        return $explorerMissionProposition->freelance_id == $user->id;
    }

    /**
     * @param ExplorerMissionProposition $explorerMissionProposition
     * @param string $status
     * @return bool
     */
    private function updatePropositionStatus(ExplorerMissionProposition $explorerMissionProposition, string $status = ExplorerMissionPropositionStatusEnum::WAITING_QUOTE): bool
    {
        $explorerMissionProposition->status = $status;
        return $explorerMissionProposition->save();
    }

    /**
     * Search and return link in message
     * @param $text
     * @return array|mixed
     */
    private function searchLinks($text)
    {
        $pattern = '~[a-zA-Z]+://\S+~';

        if ($num_found = preg_match_all($pattern, $text, $out)) {
            return ($out[0]);
        }

        return [];
    }
}
