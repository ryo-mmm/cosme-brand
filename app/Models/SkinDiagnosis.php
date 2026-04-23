<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkinDiagnosis extends Model
{
    protected $fillable = [
        'user_id', 'session_id', 'answers',
        'skin_type', 'score', 'recommended_product_ids',
    ];

    protected $casts = [
        'answers' => 'array',
        'recommended_product_ids' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function recommendedProducts()
    {
        return Product::whereIn('id', $this->recommended_product_ids ?? [])->get();
    }

    public function getSkinTypeLabelAttribute(): string
    {
        return match ($this->skin_type) {
            'dry'         => '乾燥肌',
            'oily'        => 'オイリー肌',
            'combination' => '混合肌',
            'sensitive'   => '敏感肌',
            default       => '普通肌',
        };
    }
}
