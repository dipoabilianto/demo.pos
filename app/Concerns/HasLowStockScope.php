<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasLowStockScope
{
    public function scopeLowStock(Builder $query): Builder
    {
        return $query->where('is_unlimited', false)->whereColumn('stock', '<=', 'min_stock')->where('is_active', true);
    }

    public function isLowStock(): bool
    {
        return ! $this->is_unlimited && $this->stock <= $this->min_stock && $this->is_active;
    }
}