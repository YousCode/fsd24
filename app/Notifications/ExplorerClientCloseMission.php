<?php

namespace App\Notifications;

use App\Channels\SendinblueChannel;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ExplorerClientCloseMission extends Notification
{
    use Queueable;

    private $clientName;
    private $missionName;
    private $conversationId;
    private $quoteTotalHt;
    private $freelanceSubscription;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($clientName, $missionName, $conversationId, $quoteTotalHt, $freelanceSubscription)
    {
        $this->clientName = $clientName ?? '';
        $this->missionName = $missionName ?? '';
        $this->conversationId = $conversationId ?? false;
        $this->quoteTotalHt = $quoteTotalHt ?? 0;
        $this->freelanceSubscription = $freelanceSubscription ?? false;
    }

    public function via($notifiable)
    {
        return [SendinblueChannel::class];
    }

    public function toSendinblue(User $user)
    {
        $receivers = [$user->email];
        $params = [
            'CLIENT_NAME' => $this->clientName,
            'MISSION_NAME' => $this->missionName,
            'CONVERSATION_ID' => $this->conversationId,
            'QUOTE_TOTAL_HT' => $this->quoteTotalHt,
            'FREELANCE_SUBSCRIPTIONS' => $this->freelanceSubscription,
        ];

        return [
            17, // template_id
            $receivers, // receivers
            $params, // params
            null // attachment
        ];
    }
}
