<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['team_id', 'schedule_id', 'opponent', 'date', 'location', 'status', 'score_team', 'score_opponent', 'notes', 'possession_team', 'possession_opponent', 'shoot_on_target_team', 'shoot_on_target_opponent', 'shoot_off_target_team', 'shoot_off_target_opponent'])]
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
