<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['team_id', 'schedule_id', 'opponent', 'date', 'location', 'status', 'score_team', 'score_opponent', 'notes'])]
class MatchGame extends Model
{
    // Point to matches table
    protected $table = 'matches';

    protected $casts = [
        'date' => 'date',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function statistics()
    {
        return $this->hasMany(Statistic::class, 'match_id');
    }
}
