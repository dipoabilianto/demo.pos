<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Scope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ProductBranchScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        if (!app()->bound('session.store')) {
            return;
        }

        $branchId = app('session.store')->get('branch_id');

        if ($branchId) {
            $builder->where(function ($query) use ($branchId) {
                $query->where('products.branch_id', $branchId)
                      ->orWhereHas('branches', function ($q) use ($branchId) {
                          $q->where('branch_product.branch_id', $branchId);
                      });
            });
        }
    }
}
