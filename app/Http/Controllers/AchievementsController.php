<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Achievement;
use App\Models\Badge;
use App\Services\AchievementService;
use App\Services\BadgeService;

class AchievementsController extends Controller
{
    private $achievementService;
    private $badgeService;

    public function __construct(AchievementService $achievementService, BadgeService $badgeService)
    {
        $this->achievementService = $achievementService;
        $this->badgeService = $badgeService;
    }

    public function index(User $user)
    {
        // Get all the unlocked achievements
        $unlockedAchievements = $user->achievements()->pluck('name')->toArray();

        // Get the next available achievement for each group
        $nextAvailableAchievements = $this->achievementService->getNextAvailableAchievements($unlockedAchievements);

        // Get the current user's badge
        $currentBadge = $user->badge;

        // Get the next badge
        $nextBadge = $this->badgeService->getNextBadge($currentBadge);

        $remainingToUnlockNextBadge = 0;
        if ($nextBadge) {
            // Subtract the target of the next badge from the target of current badge to know the remaining unlock the next
            $remainingToUnlockNextBadge = $nextBadge->target - $currentBadge->target;
        }

        return response()->json([
            'unlocked_achievements' => $unlockedAchievements,
            'next_available_achievements' => $nextAvailableAchievements,
            'current_badge' => $currentBadge->name ?? 'Beginner', // if the user does not have a badge_id then return Beginner Badge
            'next_badge' => $nextBadge->name ?? '', // if there is no next badge then return an empty string
            'remaing_to_unlock_next_badge' => $remainingToUnlockNextBadge
        ]);
    }
}
