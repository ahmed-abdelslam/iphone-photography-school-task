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

    //------------------------------------------------------------------------------------------
    public function getNextAvailableAchievements($unlockedAchievements) {
        /**
         * Get the next available achievement for each group
         * Instead of making multiple queries for each group
         * We can iterate through the achievement groups to get the next available for each group
         * This helps us in the future if we need later to add a new achievement group, instead of repeating the same query for the new group
         */
        $nextAvailableAchievements = [];
        $achievementModel = new Achievement();
        // Get all achievement groups
        $achievementGroups = $achievementModel->getAchievementGroups();
        foreach ($achievementGroups as $achievementGroup) {
            // Get the next available achievement in a group that is not in unlocked achievements, and order it by target asc
            $nextAvailableAchievement = Achievement::whereNotIn('name', $unlockedAchievements)
                ->where('group', $achievementGroup)
                ->orderBy('target', 'asc')
                ->pluck('name')
                ->first();
            // If there is an available achievement then add it to nextAvailableAchievements array
            if ($nextAvailableAchievement)
                array_push($nextAvailableAchievements, $nextAvailableAchievement);
        }

        return $nextAvailableAchievements;
    }
}
