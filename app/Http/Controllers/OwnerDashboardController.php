<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OwnerDashboardController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'chart');

        if ($tab === 'voucher') {
            $vouchers = Voucher::latest()->paginate(15);
            return view('owner.dashboard', compact('tab', 'vouchers'));
        }

        $from = $request->query('from');
        $to = $request->query('to');
        $hasFilter = $from || $to;

        $totalProducts = Product::withoutGlobalScopes()->count();
        $lowStockProducts = Product::withoutGlobalScopes()->lowStock()->count();

        $branches = Branch::active()->with('businessTypes')->get()->map(function ($branch) use ($from, $to, $hasFilter) {
            $query = Sale::withoutGlobalScopes()->where('branch_id', $branch->id);
            $expenseQuery = Expense::withoutGlobalScopes()->where('branch_id', $branch->id);

            if ($hasFilter) {
                $query->dateRange($from, $to);
                $expenseQuery->dateRange($from, $to, 'expense_date');
            } else {
                $query->whereDate('created_at', today());
                $expenseQuery->whereDate('expense_date', today());
            }

            $salesTotal = (clone $query)->sum('total');
            $expensesTotal = (clone $expenseQuery)->sum('amount');

            $profit = SaleItem::withoutGlobalScopes()
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->join('products', 'sale_items.product_id', '=', 'products.id')
                ->where('sales.branch_id', $branch->id)
                ->when($hasFilter, function ($q) use ($from, $to) {
                    $q->whereDate('sales.created_at', '>=', $from)
                      ->whereDate('sales.created_at', '<=', $to);
                }, function ($q) {
                    $q->whereDate('sales.created_at', today());
                })
                ->select(DB::raw('SUM((sale_items.price - COALESCE(products.cost_price, 0)) * sale_items.quantity) as profit'))
                ->value('profit');

            return [
                'branch' => $branch,
                'sales' => $salesTotal,
                'expenses' => $expensesTotal,
                'profit' => $profit ?? 0,
            ];
        });

        $totals = [
            'sales' => $branches->sum('sales'),
            'expenses' => $branches->sum('expenses'),
            'profit' => $branches->sum('profit'),
        ];

        $chartFrom = $from ?? today()->subDays(6)->toDateString();
        $chartTo = $to ?? today()->toDateString();
        $datePeriod = \Carbon\CarbonPeriod::create($chartFrom, $chartTo);

        $chartLabels = [];
        foreach ($datePeriod as $d) {
            $chartLabels[] = $d->isoFormat('DD MMM');
        }

        $activeBranches = Branch::active()->pluck('name', 'id');

        $branchDailyProfit = DB::table('sale_items')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->join('products', 'sale_items.product_id', '=', 'products.id')
            ->select(DB::raw('DATE(sales.created_at) as date'), 'sales.branch_id',
                DB::raw('SUM((sale_items.price - COALESCE(products.cost_price, 0)) * sale_items.quantity) as profit'))
            ->whereDate('sales.created_at', '>=', $chartFrom)
            ->whereDate('sales.created_at', '<=', $chartTo)
            ->groupBy('date', 'sales.branch_id')
            ->orderBy('date')
            ->get()
            ->groupBy('branch_id');

        $profitBranches = [];
        foreach ($activeBranches as $id => $name) {
            $data = [];
            foreach ($datePeriod as $d) {
                $date = $d->format('Y-m-d');
                $dayData = $branchDailyProfit->get($id);
                $data[] = (float) ($dayData ? $dayData->firstWhere('date', $date)?->profit ?? 0 : 0);
            }
            $profitBranches[] = ['name' => $name, 'data' => $data];
        }

        $profitBranchChartData = [
            'labels' => $chartLabels,
            'profitBranches' => $profitBranches,
        ];

        $branchDailySales = Sale::withoutGlobalScopes()
            ->select(DB::raw('DATE(created_at) as date'), 'branch_id', DB::raw('SUM(total) as total'))
            ->whereDate('created_at', '>=', $chartFrom)
            ->whereDate('created_at', '<=', $chartTo)
            ->groupBy('date', 'branch_id')
            ->orderBy('date')
            ->get()
            ->groupBy('branch_id');

        $branchChartBranches = [];
        foreach ($activeBranches as $id => $name) {
            $branchData = [];
            foreach ($datePeriod as $d) {
                $date = $d->format('Y-m-d');
                $dayData = $branchDailySales->get($id);
                $branchData[] = (float) ($dayData ? $dayData->firstWhere('date', $date)?->total ?? 0 : 0);
            }
            $branchChartBranches[] = ['name' => $name, 'data' => $branchData];
        }

        $branchChartData = [
            'labels' => $chartLabels,
            'branches' => $branchChartBranches,
        ];

        $branchTopProducts = Branch::active()->get()->map(function ($branch) use ($chartFrom, $chartTo) {
            $products = SaleItem::withoutGlobalScopes()
                ->select('product_id', 'product_name',
                    DB::raw('SUM(quantity) as total_qty'),
                    DB::raw('SUM(subtotal) as total_revenue'))
                ->whereHas('sale', function ($q) use ($branch, $chartFrom, $chartTo) {
                    $q->withoutGlobalScopes()
                      ->where('branch_id', $branch->id)
                      ->whereDate('created_at', '>=', $chartFrom)
                      ->whereDate('created_at', '<=', $chartTo);
                })
                ->groupBy('product_id', 'product_name')
                ->orderByDesc('total_qty')
                ->limit(3)
                ->get()
                ->toArray();

            return [
                'branch' => $branch->name,
                'products' => $products,
            ];
        });

        return view('owner.dashboard', compact(
            'tab', 'branches', 'totals', 'totalProducts', 'lowStockProducts',
            'hasFilter', 'from', 'to',
            'profitBranchChartData', 'branchChartData', 'branchTopProducts',
        ));
    }
}
