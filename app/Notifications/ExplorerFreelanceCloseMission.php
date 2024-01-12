<?php

namespace App\Notifications;

use App\Channels\SendinblueChannel;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ExplorerFreelanceCloseMission extends Notification
{
    use Queueable;

    private $freelanceName;
    private $clientName;
    private $missionName;
    private $conversationId;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($freelanceName, $clientName, $missionName, $conversationId)
    {
        $this->freelanceName = $freelanceName ?? '';
        $this->missionName = $missionName ?? '';
        $this->clientName = $clientName ?? '';
        $this->conversationId = $conversationId ?? false;
    }

    public function via($notifiable)
    {
        return [SendinblueChannel::class];
    }

    public function toSendinblue(User $user)
    {
        $receivers = [$user->email];
        $params = [
            'FREELANCE_NAME' => $this->freelanceName,
            'CLIENT_NAME' => $this->clientName,
            'MISSION_NAME' => $this->missionName,
            'CONVERSATION_ID' => $this->conversationId,
        ];

        return [
            15, // template_id
            $receivers, // receivers
            $params, // params
            null // attachment
        ];
    }
}
