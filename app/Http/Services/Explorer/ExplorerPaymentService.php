<?php

namespace App\Http\Services\Explorer;

use App\Models\ExplorerMissionQuote;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Support\Facades\URL;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use App\User;

class ExplorerPaymentService
{
    /**
     * @var ExplorerQuoteService
     */
    private $explorerQuoteService;

    public function __construct(ExplorerQuoteService $explorerQuoteService)
    {
        $this->explorerQuoteService = $explorerQuoteService;
    }
    /**
     * @throws ApiErrorException
     */
    public function createExplorerQuoteCheckoutSession(ExplorerMissionQuote $quote): Response
    {
        Stripe::setApiKey(getenv('STRIPE_SECRET'));
        $coupon = new \Stripe\StripeClient(getenv('STRIPE_SECRET'));
        $couponCode = $coupon->coupons->create(['percent_off' => 100, 'duration' => 'repeating','duration_in_months' =>3 ]);
        $coArr = [];
        $exist = false;
        $arr = $coupon->promotionCodes->all()->data;
        foreach ($arr as $array){
            if(!empty($array))
            {
                $coArr[]=$array->code;
                $exist = true;
            }
        }

        if(!$exist)
        {
            $test = $coupon->promotionCodes->create([
                'coupon' => $couponCode->id,  'code' => 'CATALOGUE'
            ]);
        }
        $taxe = $coupon->taxRates->create([
            'display_name' => 'Commission Kolab',
            'inclusive' => false,
            'percentage' => 10,
            'description' => 'KOLAB SAS Sales Tax',
        ]);
        $user = User::findOrFail($quote->proposition->freelance_id);
        $session = Session::create([
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => 'Devis mission "' . $quote->proposition->mission->name.'"'." de $user->name",
                    ],
                    'unit_amount' => $quote->convert_price * 100,
                ],
                'quantity' => 1,
                'tax_rates'=>[$taxe->id],
            ]],
            'mode' => 'payment',
            "allow_promotion_codes"=>true,
            'automatic_tax'=>[
                "enabled"=> false,
              ],
            'success_url' => route('app_explorer_messenger'),
            'cancel_url' => route('app_explorer_messenger')
        ]);

        $this->explorerQuoteService->saveStripeSessionId($quote, $session->id);
        return new Response('', 303, ['Location' => $session->url]);
    }

    public function markQuotePaidByStripeSessionId($stripeCSId)
    {
        $this->explorerQuoteService->payQuoteByStripeSessionId($stripeCSId);
    }

}
