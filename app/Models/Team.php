<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['name', 'logo', 'description', 'plan', 'premium_until', 'qris_image', 'free_expiration_email_sent'])]
class Team extends Model
{
    protected function casts(): array
    {
        return [
            'premium_until' => 'datetime',
            'free_expiration_email_sent' => 'boolean',
        ];
    }

    // Plan limits checkers
    public function isPremium(): bool
    {
        if ($this->plan !== 'premium') {
            return false;
        }

        if ($this->premium_until && now()->gt($this->premium_until)) {
            return false;
        }

        return true;
    }

    public function isFree(): bool
    {
        return !$this->isPremium();
    }

    public function isFreeExpired(): bool
    {
        if ($this->plan === 'free' && $this->created_at) {
            return $this->created_at->copy()->addMonths(2)->isPast();
        }
        return false;
    }

    public function canAddPlayer(): bool
    {
        if ($this->isPremium()) {
            return true;
        }
        return $this->players()->count() < 7;
    }

    public function canAddCoach(): bool
    {
        if ($this->isPremium()) {
            return true;
        }
        $coachRole = Role::where('name', 'coach')->first();
        if (!$coachRole) {
            return false;
        }
        return $this->users()->where('role_id', $coachRole->id)->count() < 1;
    }



    public function canAddFinance(): bool
    {
        if ($this->isPremium()) {
            return true;
        }
        return $this->finances()->count() < 10;
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function matches()
    {
        return $this->hasMany(MatchGame::class);
    }

    public function finances()
    {
        return $this->hasMany(Finance::class);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    public function tactics()
    {
        return $this->hasMany(Tactic::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
