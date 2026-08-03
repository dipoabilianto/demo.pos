<?php

namespace App\Models;

use App\Models\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RawMaterial extends Model
{
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
    protected $fillable = [
        'name', 'unit', 'current_stock', 'min_stock', 'branch_id',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope);
    }

    protected function casts(): array
    {
        return [
            'current_stock' => 'decimal:2',
            'min_stock' => 'decimal:2',
        ];
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }

    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->min_stock;
    }

    public function stockIn(): HasMany
    {
        return $this->hasMany(StockTransaction::class)->where('type', 'in');
    }

    public function stockOut(): HasMany
    {
        return $this->hasMany(StockTransaction::class)->where('type', 'out');
    }

    public function stockOpnames(): HasMany
    {
        return $this->hasMany(StockTransaction::class)->where('type', 'opname');
    }
}
