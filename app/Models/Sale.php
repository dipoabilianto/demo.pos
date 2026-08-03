<?php

namespace App\Models;

use App\Concerns\HasPeriodQueries;
use App\Models\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Sale extends Model
{
    use HasFactory, HasPeriodQueries;

    protected $fillable = [
        'invoice_number', 'subtotal', 'discount', 'tax', 'total',
        'paid_amount', 'change_amount', 'payment_method', 'payment_status',
        'notes', 'voucher_id', 'voucher_code', 'branch_id', 'business_type_id',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'change_amount' => 'decimal:2',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function transaction(): MorphOne
    {
        return $this->morphOne(Transaction::class, 'transactionable');
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope);

        static::creating(function (Sale $sale) {
            $sale->invoice_number = 'INV-' . now()->format('Ymd') . '-' . strtoupper(\Str::random(6));
        });
    }
}