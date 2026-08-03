<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

trait HasPeriodQueries
{
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year);
    }

    public function scopeDateRange(Builder $query, ?string $from, ?string $to, string $column = 'created_at'): Builder
    {
        if ($from) {
            $query->whereDate($column, '>=', $from);
        }
        if ($to) {
            $query->whereDate($column, '<=', $to);
        }
        return $query;
    }

    public static function sumToday(string $column = 'total'): int|float
    {
        return static::query()->today()->sum($column);
    }

    public static function sumThisMonth(string $column = 'total'): int|float
    {
        return static::query()->thisMonth()->sum($column);
    }

    public static function sumAllTime(string $column = 'total'): int|float
    {
        return static::sum($column);
    }
}