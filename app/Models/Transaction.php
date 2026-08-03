<?php

namespace App\Models;

use App\Models\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Transaction extends Model
{
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
    protected $fillable = [
        'xendit_id', 'type',
        'transactionable_type', 'transactionable_id',
        'payment_channel', 'status',
        'amount', 'external_id', 'xendit_response', 'branch_id',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope);
    }

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'xendit_response' => 'array',
        ];
    }

    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }
}