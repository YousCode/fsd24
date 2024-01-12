<?php

namespace App\Http\Helpers;

use App\Events\ExplorerMessage;
use App\Http\Controllers\Admin\ExplorerController;
use App\Models\ExplorerMissionConversation;
use App\Models\ExplorerMissionConversationMessage;
use App\Models\ExplorerMissionProposition;
use App\Models\Notification;
use App\Models\Appointment;
use App\Models\Project;
use App\Models\Task;
use App\User;


use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Class ClientHelper
 * @package App\Http\Helpers
 */
class NotificationHelper
{

    public function get($client,$freelance)
    {
        // return collect($this->getTasksNotifications())
        //     ->concat($this->getProjectsNotifications())
        //     ->concat($this->getTalentsNotifications())
        //     ->concat($this->getAppointmentsNotifications())
        //     ->sortByDesc('datetime')->slice(0, 10)->values();

        $notifications = Notification::whereRaw('id IN (select MAX(id) FROM notifications GROUP BY element_id, element_type)')
            ->orderBy('id', 'DESC')
            ->get();
        if(!empty($client) && !empty($freelance))
        {
            return $this->getMessageNotifications($client,$freelance);
        }else{
            return [];
        }
        return $notifications;
    }

    public function getByElemId($element_id)
    {
        $notification = Notification::where("element_id", '=', $element_id)->where("message", '=', 'Voulez vous clôturer cette tâche ? (Elle le sera automatiquement à la fin de la journée)')->get();
        return $notification;
    }

    public function save($message, $user_id = null, $element_type = null, $element_id = null)
    {
        $notification = new Notification;

        $notification->message = $message;
        $notification->user_id = $user_id;
        $notification->element_type = $element_type;
        $notification->element_id = $element_id;

        $notification->save();

        return $notification;
    }

    /**
     * Retrieve last ten tasks notifications
     * @return array
     */
    private function getTasksNotifications()
    {
        $tasks = Task::query()
            ->select([
                'tasks.id as id',
                DB::raw('coalesce(updated_at, created_at) as datetime, coalesce(updated_by, created_by) as user')
            ])
            ->orderBy('datetime', 'desc')
            ->limit(10)
            ->get();

        $notifications = [];

        foreach ($tasks as $task) {
            $notifications[] = [
                'type' => 'task',
                'user' => $task->user,
                'datetime' => $task->datetime,
                'content' => 'Task Id -> ' . $task->id
            ];
        }
        return $notifications;
    }
    private function getMessageNotifications($client,$freelance)
    {
        $user = \Auth::user();
        $now = new \DateTime();
        $messages = ExplorerMissionConversationMessage::query()->select(['explorer_mission_conversation_messages.id as id',
            DB::raw('coalesce(updated_at, created_at) as datetime,coalesce(created_by) as fromuser,coalesce(mark_as_read) as mark,coalesce(message) as messages,coalesce(id_conversation) as convId')])
            ->where('mark_as_read','=',0)
            ->where('created_by','<>',$user)
            ->orderBy('datetime', 'desc')
            ->get();
        $notifications = [];
        foreach ($messages as $message) {
            $notifications[] = [
                'type' => 'message',
                'datetime' => $message->datetime,
                'id'=> $message->id,
                'content' => 'Message Content -> ' . $message->messages,
                'createdBy'=> $message->fromuser,
                'freelance'=>$freelance,
                'client'=>$client,
                'id_conversation'=>$message->convId,
                'mark_as_read'=>$message->mark
            ];
        }
        return $notifications;

    }

    /**
     * Retrieve last ten projects notifications
     * @return array
     */
    private function getProjectsNotifications()
    {
        $projects = Project::query()
            ->select(['projects.id as id', DB::raw('coalesce(updated_at, created_at) as datetime, coalesce(updated_by, created_by) as user')])
            ->orderBy('datetime', 'desc')
            ->limit(10)
            ->get();

        $notifications = [];

        foreach ($projects as $project) {
            $notifications[] = [
                'type' => 'project',
                'user' => $project->user,
                'datetime' => $project->datetime,
                'content' => 'Project Id -> ' . $project->id
            ];
        }
        return $notifications;
    }

    /**
     * Retrieve last ten talents notifications
     * @return array
     */
    private function getTalentsNotifications()
    {
        $talents = User::query()
            ->select(['users.id as id', DB::raw('coalesce(updated_at, created_at) as datetime, coalesce(updated_by, created_by) as user')])
            ->orderBy('datetime', 'desc')
            ->limit(10)
            ->get();

        $notifications = [];

        foreach ($talents as $talent) {
            $notifications[] = [
                'type' => 'talent',
                'user' => $talent->user,
                'datetime' => $talent->datetime,
                'content' => 'Talent Id -> ' . $talent->id
            ];
        }
        return $notifications;
    }

    /**
     * Retrieve last ten appointments notifications
     * @return array
     */
    private function getAppointmentsNotifications()
    {
        $appointments = Appointment::query()
            ->select(['appointments.id as id',  DB::raw('coalesce(updated_at, created_at) as datetime, coalesce(updated_by, created_by) as user')])
            ->orderBy('datetime', 'desc')
            ->limit(10)
            ->get();
        $notifications = [];

        foreach ($appointments as $appointment) {
            $notifications[] = [
                'type' => 'appointment',
                'user' => $appointment->user,
                'datetime' => $appointment->datetime,
                'content' => 'Appointment Id -> ' . $appointment->id
            ];
        }
        return $notifications;
    }

    public function getUnreadMessages($propositionId, $userId, $unreadConversationList = false)
    {
        $unreadMessages = 0;
        $conversationId = false;
        $unreadConversationId = [];
        $proposition = ExplorerMissionProposition::findOrFail($propositionId);
        if ($proposition) {
            $conversations = $proposition->conversation()->get();
            foreach ($conversations as $conversation) {
                if ($conversation->id) {
                    $conversationId = $conversation->id;
                }
            }
            $messages = DB::table('explorer_mission_conversation_messages')
                ->where('explorer_mission_conversation_messages.id_conversation',$conversationId)
                ->get();
            }
                if ($unreadConversationList) {
                    foreach ($messages as $message) {
                        $markAsRead = json_decode($message->mark_as_read);
                        if (gettype($markAsRead) == 'object' && $userId && !$markAsRead->{$userId}) {
                            $unreadConversationId[$message->id_conversation] = $message->id;
                        }
                    }
                } else {
                    foreach ($messages as $message) {
                        $markAsRead = json_decode($message->mark_as_read);
                        if (gettype($markAsRead) == 'object' && $userId && $markAsRead->{$userId} == 0) {
                            $unreadMessages++;
                        }
                    }
                }
        if (!$unreadConversationList) {
            return $unreadMessages;
        } else {
            return $unreadConversationId;
        }
    }
}
