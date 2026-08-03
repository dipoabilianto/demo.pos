<?php

namespace App\Models;

use App\Concerns\HasLowStockScope;
use App\Models\Scopes\ProductBranchScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory, HasLowStockScope;

    protected $fillable = [
        'category_id', 'name', 'sku', 'description',
        'price', 'sale_price', 'cost_price', 'stock', 'is_unlimited', 'is_sold_out', 'min_stock', 'image', 'is_active',
        'branch_id',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new ProductBranchScope);
    }

    public function branches(): BelongsToMany
    {
        return $this->belongsToMany(Branch::class);
    }

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_unlimited' => 'boolean',
            'is_sold_out' => 'boolean',
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'cost_price' => 'decimal:2',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function getImageUrlAttribute(): string
    {
        if (!$this->image) return '';
        if (str_starts_with($this->image, 'http://') || str_starts_with($this->image, 'https://')) {
            return $this->image;
        }
        return Storage::disk('public')->url($this->image);
    }

    public function isUnlimited(): bool
    {
        return $this->is_unlimited;
    }
}