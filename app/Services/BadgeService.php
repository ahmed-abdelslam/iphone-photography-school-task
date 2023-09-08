<?php

namespace App\Services;

use App\Events\BadgeUnlocked;
use App\Models\Badge;
use App\Models\User;

class BadgeService
{
    /**
     * Add a badge for a user
     */
    public function addBadge(Badge $badge, User $user)
    {
        // Assign the badge to the user by changing the value of $user->badge_id
        $user->badge_id = $badge->id;
        // Save
        $user->save();

        // Fire BadgeUnlocked event
        event(new BadgeUnlocked($badge->name, $user));
    }
}
