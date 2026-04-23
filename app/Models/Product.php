<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'ingredients',
        'price', 'subscription_price', 'category',
        'skin_types', 'image', 'volume_ml', 'is_active', 'stock',
    ];

    protected $casts = [
        'skin_types' => 'array',
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'subscription_price' => 'decimal:2',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForSkinType($query, string $skinType)
    {
        return $query->whereJsonContains('skin_types', $skinType);
    }

    public function getDiscountPercentAttribute(): int
    {
        if ($this->price <= 0) return 0;
        return (int) round((1 - $this->subscription_price / $this->price) * 100);
    }
}
