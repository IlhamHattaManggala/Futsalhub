<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['team_id', 'coach_id', 'task_category_id', 'title', 'description', 'due_date'])]
class Task extends Model
{
    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }

    public function category()
    {
        return $this->belongsTo(TaskCategory::class, 'task_category_id');
    }

    public function players()
    {
        return $this->belongsToMany(Player::class, 'player_tasks')
            ->withPivot('status', 'proof_image', 'completed_at', 'started_at', 'start_proof_image')
            ->withTimestamps();
    }
}
