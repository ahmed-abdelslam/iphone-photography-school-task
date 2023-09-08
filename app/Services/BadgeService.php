<?php

namespace App\Services;

use App\Events\BadgeUnlocked;
use App\Models\Badge;
use App\Models\User;

class BadgeService
{
    /**
     * Check if the user has unlocked a new badge or not
     */
    public function checkBadgeUnlock(User $user)
    {
        // Get the total user's achievements
        $achievementsCount = $user->achievements()->count();

        // Get the badge that has the target of the same number of achievements
        $badge = Badge::where('target', $achievementCount)->first();

        // If there is no matching badge, then return and do nothing
        if (!$badge) {
            return;
        }

        // Add the badge to the user
        $this->addBadge($badge, $user);
    }

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
