<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Attributes\Fillable;

#[Fillable(['team_id', 'user_id', 'amount', 'status', 'admin_notes', 'reference', 'merchant_ref', 'payment_method', 'payment_status', 'payment_instructions', 'qr_url', 'pay_code', 'payment_url'])]
class PremiumPayment extends Model
{
    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'payment_instructions' => 'array',
        ];
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
