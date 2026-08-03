<?php

namespace App\Models;

use App\Concerns\HasPeriodQueries;
use App\Models\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Expense extends Model
{
    use HasPeriodQueries;

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    protected $fillable = [
        'title', 'description', 'amount', 'category', 'expense_date', 'branch_id',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope);
    }

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'expense_date' => 'date',
        ];
    }

    public function transaction(): MorphOne
    {
        return $this->morphOne(Transaction::class, 'transactionable');
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('expense_date', today());
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('expense_date', now()->month)->whereYear('expense_date', now()->year);
    }

    public function stockTransactions(): HasMany
    {
        return $this->hasMany(StockTransaction::class);
    }
}