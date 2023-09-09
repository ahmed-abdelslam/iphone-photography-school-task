<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Lesson;
use App\Events\LessonWatched;
use App\Models\Achievement;
use App\Models\Badge;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LessonWatchedTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_user_watched_one_lesson(): void
    {
        $user = User::factory()->create();
        $lesson = Lesson::factory()->create();

        $masterBadge = Badge::factory()->create(['name' => 'Master', 'target' => 10]);
        $intermediateBadge = Badge::factory()->create(['name' => 'Intermediate', 'target' => 4]);
        $advancedBadge = Badge::factory()->create(['name' => 'Advanced', 'target' => 8]);

        $firstLessonWatched = Achievement::factory()->create([
            'name' => 'First Lesson Watched',
            'target' => 1,
            'group' => 'lesson'
        ]);

        event(new LessonWatched($lesson, $user));

        $achievemntsCount = $user->achievements()->where('group', 'lesson')->count();

        $this->assertTrue($achievemntsCount == 1);
    }

    public function test_user_watched_eight_lessons_should_have_intermediate_badge(): void
    {
        $user = User::factory()->create();
        $lessons = Lesson::factory()->times(8)->create();

        $masterBadge = Badge::factory()->create(['name' => 'Master', 'target' => 10]);
        $intermediateBadge = Badge::factory()->create(['name' => 'Intermediate', 'target' => 4]);
        $advancedBadge = Badge::factory()->create(['name' => 'Advanced', 'target' => 8]);

        $firstLessonWatched = Achievement::factory()->create([
            'name' => 'First Lesson Watched',
            'target' => 1,
            'group' => 'lesson'
        ]);
        $threeLessonsWatched = Achievement::factory()->create([
            'name' => 'Three Lessons Watched',
            'target' => 3,
            'group' => 'lesson'
        ]);
        $fiveLessonsWatched = Achievement::factory()->create([
            'name' => 'Five Lessons Watched',
            'target' => 5,
            'group' => 'lesson'
        ]);
        $eightLessonsWatched = Achievement::factory()->create([
            'name' => 'Eight Lessons Watched',
            'target' => 8,
            'group' => 'lesson'
        ]);

        foreach ($lessons as $key => $lesson) {
            event(new LessonWatched($lesson, $user));
        }

        $achievemntsCount = $user->achievements()->where('group', 'lesson')->count();
        $user = User::with('badge')->findOrFail($user->id);

        $this->assertTrue($achievemntsCount == 4);
        $this->assertTrue($user->badge->name == $intermediateBadge->name);
    }

}
