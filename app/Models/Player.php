<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['team_id', 'user_id', 'name', 'number', 'position', 'phone', 'birth_date', 'height', 'weight'])]
class Player extends Model
{
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function statistics()
    {
        return $this->hasMany(Statistic::class);
    }

    public function tasks()
    {
        return $this->belongsToMany(Task::class, 'player_tasks')
            ->withPivot('status', 'proof_image', 'completed_at', 'started_at', 'start_proof_image')
            ->withTimestamps();
    }
}
