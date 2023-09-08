<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

class BadgeUnlocked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $badge_name;
    public $user;

    /**
     * Create a new event instance.
     */
    public function __construct(string $badgeName, User $user)
    {
        $this->badge_name = $badgeName;
        $this->user = $user;
    }
}
