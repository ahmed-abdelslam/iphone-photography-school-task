<?php

namespace App\Services;

use App\Events\AchievementUnlocked;
use App\Models\Achievement;
use App\Models\User;
use App\Services\BadgeService;

class AchievementService
{
    private $badgeService;

    public function __construct(BadgeService $badgeService)
    {
        $this->badgeService = $badgeService;
    }

    /**
     * Check if a user will unlock a new achievement or not
     */
    public function checkAchievementUnlock($achievementsCount, $achievementGroup, $user) {
        /**
         * Get the achievement that has the target of the same number of achievements count and the same group
         * For example: if a user watched 5 lessons then check if there is an achievement that has a target of 5 and it is in the 'lesson' group
         */
        $achievement = Achievement::where([
            ['target', $achievementsCount],
            ['group', $achievementGroup]
        ])->first();

        // If there is no matching achievement, then return and do nothing
        if (!$achievement) {
            return;
        }

        // Unlock the new achievement
        $this->unlockAchievement($achievement, $user);
    }

    /**
     * Unlock a new achievement
     */
    public function unlockAchievement(Achievement $achievement, User $user)
    {
        // Attach the new achievement to the user's achievements
        $user->achievements()->syncWithoutDetaching($achievement->id);

        // Fire AchievementUnlocked event
        event(new AchievementUnlocked($achievement->name, $user));

        // Check if the user will unlock a new badge or not
        $this->badgeService->checkBadgeUnlock($user);
    }
}
