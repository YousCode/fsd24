<?php

namespace App\Http\Controllers\API\Explorer;

use App\Http\Controllers\Controller;
use App\Models\ExplorerMissionQuote;
use Illuminate\Http\Response;
use Stripe\Exception\ApiErrorException;

class ExplorerStripeFreelanceLogin extends Controller
{

    public function loginFreelanceToExpressStripeDashboard($quote_id)
    {
        $quote = ExplorerMissionQuote::findOrFail($quote_id);
        try {
            return $this->propositionUserToken($quote);
        }catch(\Exception $e){
            return redirect()->route('app_explorer_messenger');
        }

    }
    //
    /**
     * @throws ApiErrorException
     */
    private function propositionUserToken($quote):Response
    {
        $stripe = new \Stripe\StripeClient(getenv('STRIPE_SECRET'));
        $token = $stripe->accounts->createLoginLink($quote->freelance_Stripe_accId, []);
        return new Response('', 303, ['Location' => $token->url]);
    }
}
