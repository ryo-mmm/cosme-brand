<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'stripe_charge_id',
        'stripe_invoice_id',
        'amount',
        'currency',
        'status',
        'description',
        'payment_method_type',
        'refunded_at',
    ];

    protected $casts = [
        'refunded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function isRefunded(): bool
    {
        return $this->status === 'refunded';
    }
}
