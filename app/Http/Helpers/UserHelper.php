<?php

namespace App\Http\Helpers;

use App\Enum\UserRolesEnum;
use App\User;

class UserHelper {
    /**
     * Check if the user is a Freelance
     * @param User $user
     * @return bool
     */
    public static function isFreelance(User $user): bool
    {
        return $user->hasRole(UserRolesEnum::FREELANCE);
    }
}
