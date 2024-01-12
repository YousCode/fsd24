<?php

namespace App\Enum;

class ExplorerMissionPropositionStatusEnum
{
    const WAITING_QUOTE = "waiting_quote";
    const WAITING_PAYMENT = "waiting_payment";
    const WAITING_WORK = "waiting_work";
    const FINISHED_WAITING_CLOSING = "waiting_closing";
    const INFO_BEEN_FILLED = "info_filled";
    const CLOSED = "closed";
    const CANCELED = "canceled";
    const TAKEN_BY_OTHER = "taken_by_other";
}
