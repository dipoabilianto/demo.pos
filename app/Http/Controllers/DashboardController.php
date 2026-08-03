<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $from = $request->query('from');
        $to = $request->query('to');
        $hasFilter = $from || $to;

        $totalProducts = Product::count();
        $lowStockProducts = Product::lowStock()->count();

        if ($hasFilter) {
            $periodSales = Sale::dateRange($from, $to)->sum('total');
            $periodExpenses = Expense::dateRange($from, $to, 'expense_date')->sum('amount');
            $recentSales = Sale::with('items')->dateRange($from, $to)->latest()->take(10)->get();
            $recentExpenses = Expense::dateRange($from, $to, 'expense_date')->latest()->take(5)->get();
        } else {
            $periodSales = Sale::sumToday();
            $periodExpenses = Expense::sumToday('amount');
            $recentSales = Sale::with('items')->latest()->take(10)->get();
            $recentExpenses = Expense::latest()->take(5)->get();
        }

        return view('dashboard.index', compact(
            'periodSales', 'periodExpenses', 'totalProducts', 'lowStockProducts',
            'recentSales', 'recentExpenses', 'hasFilter', 'from', 'to'
        ));
    }
}