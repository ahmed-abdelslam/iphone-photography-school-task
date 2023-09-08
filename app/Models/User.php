<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Lesson;
use App\Models\Comment;
use App\Models\Achievement;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'badge_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relationships
     *
     */

    /**
      * Get the lessons watched by a user
      */
    public function watched() {
        return $this->belongsToMany(Lesson::class, 'lesson_user')->where('watched', 1);
    }

    /**
      * Get the comments written by a user
      */
    public function comments() {
        return $this->hasMany(Comment::class);
    }

    /**
     * Get the user's achievements
     */
    public function achievements() {
        return $this->belongsToMany(Achievement::class, 'achievement_user');
    }
}
