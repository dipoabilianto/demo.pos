<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class BranchScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (!app()->bound('session.store')) {
            return;
        }

        $branchId = app('session.store')->get('branch_id');

        if ($branchId) {
            $builder->where(function ($q) use ($branchId) {
                $q->where('branch_id', $branchId)
                  ->orWhereNull('branch_id');
            });
        }
    }
}
