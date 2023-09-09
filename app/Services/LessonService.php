<?php

namespace App\Services;

use App\Models\User;
use App\Models\Lesson;

class LessonService
{
    /**
     * Save the new lesson to the user's list
     */
    public function addNewLessonAsWatchedByUser(User $user, Lesson $lesson) {
        $user->lessons()->syncWithPivotValues($lesson->id, ['watched' => 1], false);
    }

}
