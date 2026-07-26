<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['match_id', 'player_id', 'goals', 'assists', 'yellow_cards', 'red_cards', 'minutes_played', 'clearance', 'save', 'shoot_on_target', 'shoot_off_target'])]
class Statistic extends Model
{
    public function matchGame()
    {
        return $this->belongsTo(MatchGame::class, 'match_id');
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
