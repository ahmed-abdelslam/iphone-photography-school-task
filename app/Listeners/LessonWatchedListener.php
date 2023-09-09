<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Services\AchievementService;
use App\Services\LessonService;

class LessonWatchedListener
{
    private $achievementService;
    private $lessonService;

    /**
     * Create the event listener.
     */
    public function __construct(AchievementService $achievementService, LessonService $lessonService)
    {
        $this->achievementService = $achievementService;
        $this->lessonService = $lessonService;
    }

    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        // Get the user model
        $user = $event->user;

        // Get the lesson model
        $lesson = $event->lesson;

        // Save the lesson as watched by the user
        $this->lessonService->addNewLessonAsWatchedByUser($user, $lesson);

        // Get the number of watched lessons
        $lessonsCount = $user->watched()->count();

        // Check if the user will unlock a new achievement or not
        $achievementGroup = 'lesson';
        $this->achievementService->checkAchievementUnlock($lessonsCount, $achievementGroup, $user);
    }
}
