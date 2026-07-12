<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['team_id', 'coach_id', 'title', 'description', 'formation', 'canvas_data'])]
class Tactic extends Model
{
    protected $casts = [
        'canvas_data' => 'array',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function coach()
    {
        return $this->belongsTo(User::class, 'coach_id');
    }
}
