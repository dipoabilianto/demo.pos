<?php

namespace App\Models;

use App\Models\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Order extends Model
{
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    protected $fillable = [
        'order_number', 'customer_name', 'customer_phone', 'customer_email',
        'shipping_address', 'payment_method', 'payment_status', 'order_status',
        'subtotal', 'shipping_cost', 'discount', 'tax', 'total', 'notes',
        'voucher_id', 'voucher_code', 'seen_at', 'user_id', 'public_token',
        'processed_by', 'processed_at', 'branch_id', 'business_type_id',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'discount' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function transaction(): MorphOne
    {
        return $this->morphOne(Transaction::class, 'transactionable');
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function scopeUnprocessed($query)
    {
        return $query->whereNull('processed_by');
    }

    public function scopeProcessing($query)
    {
        return $query->whereNotNull('processed_by')->where('order_status', '!=', 'completed');
    }

    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope);

        static::creating(function (Order $order) {
            if (!$order->order_number) {
                $order->order_number = static::generateOrderNumber('ORDOF');
            }
            if (!$order->public_token) {
                $order->public_token = (string) Str::uuid();
            }
            if (!$order->branch_id && session()->has('branch_id')) {
                $order->branch_id = session('branch_id');
            }
        });

        static::updated(function (Order $order) {
            if ($order->wasChanged('order_status') && $order->order_status === 'cancelled') {
                $order->loadMissing('items.product');
                foreach ($order->items as $item) {
                    if ($item->product && !$item->product->isUnlimited()) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }
            }
        });
    }

    public static function generateOrderNumber(string $prefix): string
    {
        $datePrefix = now()->format('Ymd');

        return DB::transaction(function () use ($prefix, $datePrefix) {
            $row = DB::table('order_sequences')
                ->where('prefix', $prefix)
                ->where('date_prefix', $datePrefix)
                ->lockForUpdate()
                ->first();

            if (! $row) {
                $seq = $prefix === 'ORDON' ? 6001 : 1;
                DB::table('order_sequences')->insert([
                    'prefix' => $prefix,
                    'date_prefix' => $datePrefix,
                    'last_sequence' => $seq,
                ]);
            } else {
                $seq = $row->last_sequence + 1;
                DB::table('order_sequences')
                    ->where('prefix', $prefix)
                    ->where('date_prefix', $datePrefix)
                    ->update(['last_sequence' => $seq]);
            }

            return sprintf('%s-%s-%04d', $prefix, $datePrefix, $seq);
        });
    }

    public function transitionStatus(string $status): void
    {
        $valid = [
            'pending' => ['pending', 'confirmed', 'processing', 'cancelled'],
            'confirmed' => ['processing', 'cancelled'],
            'processing' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];

        $current = $this->order_status ?? 'pending';

        if (! in_array($status, $valid[$current] ?? [])) {
            throw new \InvalidArgumentException("Cannot transition from {$current} to {$status}.");
        }

        $this->update(['order_status' => $status]);
    }

    public static function previewOrderNumber(string $prefix): string
    {
        $datePrefix = now()->format('Ymd');
        $row = DB::table('order_sequences')
            ->where('prefix', $prefix)
            ->where('date_prefix', $datePrefix)
            ->first();
        $seq = $row ? $row->last_sequence + 1 : ($prefix === 'ORDON' ? 6001 : 1);
        return sprintf('%s-%s-%04d', $prefix, $datePrefix, $seq);
    }
}