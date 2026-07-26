<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use NotificationChannels\WebPush\HasPushSubscriptions;

#[Fillable(['name', 'email', 'password', 'team_id', 'role_id', 'slug', 'avatar', 'google_id', 'is_locked', 'email_verified_at'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasPushSubscriptions;

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            if (app()->environment('testing') && !array_key_exists('email_verified_at', $user->getAttributes())) {
                $user->email_verified_at = now();
            }

            if (empty($user->slug)) {
                if ($user->role_id) {
                    $role = \App\Models\Role::find($user->role_id);
                    if ($role && $role->name === 'superadmin') {
                        $user->slug = 'superadmin';
                        return;
                    }
                }
                $user->slug = static::generateUniqueSlug($user->name);
            }
        });

        static::updating(function ($user) {
            if ($user->isDirty('name')) {
                if ($user->role_id) {
                    $role = \App\Models\Role::find($user->role_id);
                    if ($role && $role->name === 'superadmin') {
                        $user->slug = 'superadmin';
                        return;
                    }
                }
                $user->slug = static::generateUniqueSlug($user->name);
            }
        });
    }

    public static function generateUniqueSlug(string $name): string
    {
        $slug = \Illuminate\Support\Str::slug($name);
        $originalSlug = $slug;
        $count = 1;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_locked' => 'boolean',
        ];
    }

    // Relationships
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function player()
    {
        return $this->hasOne(Player::class);
    }

    // Role Check Helpers
    public function isSuperAdmin(): bool
    {
        return $this->role && $this->role->name === 'superadmin';
    }

    public function isManagement(): bool
    {
        return $this->role && $this->role->name === 'management';
    }

    public function isCoach(): bool
    {
        return $this->role && $this->role->name === 'coach';
    }

    public function isPlayer(): bool
    {
        return $this->role && $this->role->name === 'player';
    }
}
