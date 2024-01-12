<?php

namespace App\Http\Controllers\API\Explorer;

use App\Enum\ExplorerMissionQuoteStatusEnum;
use App\Http\Controllers\Controller;
use App\Http\Services\Explorer\ExplorerPaymentService;
use App\Http\Services\Explorer\ExplorerQuoteService;
use App\Models\ExplorerMissionQuote;
use Illuminate\Http\Request;
use Stripe\Exception\ApiErrorException;
use Session;
use Stripe;

class ExplorerPaymentController extends Controller
{
    public function quotePaymentPage($quote_id, Request $request, ExplorerPaymentService $explorerPaymentService)
    {
        $quote = ExplorerMissionQuote::find($quote_id);

        if ($quote->status === ExplorerMissionQuoteStatusEnum::PAID) {
            return redirect()->route('app_explorer_messenger');
        }
        try {
             return $explorerPaymentService->createExplorerQuoteCheckoutSession($quote);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
            return redirect()->route('app_explorer_messenger');
        }
    }
}
