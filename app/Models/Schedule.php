<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['team_id', 'title', 'type', 'start_time', 'location', 'description', 'opponent', 'dues_amount'])]
class Schedule extends Model
{
    protected $casts = [
        'start_time' => 'datetime',
        'dues_amount' => 'decimal:2',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
