<?php

namespace App\Jobs\StripeWebhooks;

use App\Http\Services\Explorer\ExplorerPaymentService;
use App\Http\Services\Explorer\ExplorerQuoteService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\WebhookClient\Models\WebhookCall;

class HandleAccountUpdated implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /** @var WebhookCall */
    public $webhookCall;

    public function __construct(WebhookCall $webhookCall)
    {
        $this->webhookCall = $webhookCall;
    }

    public function handle(ExplorerQuoteService $explorerQuoteService)
    {
        if (!empty($this->webhookCall->payload['data']['object']['external_accounts']['data']) &&
            !empty($this->webhookCall->payload['data']['object']['capabilities']['card_payments']) &&
            $this->webhookCall->payload['data']['object']['capabilities']['card_payments'] == 'active' &&
            !empty($this->webhookCall->payload['data']['object']['capabilities']['transfers']) &&
            $this->webhookCall->payload['data']['object']['capabilities']['transfers'] == 'active' &&
            $this->webhookCall->payload['type'] == "account.updated") {
            try {
                $explorerQuoteService->accountUpdateByConnectSession($this->webhookCall->payload['data']['object']['id']);
            } catch (\Exception $e) {
                $e->getMessage();
            }
        }
    }
}
