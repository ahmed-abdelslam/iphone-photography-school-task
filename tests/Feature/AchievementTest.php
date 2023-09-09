<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Achievement;
use App\Models\Badge;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AchievementTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $user = User::factory()->create();

        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertStatus(200);
    }

    public function test_the_application_returns_404_response_for_non_exist_user(): void
    {
        $response = $this->get("/users/3/achievements");

        $response->assertStatus(404);
    }

    /**
     * A new user with no achievements unlocked or available and with no badges at all
     */
    public function test_the_application_returns_a_correct_response_for_a_user_does_not_have_achievements_and_no_next_available_achievements_no_next_badge(): void
    {
        $user = User::factory()->create();

        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertExactJson([
            'unlocked_achievements' => [],
            'next_available_achievements' => [],
            'current_badge' => 'Beginner',
            'next_badge' => '',
            'remaing_to_unlock_next_badge' => 0
        ]);
    }

    /**
     * A user with unlocked all achievements
     */
    public function test_the_application_returns_a_correct_response_for_a_user_unlocked_all_achievements(): void
    {
        $user = User::factory()->create();

        $firstLessonWatched = Achievement::factory()->create([
            'name' => 'First Lesson Watched',
            'target' => 1,
            'group' => 'lesson'
        ]);

        $firstCommentWritten = Achievement::factory()->create([
            'name' => 'First Comment Written',
            'target' => 1,
            'group' => 'comment'
        ]);

        $user->achievements()->attach([$firstLessonWatched->id, $firstCommentWritten->id]);

        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertExactJson([
            'unlocked_achievements' => [$firstLessonWatched->name, $firstCommentWritten->name],
            'next_available_achievements' => [],
            'current_badge' => 'Beginner',
            'next_badge' => '',
            'remaing_to_unlock_next_badge' => 0
        ]);
    }

    /**
     * A user that has no achievements and there is available achievements
     * If there are multiple groups then it should only return one for each group
     * For example: ['First Lesson Watched', 'First Comment Written'], other lessons achievement should not be in the next available
     */
    public function test_the_application_returns_a_correct_response_for_a_user_does_not_have_achievements(): void
    {
        $user = User::factory()->create();

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
        $firstCommentWritten = Achievement::factory()->create([
            'name' => 'First Comment Written',
            'target' => 1,
            'group' => 'comment'
        ]);

        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertExactJson([
            'unlocked_achievements' => [],
            'next_available_achievements' => [$firstLessonWatched->name, $firstCommentWritten->name],
            'current_badge' => 'Beginner',
            'next_badge' => '',
            'remaing_to_unlock_next_badge' => 0
        ]);
    }

    /**
     * A user that has unlocked achievements annd there are available achievements
     */
    public function test_the_application_returns_a_correct_response_for_a_user_does_have_achievements(): void
    {
        $user = User::factory()->create();

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
        $firstCommentWritten = Achievement::factory()->create([
            'name' => 'First Comment Written',
            'target' => 1,
            'group' => 'comment'
        ]);

        $user->achievements()->attach([$firstLessonWatched->id, $firstCommentWritten->id]);

        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertExactJson([
            'unlocked_achievements' => [$firstLessonWatched->name, $firstCommentWritten->name],
            'next_available_achievements' => [$threeLessonsWatched->name],
            'current_badge' => 'Beginner',
            'next_badge' => '',
            'remaing_to_unlock_next_badge' => 0
        ]);
    }

    /**
     * A user that has unlocked achievements and next badge
     */
    public function test_the_application_returns_a_correct_response_for_a_user_does_have_achievements_with_next_badge(): void
    {
        $currentBadge = Badge::factory()->create(['name' => 'Intermediate', 'target' => 4]);

        $nextBadge = Badge::factory()->create(['name' => 'Advanced', 'target' => 8]);

        $user = User::factory()->create(['badge_id' => $currentBadge->id]);

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
        $firstCommentWritten = Achievement::factory()->create([
            'name' => 'First Comment Written',
            'target' => 1,
            'group' => 'comment'
        ]);

        $user->achievements()->attach([$firstLessonWatched->id, $threeLessonsWatched->id, $fiveLessonsWatched->id, $firstCommentWritten->id]);

        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertExactJson([
            'unlocked_achievements' => [$firstLessonWatched->name, $threeLessonsWatched->name, $fiveLessonsWatched->name, $firstCommentWritten->name],
            'next_available_achievements' => [],
            'current_badge' => 'Intermediate',
            'next_badge' => 'Advanced',
            'remaing_to_unlock_next_badge' => 4
        ]);
    }

    /**
     * A user that has unlocked achievements and next badge
     */
    public function test_the_application_returns_a_correct_response_for_a_user_unlocked_all_achievements_with_no_next_badge(): void
    {
        $masterBadge = Badge::factory()->create(['name' => 'Master', 'target' => 10]);
        $intermediateBadge = Badge::factory()->create(['name' => 'Intermediate', 'target' => 4]);
        $advancedBadge = Badge::factory()->create(['name' => 'Advanced', 'target' => 8]);

        $user = User::factory()->create(['badge_id' => $masterBadge->id]);

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
        $firstCommentWritten = Achievement::factory()->create([
            'name' => 'First Comment Written',
            'target' => 1,
            'group' => 'comment'
        ]);

        $user->achievements()->attach([$firstLessonWatched->id, $threeLessonsWatched->id, $fiveLessonsWatched->id, $firstCommentWritten->id]);

        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertExactJson([
            'unlocked_achievements' => [$firstLessonWatched->name, $threeLessonsWatched->name, $fiveLessonsWatched->name, $firstCommentWritten->name],
            'next_available_achievements' => [],
            'current_badge' => 'Master',
            'next_badge' => '',
            'remaing_to_unlock_next_badge' => 0
        ]);
    }

    /**
     * A user that has no badge
     */
    public function test_the_application_returns_a_correct_response_for_a_user_has_no_badge(): void
    {
        $masterBadge = Badge::factory()->create(['name' => 'Master', 'target' => 10]);
        $intermediateBadge = Badge::factory()->create(['name' => 'Intermediate', 'target' => 4]);
        $advancedBadge = Badge::factory()->create(['name' => 'Advanced', 'target' => 8]);

        $user = User::factory()->create();

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
        $firstCommentWritten = Achievement::factory()->create([
            'name' => 'First Comment Written',
            'target' => 1,
            'group' => 'comment'
        ]);

        $user->achievements()->attach([$firstLessonWatched->id]);

        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertExactJson([
            'unlocked_achievements' => [$firstLessonWatched->name],
            'next_available_achievements' => [$threeLessonsWatched->name, $firstCommentWritten->name],
            'current_badge' => 'Beginner',
            'next_badge' => 'Intermediate',
            'remaing_to_unlock_next_badge' => 4
        ]);
    }

    /**
     * A user that has unlocked achievements and next badge
     */
    public function test_the_application_returns_a_correct_response_for_a_user_unlocked_all_achievements_with_unordered_achievement(): void
    {
        $masterBadge = Badge::factory()->create(['name' => 'Master', 'target' => 10]);
        $intermediateBadge = Badge::factory()->create(['name' => 'Intermediate', 'target' => 4]);
        $advancedBadge = Badge::factory()->create(['name' => 'Advanced', 'target' => 8]);

        $user = User::factory()->create(['badge_id' => $masterBadge->id]);

        // Random Achievement's target order should not affect the desired result
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
        $firstLessonWatched = Achievement::factory()->create([
            'name' => 'First Lesson Watched',
            'target' => 1,
            'group' => 'lesson'
        ]);
        $firstCommentWritten = Achievement::factory()->create([
            'name' => 'First Comment Written',
            'target' => 1,
            'group' => 'comment'
        ]);

        $user->achievements()->attach([$firstLessonWatched->id, $threeLessonsWatched->id, $fiveLessonsWatched->id, $firstCommentWritten->id]);

        $response = $this->get("/users/{$user->id}/achievements");

        $response->assertExactJson([
            'unlocked_achievements' => [$firstLessonWatched->name, $threeLessonsWatched->name, $fiveLessonsWatched->name, $firstCommentWritten->name],
            'next_available_achievements' => [],
            'current_badge' => 'Master',
            'next_badge' => '',
            'remaing_to_unlock_next_badge' => 0
        ]);
    }
}
