<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Lesson;
use App\Events\LessonWatched;
use App\Models\Comment;
use App\Events\CommentWritten;
use App\Models\Achievement;
use App\Models\Badge;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentWrittenTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_user_wrote_one_comment(): void
    {
        $user = User::factory()->create();
        $comment = Comment::factory()->create(['user_id' => $user->id]);

        $masterBadge = Badge::factory()->create(['name' => 'Master', 'target' => 10]);
        $intermediateBadge = Badge::factory()->create(['name' => 'Intermediate', 'target' => 4]);
        $advancedBadge = Badge::factory()->create(['name' => 'Advanced', 'target' => 8]);

        $firstCommentWritten = Achievement::factory()->create([
            'name' => 'First Comment Written',
            'target' => 1,
            'group' => 'comment'
        ]);

        event(new CommentWritten($comment));

        $achievemntsCount = $user->achievements()->where('group', 'comment')->count();

        $this->assertTrue($achievemntsCount == 1);
    }

    public function test_user_wrote_eight_comment_should_have_intermediate_badge(): void
    {
        $user = User::factory()->create();

        $masterBadge = Badge::factory()->create(['name' => 'Master', 'target' => 10]);
        $intermediateBadge = Badge::factory()->create(['name' => 'Intermediate', 'target' => 4]);
        $advancedBadge = Badge::factory()->create(['name' => 'Advanced', 'target' => 8]);

        $firstCommentWritten = Achievement::factory()->create([
            'name' => 'First Comment Written',
            'target' => 1,
            'group' => 'comment'
        ]);
        $threeCommentsWritten = Achievement::factory()->create([
            'name' => 'Three Comments Written',
            'target' => 3,
            'group' => 'comment'
        ]);
        $fiveCommentsWritten = Achievement::factory()->create([
            'name' => 'Five Comments Written',
            'target' => 5,
            'group' => 'comment'
        ]);
        $eightCommentsWritten = Achievement::factory()->create([
            'name' => 'Eight Comments Written',
            'target' => 8,
            'group' => 'comment'
        ]);

        for ($i = 0; $i < 8; $i++) {
            $comment = Comment::factory()->create(['user_id' => $user->id]);
            event(new CommentWritten($comment));
        }

        $achievemntsCount = $user->achievements()->where('group', 'comment')->count();
        $user = User::with('badge')->findOrFail($user->id);

        $this->assertTrue($achievemntsCount == 4);
        $this->assertTrue($user->badge->name == $intermediateBadge->name);
    }

}
