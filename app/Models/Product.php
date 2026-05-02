<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'slug', 'description', 'ingredients',
        'price', 'subscription_price', 'category',
        'image', 'volume_ml', 'is_active', 'stock',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'decimal:2',
        'subscription_price' => 'decimal:2',
    ];

    public function skinTypes()
    {
        return $this->belongsToMany(SkinType::class);
    }

    public function scopeActive(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForSkinType(\Illuminate\Database\Eloquent\Builder $query, string $skinType): \Illuminate\Database\Eloquent\Builder
    {
        return $query->whereHas('skinTypes', fn($q) => $q->where('slug', $skinType));
    }

    public function getDiscountPercentAttribute(): int
    {
        if ($this->price <= 0) return 0;
        return (int) round((1 - $this->subscription_price / $this->price) * 100);
    }
}
