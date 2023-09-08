<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\AchievementService;

class CommentWrittenListener
{
    private $achievementService;

    /**
     * Create the event listener.
     */
    public function __construct(AchievementService $achievementService)
    {
        $this->achievementService = $achievementService;
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        // Get the user model
        $user = $event->user;

        // Get the number of user's comments
        $commentsCount = $user->comments()->count();

        // Check if the user will unlock a new achievement or not
        $achievementGroup = 'comment';
        $this->achievementService->checkAchievementUnlock($commentsCount, $achievementGroup, $user);
    }
}
