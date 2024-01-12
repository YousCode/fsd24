<?php

namespace App\Notifications;

use App\Channels\SendinblueChannel;
use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ExplorerClientMissionPayed extends Notification
{
    use Queueable;

    private $missionName;
    private $conversationId;
    private $quoteTotalHt;
    private $quoteTotalFee;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($missionName, $conversationId, $quoteTotalHt, $quoteTotalFee)
    {
        $this->missionName = $missionName ?? '';
        $this->conversationId = $conversationId ?? false;
        $this->quoteTotalHt = $quoteTotalHt ?? 0;
        $this->quoteTotalFee = $quoteTotalFee ?? 0;
    }

    public function via($notifiable)
    {
        return [SendinblueChannel::class];
    }

    public function toSendinblue(User $user)
    {
        $receivers = [$user->email];
        $params = [
            'MISSION_NAME' => $this->missionName,
            'CONVERSATION_ID' => $this->conversationId,
            'QUOTE_TOTAL_HT' => $this->quoteTotalHt,
            'QUOTE_TOTAL_FEE' => $this->quoteTotalFee,
        ];

        return [
            14, // template_id
            $receivers, // receivers
            $params, // params
            null // attachment
        ];
    }
}
