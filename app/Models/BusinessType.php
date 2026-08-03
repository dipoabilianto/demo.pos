<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessType extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_active'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_business_type');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
