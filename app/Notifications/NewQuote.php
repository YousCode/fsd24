<?php

namespace App\Notifications;

use App\Channels\SendinblueChannel;
use App\Models\ExplorerMissionConversation;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewQuote extends Notification
{
    use Queueable;

    protected $conversationId;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(int $conversationId)
    {
        $this->conversationId = $conversationId;
    }

    public function via($notifiable)
    {
        return [SendinblueChannel::class];
    }

    public function toSendinblue(User $user)
    {
        $conversation = ExplorerMissionConversation::find($this->conversationId);
        $proposition = $conversation->proposition;
        $freelance = $proposition->freelance;

        $receivers = [$user->email];
        $params = [
            'FREELANCE_NAME'=> $freelance->firstname && $freelance->lastname ? "{$freelance->firstname} {$freelance->lastname}" : $freelance->name,
            'PROJECT_TITLE'=> $proposition->explorerMission->name,
            'CONVERSATION_ID'=> $this->conversationId,
            'MISSION_STATUS' => $proposition->explorerMission->status
        ];

        return [
            13, // template_id
            $receivers, // receivers
            $params, // params
            null // attachment
        ];
    }
}
