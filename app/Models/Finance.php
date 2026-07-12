<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['team_id', 'type', 'amount', 'date', 'description', 'category'])]
class Finance extends Model
{
    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
