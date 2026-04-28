<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DiagnosisQuestion extends Model
{
    protected $fillable = ['order', 'text', 'options', 'is_active'];

    protected $casts = [
        'options'   => 'array',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}
