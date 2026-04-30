<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, Billable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'postal_code',
        'address',
        'address_line2',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function skinDiagnoses()
    {
        return $this->hasMany(SkinDiagnosis::class);
    }

    public function latestDiagnosis()
    {
        return $this->hasOne(SkinDiagnosis::class)->latestOfMany();
    }

    public function subscriptionSkips()
    {
        return $this->hasMany(SubscriptionSkip::class);
    }
}
