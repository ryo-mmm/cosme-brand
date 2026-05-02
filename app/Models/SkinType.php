<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SkinType extends Model
{
    protected $fillable = ['slug', 'label'];

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
