<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionSkip extends Model
{
    protected $fillable = [
        'user_id', 'stripe_subscription_id',
        'original_next_billing_date', 'new_next_billing_date',
    ];

    protected $casts = [
        'original_next_billing_date' => 'date',
        'new_next_billing_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
