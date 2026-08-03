<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'slug', 'address', 'phone', 'is_active', 'is_online', 'latitude', 'longitude', 'radius_meters'];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_online' => 'boolean',
        ];
    }

    public function scopeOnline($query)
    {
        return $query->where('is_online', true);
    }

    public function businessTypes()
    {
        return $this->belongsToMany(BusinessType::class, 'branch_business_type');
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function rawMaterials()
    {
        return $this->hasMany(RawMaterial::class);
    }

    public function stockTransactions()
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }

    public function shifts()
    {
        return $this->hasMany(Shift::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
