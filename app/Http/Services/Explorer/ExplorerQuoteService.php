<?php

namespace App\Http\Services\Explorer;


use App\Enum\ExplorerMissionQuoteStatusEnum;
use App\Events\ExplorerMessage;

use App\Models\ExplorerMissionProposition;
use App\Models\ExplorerMissionQuote;
use App\User;
use Carbon\Carbon;
use DateTime;
use App\Notifications\QuotePayed;
use App\Notifications\ExplorerClientMissionPayed;
use AmrShawky\LaravelCurrency\Facade\Currency;
use Illuminate\Support\Facades\Log;

class ExplorerQuoteService
{
    /**
     * @var ExplorerMissionService
     */
    private $explorerMissionService;
    /**
     * @var ExplorerKolabService
     */
    private $explorerKolabService;

    public function __construct(ExplorerMissionService $explorerMissionService, ExplorerKolabService $explorerKolabService, Currency $currency)
    {
        $this->explorerMissionService = $explorerMissionService;
        $this->explorerKolabService = $explorerKolabService;
    }

    /**
     * @param $quoteInfos
     * @param ExplorerMissionProposition $explorerMissionProposition
     * @return ExplorerMissionQuote
     */
    public function newQuote($quoteInfos, ExplorerMissionProposition $explorerMissionProposition): ExplorerMissionQuote
    {
        $explorerMission = $explorerMissionProposition;
        $quote = new ExplorerMissionQuote();
        $currency = $quoteInfos['currency'] ?? 1;
        $quote->deadline = match ($quoteInfos['deadline']) {
            __('explorer.form-choice-delivery-1') => DateTime::createFromFormat('Y-m-d', Carbon::now()->add(24, 'hours')->format('Y-m-d'))->format('Y-m-d'),
            __('explorer.form-choice-delivery-2')  => DateTime::createFromFormat('Y-m-d', Carbon::now()->add(2, 'days')->format('Y-m-d'))->format('Y-m-d'),
            __('explorer.form-choice-delivery-3')  => DateTime::createFromFormat('Y-m-d', Carbon::now()->add(3, 'days')->format('Y-m-d'))->format('Y-m-d'),
            __('explorer.form-choice-delivery-4')  => DateTime::createFromFormat('Y-m-d', Carbon::now()->add(4, 'days')->format('Y-m-d'))->format('Y-m-d'),
            default => $quoteInfos['deadline'],
        };
        //$quote->deadline = $quoteInfos['deadline'];
        $quote->quote_line = json_encode($quoteInfos['quoteItems']);
        $price = $quoteInfos['price'] ?? 0;
        $service_fee = $this->fees($quoteInfos['price']) ?? 0;

        if ($currency == 1) { //EUR
            $quote->convert_price = $price;
            $quote->service_fee = $service_fee;
        } elseif ($currency == 2) { //US
            $quote->convert_price = round(Currency::convert()->from('USD')->to('EUR')->amount($price)->get(), 0);
            $quote->service_fee_convert = round(Currency::convert()->from('USD')->to('EUR')->amount($service_fee)->get(), 0);
        }
        $quote->price = $price;
        $quote->service_fee = $service_fee;

        $quote->comments = $quoteInfos['comment'];
        $quote->currency_id = $currency;
        $quote->status = ExplorerMissionQuoteStatusEnum::WAITING_PAYMENT;
        $quote->id_proposition = $explorerMissionProposition->id;
        $quote->created_by = $explorerMissionProposition->freelance_id;
        $quote->save();

        return $quote;
    }
    public function fees($amount)
    {
        return ($amount * 10) / 100;
    }

    public function payQuote(ExplorerMissionQuote $quote)
    {
        try {
            event(new ExplorerMessage());
            $this->explorerKolabService->createKolabProjectFromExplorerProposition($quote->proposition);
            $this->explorerKolabService->createKolabPlanningTask($quote);
            $this->updateQuoteStatus($quote, ExplorerMissionQuoteStatusEnum::PAID);
            $freelance =  User::where('id','=',$quote->proposition->freelance_id)->first();
            $client =  User::where('id','=',$quote->proposition->client_id)->first();
            $this->explorerMissionService->quotePaidMissionProposition($quote->proposition);
            $freelance->notify(new QuotePayed($quote->proposition->conversationId));

            $missionName = $quote->proposition->explorerMission->name ?? '';
            $conversationId = $quote->proposition->conversationId ?? false;
            $quoteTotalHt = $quote->price - $quote->service_fee ?? 0;
            $quoteTotalFee = $quote->service_fee ?? 0;
            $client->notify(new ExplorerClientMissionPayed($missionName, $conversationId, $quoteTotalHt, $quoteTotalFee));
            $freelance->notify(new ExplorerClientMissionPayed($missionName, $conversationId, $quoteTotalHt, $quoteTotalFee));
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());

        }

    }

    public function updateQuoteStatus(ExplorerMissionQuote $quote, $status): ExplorerMissionQuote
    {
        $quote->status = $status;

        if ($status == ExplorerMissionQuoteStatusEnum::PAID) {
            $quote->quote_paid_date = new \DateTime();
        }

        $quote->save();
        return $quote;
    }

    private function findQuote($quoteId)
    {
        return ExplorerMissionQuote::find($quoteId);
    }

    public function saveStripeSessionId(ExplorerMissionQuote $quote, $stripeCSId)
    {
        $quote->stripe_cs_id = $stripeCSId;
        $quote->init_payment_date = new \DateTime();
        $quote->save();
    }

    public function saveStripeAccountId(ExplorerMissionQuote $quote, $stripeAccountId)
    {
        $quote->freelance_Stripe_accId = $stripeAccountId;
        $quote->save();
    }

    public function payQuoteByStripeSessionId($stripeCSId)
    {
        $quote = ExplorerMissionQuote::where('stripe_cs_id', '=', $stripeCSId)->first();
        $this->payQuote($quote);
    }

    //If the freelance has filled his bank information for payment
    public function accountUpdateByConnectSession($accountId, $connected = false)
    {
        $quote = ExplorerMissionQuote::where('freelance_Stripe_accId', '=', $accountId);
        if ($connected) {
            $quote->where('status', ExplorerMissionQuoteStatusEnum::PAID);
        }
        $quote = $quote->first();
        $proposition = ExplorerMissionProposition::where('id', $quote->id_proposition)->first();
        $quote->status = ExplorerMissionQuoteStatusEnum::INFO_FILLED;
        $quote->freelanceSubscription = 1;
        $quote->save();
        $this->explorerMissionService->freelanceInformationFilledQuote($proposition);
        return $quote;
    }
}
