<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Achievement extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'target',
        'group'
    ];

    /**
     * Achievement groups
     * Add here any new group for reusability across the project
     */
    private $achievementGroups = [
        'lesson',
        'comment'
    ];

    // Get achievement groups method
    public static function getAchievementGroups() {
        return $achievementGroups;
    }
}
