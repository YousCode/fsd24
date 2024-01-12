<?php


namespace App\Jobs\StripeWebhooks;

use App\Http\Services\Explorer\ExplorerPaymentService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Spatie\WebhookClient\Models\WebhookCall;

class HandleCheckoutSessionCompleted implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    /** @var WebhookCall */
    public $webhookCall;

    public function __construct(WebhookCall $webhookCall)
    {
        $this->webhookCall = $webhookCall;
    }

    public function handle(ExplorerPaymentService $explorerPaymentService)
    {
        if ($this->webhookCall->payload['type'] == "checkout.session.completed") {
            $explorerPaymentService->markQuotePaidByStripeSessionId($this->webhookCall->payload['data']['object']['id']);
        }
    }
}
