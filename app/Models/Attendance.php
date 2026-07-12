<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['schedule_id', 'player_id', 'status', 'is_dues_paid', 'payment_receipt', 'notes'])]
class Attendance extends Model
{
    protected $casts = [
        'is_dues_paid' => 'boolean',
    ];
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}
