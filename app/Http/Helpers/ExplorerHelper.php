<?php

namespace App\Http\Helpers;

use Carbon\Carbon;
use App\User;
use App\Models\ExplorerMissionQuote;
use App\Enum\ExplorerMissionQuoteStatusEnum;
use App\Enum\ExplorerMissionPropositionStatusEnum;
use Illuminate\Support\Facades\Log;
use App\Notifications\ExplorerFreelanceCloseMission;

class ExplorerHelper {

    public function __construct() {
    
    }

    public function checkPaidMission()
    {
        $now = Carbon::now();
        $quotePaid = ExplorerMissionQuote::where('status', ExplorerMissionQuoteStatusEnum::PAID)->orWhere('status', ExplorerMissionQuoteStatusEnum::INFO_FILLED)->get();
        foreach ($quotePaid as $quote) {
            $proposition = $quote->proposition()->first();
            if ($proposition && $proposition->status == ExplorerMissionPropositionStatusEnum::INFO_BEEN_FILLED) {
                $client = User::findOrFail($proposition->client_id);
                $freelance = User::findOrFail($proposition->freelance_id);
                $mission = $proposition->explorerMission()->first();
                if ($mission && $client && $freelance) {
                    $deadline = Carbon::createFromFormat('Y-m-d H:i:s', $mission->deadline);
                    if ($deadline) {
                        $deadlineAddOneDay = $deadline->addDay();
                        if ($deadlineAddOneDay && $client && $now->greaterThan($deadlineAddOneDay)) {
                            $client->notify(new ExplorerFreelanceCloseMission($freelance->name, $client->name, $mission->name, $proposition->conversationId));
                        }
                    }
                }
            }
        }
    }
}
