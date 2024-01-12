<?php

namespace App\Http\Controllers\API\Explorer;

use App\Http\Controllers\Controller;
use App\Http\Services\Explorer\ExplorerQuoteService;
use App\Models\ExplorerMissionProposition;
use App\Models\ExplorerMissionQuote;
use App\User;
use Illuminate\Http\Response;
use Stripe\Exception\ApiErrorException;

class ExplorerStripeAccountController extends Controller
{
    /**
     * @var ExplorerQuoteService
     */
    private $explorerQuoteService;

    public function __construct(ExplorerQuoteService $explorerQuoteService)
    {
        $this->explorerQuoteService = $explorerQuoteService;
    }
    //Redirect only freelance to the OnboardingStripe Connect account
    public function accountCreate($quote_id)
    {
        $quote = ExplorerMissionQuote::findOrFail($quote_id);
        $user = $quote->created_by;
        try {
            return $this->userToStripePayment($user,$quote);
        }catch(\Exception $e){
            throw new \Exception($e->getMessage());
            return redirect()->route('app_explorer_messenger');
        }
    }

    /**
     * @throws ApiErrorException
     */
    private function userToStripePayment($user, $quote): Response
    {
        $userToCreate = User::findOrFail($user);
        $stripe = new \Stripe\StripeClient(getenv('STRIPE_SECRET'));
        $freelanceStripeAccId = $quote->getStripeAccIdFromUserId($userToCreate->id);
        if ($freelanceStripeAccId) {
            $retrieve = $stripe->accounts->retrieve($freelanceStripeAccId, []);
            if ($retrieve) {
                //$retrieve = json_encode($retrieve);
                //_info($retrieve, 'single');
                if (!empty($retrieve['capabilities']['card_payments']) &&
                    $retrieve['capabilities']['card_payments'] == 'active' &&
                    !empty($retrieve['capabilities']['transfers']) &&
                    $retrieve['capabilities']['transfers'] == 'active') {
                    try {
                       // _info($retrieve['id']);
                        $this->explorerQuoteService->saveStripeAccountId($quote, $retrieve['id']);
                        $this->explorerQuoteService->accountUpdateByConnectSession($retrieve['id'], true);
                        $loginLink = $stripe->accounts->createLoginLink($freelanceStripeAccId, []);
                        if ($loginLink) {
                            return new Response('', 303, ['Location' => $loginLink->url]);
                        }
                    } catch (\Exception $e) {
                        $e->getMessage();
                    }
                }
            }
        }
        $create = $stripe->accounts->create([
            'type' => 'express',
            'email' => $userToCreate->email,
            'capabilities' => [
                'card_payments' => ['requested' => true],
                'transfers' => ['requested' => true],
            ],
            //'person_token' => $this->propositionUserToken($user)
        ]);
        $this->explorerQuoteService->saveStripeAccountId($quote, $create->id);
        $links = $stripe->accountLinks->create(
            [
                'account' => $create->id,
                'refresh_url' => route('app_explorer_messenger'),
                'return_url' => route('app_explorer_messenger'),
                'type' => 'account_onboarding',
            ]
        );
        //_info($links, 'single');
        return new Response('', 303, ['Location' => $links->url]);

    }

}
