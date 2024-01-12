<?php

namespace App\Enum;

class ExplorerMissionMessagesTypesEnum
{
    //const SYSTEM_QUOTE = "system_quote";
    const SYSTEM_SUBMIT_PROJECT = "system_project";

    const SYSTEM_MISSION_PROPOSITION_WAITING_DELIVERY = "system_mission_proposition_waiting_delivery";
    const SYSTEM_MISSION_QUOTE_PAID = "system_mission_quote_paid";
    CONST SYSTEM_MISSION_QUOTE_PAID_NOT_FILLED = "system_mission_quote_paid_not_filled";
    const SYSTEM_MISSION_FINISHED = 'system_mission_finished';
    const SYSTEM_MISSION_CLOSED = 'system_mission_closed';
    const SYSTEM_MISSION_CANCELED = 'system_mission_canceled';
    const SYSTEM_QUOTE_CREATION = 'system_quote_creation';
    const SYSTEM_INFO_FILLED = 'system_freelance_quote_info_filled';
    const SYSTEM_MISSION_TAKEN_BY_OTHER = 'system_mission_taken_by_other';
    const SYSTEM_MISSION_NOT_AVAILABLE = 'system_mission_not_available';

    const USER_QUOTE = "user_quote";
    const USER_MISSION_PROPOSAL = "user_mission_proposal";
    const USER_MESSAGE = "user_message";
    const USER_DRIVE_LINK = "user_drive_link";
    const USER_MEDIA = "user_media";
}
