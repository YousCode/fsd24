<?php

namespace App\Notifications;

use App\Channels\SendinblueChannel;
use App\Models\ExplorerMissionConversation;
use App\Models\ExplorerMissionQuote;
use App\User;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class QuotePayed extends Notification
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
        $client = $proposition->client;
        $quote = ExplorerMissionQuote::find($proposition->quoteId);
        $quotePrice = $quote->price ?? 0;
        $quoteServiceFee = $quote->service_fee ?? 0;
        $amount = $quotePrice;

        $receivers = [$user->email];
        $params = [
            'CLIENT_NAME'=> $client->firstname && $client->lastname ? "{$client->firstname} {$client->lastname}" : $client->name,
            'PROJECT_TITLE'=> $proposition->explorerMission->name,
            'CONVERSATION_ID'=> $this->conversationId,
            'AMOUNT' => $amount,
        ];

        return [
            16, // template_id
            $receivers, // receivers
            $params, // params
            null // attachment
        ];
    }
}
