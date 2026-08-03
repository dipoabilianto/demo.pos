<?php

namespace App\Models;

use App\Models\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'type', 'value', 'min_order', 'max_discount',
        'max_uses', 'max_uses_per_user', 'used_count', 'starts_at',
        'expires_at', 'is_active', 'created_by', 'branch_id',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope);
    }

    protected function casts(): array
    {
        return [
            'value' => 'decimal:2',
            'min_order' => 'decimal:2',
            'max_discount' => 'decimal:2',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class);
    }

    public function isValidFor(float $subtotal, ?string $customerIdentifier = null): bool
    {
        if (!$this->is_active) return false;

        if ($this->starts_at && now()->lt($this->starts_at)) return false;
        if ($this->expires_at && now()->gt($this->expires_at)) return false;

        if ($this->max_uses > 0 && $this->used_count >= $this->max_uses) return false;

        if ($subtotal < $this->min_order) return false;

        if ($customerIdentifier && $this->max_uses_per_user > 0) {
            $userUsage = $this->usages()
                ->where('customer_identifier', $customerIdentifier)
                ->count();
            if ($userUsage >= $this->max_uses_per_user) return false;
        }

        return true;
    }

    public function calculateDiscount(float $subtotal): float
    {
        if ($this->type === 'percentage') {
            $discount = $subtotal * $this->value / 100;
            if ($this->max_discount) {
                $discount = min($discount, (float) $this->max_discount);
            }
            return round($discount, 2);
        }

        return min((float) $this->value, $subtotal);
    }

    public function markUsed(Order $order, string $customerIdentifier): void
    {
        DB::transaction(function () use ($order, $customerIdentifier) {
            $updated = Voucher::where('id', $this->id)
                ->where(function ($q) {
                    $q->where('max_uses', 0)
                      ->orWhere('used_count', '<', DB::raw('max_uses'));
                })
                ->increment('used_count');

            if ($updated === 0) {
                throw new \RuntimeException('Voucher sudah habis digunakan.');
            }

            $this->usages()->create([
                'order_id' => $order->id,
                'customer_identifier' => $customerIdentifier,
            ]);
        });
    }

    public function scopeValid($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            });
    }

    public static function generateCode(): string
    {
        $prefix = 'VCH-';
        return $prefix . strtoupper(\Str::random(8));
    }
}
