<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Achievement;
use App\Models\Badge;
use App\Services\AchievementService;

class AchievementsController extends Controller
{
    private $achievementService;

    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    public function index(User $user)
    {
        // Get all the unlocked achievements
        $unlockedAchievements = $user->achievements()->pluck('name')->toArray();

        // Get the next available achievement for each group
        $nextAvailableAchievements = $this->achievementService->getNextAvailableAchievements($unlockedAchievements);

        // Get the current user's badge
        $currentBadge = $user->badge;
        $nextBadge = null;
        if ($currentBadge) {
            // The next badge should be greater than the current badge's target, and order it by target asc
            $nextBadge = Badge::where('target', '>', $currentBadge->target)
                ->orderBy('target', 'asc')
                ->first();
        }

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
