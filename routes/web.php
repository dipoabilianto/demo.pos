<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BranchController;
use App\Http\Controllers\BusinessTypeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OwnerDashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RawMaterialController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VoucherController;
use App\Models\Branch;
use App\Models\Expense;
use App\Models\Order;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Sale;
use App\Models\StockTransaction;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:login');
    Route::get('/login/captcha', [AuthController::class, 'captchaForm'])->name('login.captcha');
    Route::post('/login/captcha', [AuthController::class, 'verifyCaptcha'])->name('login.captcha.verify');
    Route::get('/login/2fa', [AuthController::class, 'verify2faForm'])->name('login.2fa');
    Route::post('/login/2fa', [AuthController::class, 'verify2fa'])->name('login.2fa.verify')->middleware('throttle:10,60');

    Route::get('/forgot-password', [AuthController::class, 'forgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'resetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

Route::post('payments/webhook/xendit', [PaymentController::class, 'webhook'])->name('payments.webhook.xendit');

Route::get('/api/products/batch', function (Request $request) {
    $ids = $request->query('ids', '');
    $ids = array_filter(explode(',', $ids));
    if (empty($ids)) {
        return response()->json([]);
    }

    return Product::whereIn('id', $ids)->where('is_active', true)->get(['id', 'name', 'price', 'sale_price', 'stock', 'is_unlimited', 'is_sold_out']);
})->withoutMiddleware([VerifyCsrfToken::class]);

Route::get('/api/products/search', function (Request $request) {
    $q = $request->query('q', '');
    if (strlen($q) < 1) {
        return response()->json([]);
    }

    return Product::where('is_active', true)
        ->where(function ($query) use ($q) {
            $query->where('name', 'like', "%{$q}%")
                ->orWhere('sku', 'like', "%{$q}%");
        })
        ->limit(20)
        ->get(['id', 'name', 'sku', 'price', 'sale_price', 'stock', 'is_unlimited', 'is_sold_out']);
})->withoutMiddleware([VerifyCsrfToken::class]);

Route::prefix('orders')->name('orders.')->group(function () {
    Route::get('/', [OrderController::class, 'catalog'])->name('catalog')->middleware('auth');
    Route::get('/public', [OrderController::class, 'publicCatalog'])->name('public-catalog');
    Route::post('/public/store', [OrderController::class, 'publicStore'])->name('public-store');
    Route::get('/public/check-voucher', [OrderController::class, 'checkVoucher'])->name('public.check-voucher');
    Route::get('/public/{order:public_token}/payment', [OrderController::class, 'publicPayment'])->name('public-payment');
    Route::get('/checkout', [OrderController::class, 'checkout'])->name('checkout')->middleware('auth');
    Route::post('/', [OrderController::class, 'store'])->name('store')->middleware('auth');
    Route::get('/{order}/payment', [OrderController::class, 'payment'])->name('payment')->middleware('auth');
    Route::post('/{order}/invoice', [OrderController::class, 'createInvoice'])->name('invoice')->middleware(['auth', 'throttle:10,60']);
    Route::get('/public/status/{xenditId}', [OrderController::class, 'publicStatus'])->name('public-status');
    Route::get('/public/{order:public_token}/receipt', [OrderController::class, 'publicReceipt'])->name('public-receipt');
});

Route::get('/api/orders/latest', function (\Illuminate\Http\Request $request) {
    $since = $request->input('since');
    $query = \App\Models\Order::where(function ($q) {
        $q->whereIn('payment_status', ['paid', 'success'])
          ->orWhere(function ($q) {
              $q->whereIn('payment_method', ['cash', 'transfer'])
                ->where('payment_status', 'pending');
          });
    });
    if ($since) {
        $ts = \Carbon\Carbon::parse($since);
        $query->where(function ($q) use ($ts) {
            $q->where('updated_at', '>', $ts)
              ->orWhere('created_at', '>', $ts);
        });
    }
    $orders = $query->latest('created_at')->take(10)->get()->reverse()->values();
    return response()->json([
        'orders' => $orders->map(fn ($o) => [
            'id' => $o->id,
            'order_number' => $o->order_number,
            'customer_name' => $o->customer_name,
            'total' => (int) $o->total,
            'item_count' => $o->items()->count(),
            'created_at' => $o->created_at->diffForHumans(),
            'type' => $o->order_status === 'pending' && $o->payment_status === 'pending'
                ? 'new_order'
                : ($o->payment_status === 'pending' && in_array($o->payment_method, ['cash', 'transfer'])
                    ? 'payment_pending'
                    : 'paid'),
        ]),
        'server_time' => now()->toIso8601String(),
    ]);
})->name('api.orders.latest')->middleware('auth');

Route::middleware(['auth'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/owner/dashboard', [OwnerDashboardController::class, 'index'])
        ->name('owner.dashboard')
        ->middleware('role:superadmin,owner');

    Route::get('products', [ProductController::class, 'index'])->name('products.index')->middleware('role:permission:products.view');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create')->middleware('role:permission:products.create');
    Route::post('products', [ProductController::class, 'store'])->name('products.store')->middleware('role:permission:products.create');
    Route::get('products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit')->middleware('role:permission:products.edit');
    Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update')->middleware('role:permission:products.edit');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy')->middleware('role:permission:products.delete');
    Route::post('products/{product}/toggle-sold', [ProductController::class, 'toggleSoldOut'])->name('products.toggle-sold');

    Route::get('categories', [CategoryController::class, 'index'])->name('categories.index')->middleware('role:permission:categories.view');
    Route::post('categories', [CategoryController::class, 'store'])->name('categories.store')->middleware('role:permission:categories.create');
    Route::put('categories/{category}', [CategoryController::class, 'update'])->name('categories.update')->middleware('role:permission:categories.edit');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy')->middleware('role:permission:categories.delete');

    Route::get('/sales/create', function () {
        return redirect()->route('orders.catalog');
    })->name('sales.create');
    Route::get('sales', [SaleController::class, 'index'])->name('sales.index')->middleware('role:permission:sales.view');
    Route::post('sales', [SaleController::class, 'store'])->name('sales.store')->middleware('role:permission:sales.create');
    Route::get('sales/{sale}', [SaleController::class, 'show'])->name('sales.show')->middleware('role:permission:sales.view');

    Route::get('expenses', [ExpenseController::class, 'index'])->name('expenses.index')->middleware('role:permission:expenses.view');
    Route::get('expenses/create', [ExpenseController::class, 'create'])->name('expenses.create')->middleware('role:permission:expenses.create');
    Route::post('expenses', [ExpenseController::class, 'store'])->name('expenses.store')->middleware('role:permission:expenses.create');
    Route::get('expenses/{expense}/edit', [ExpenseController::class, 'edit'])->name('expenses.edit')->middleware('role:permission:expenses.edit');
    Route::put('expenses/{expense}', [ExpenseController::class, 'update'])->name('expenses.update')->middleware('role:permission:expenses.edit');
    Route::delete('expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy')->middleware('role:permission:expenses.delete');

    Route::get('raw-materials', [RawMaterialController::class, 'index'])->name('raw-materials.index')->middleware('role:permission:raw-materials.view');
    Route::get('raw-materials/create', [RawMaterialController::class, 'create'])->name('raw-materials.create')->middleware('role:permission:raw-materials.create');
    Route::post('raw-materials', [RawMaterialController::class, 'store'])->name('raw-materials.store')->middleware('role:permission:raw-materials.create');
    Route::get('raw-materials/{raw_material}/edit', [RawMaterialController::class, 'edit'])->name('raw-materials.edit')->middleware('role:permission:raw-materials.edit');
    Route::put('raw-materials/{raw_material}', [RawMaterialController::class, 'update'])->name('raw-materials.update')->middleware('role:permission:raw-materials.edit');
    Route::delete('raw-materials/{raw_material}', [RawMaterialController::class, 'destroy'])->name('raw-materials.destroy')->middleware('role:permission:raw-materials.delete');

    Route::prefix('stock-opname')->name('stock-opname.')->group(function () {
        Route::get('/', fn() => redirect()->route('raw-materials.index', ['tab' => 'opname']))->name('index');
        Route::get('/history', [StockOpnameController::class, 'history'])->name('history')->middleware('role:permission:stock-opname.history');
        Route::get('/adjust/{rawMaterial}', [StockOpnameController::class, 'adjustForm'])->name('adjust-form')->middleware('role:permission:stock-opname.adjust');
        Route::post('/adjust', [StockOpnameController::class, 'adjust'])->name('adjust')->middleware('role:permission:stock-opname.adjust');
    });

    Route::prefix('payments')->name('payments.')->middleware('role:permission:payments.process')->group(function () {
        Route::get('/checkout/{sale}', [PaymentController::class, 'checkout'])->name('checkout');
        Route::post('create-invoice/{sale}', [PaymentController::class, 'createInvoice'])->name('create-invoice');
        Route::get('status/{transaction}', [PaymentController::class, 'status'])->name('status');
    });

    Route::prefix('orders')->name('orders.')->group(function () {
        Route::post('/save', [OrderController::class, 'save'])->name('save');
        Route::get('/saved-list', [OrderController::class, 'savedList'])->name('saved-list');
        Route::get('/history', [OrderController::class, 'history'])->name('history')->middleware('role:permission:orders.view');
        Route::post('/{order}/process', [OrderController::class, 'process'])->name('process')->middleware('role:permission:orders.view');
        Route::post('/{order}/complete', [OrderController::class, 'complete'])->name('complete')->middleware('role:permission:orders.view');
        Route::get('/{order}', [OrderController::class, 'show'])->name('show')->middleware('role:permission:orders.view');
        Route::get('/{order}/receipt/consumer', [ReceiptController::class, 'orderConsumer'])->name('receipt.consumer')->middleware('role:permission:receipts.view');
        Route::get('/{order}/receipt/kitchen', [ReceiptController::class, 'orderKitchen'])->name('receipt.kitchen')->middleware('role:permission:receipts.view');
        Route::post('/{order}/cancel', [OrderController::class, 'cancel'])->name('cancel')->middleware('role:permission:orders.view');
        Route::get('/{id}/items', [OrderController::class, 'getItems'])->name('items')->whereNumber('id')->middleware('role:permission:orders.view');
    });

    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/{sale}/receipt/consumer', [ReceiptController::class, 'saleConsumer'])->name('receipt.consumer')->middleware('role:permission:receipts.view');
        Route::get('/{sale}/receipt/kitchen', [ReceiptController::class, 'saleKitchen'])->name('receipt.kitchen')->middleware('role:permission:receipts.view');
    });

    Route::get('vouchers', [VoucherController::class, 'index'])->name('vouchers.index')->middleware('role:permission:vouchers.view');
    Route::get('vouchers/create', [VoucherController::class, 'create'])->name('vouchers.create')->middleware('role:permission:vouchers.create');
    Route::post('vouchers', [VoucherController::class, 'store'])->name('vouchers.store')->middleware('role:permission:vouchers.create');
    Route::get('vouchers/{voucher}/edit', [VoucherController::class, 'edit'])->name('vouchers.edit')->middleware('role:permission:vouchers.edit');
    Route::put('vouchers/{voucher}', [VoucherController::class, 'update'])->name('vouchers.update')->middleware('role:permission:vouchers.edit');
    Route::delete('vouchers/{voucher}', [VoucherController::class, 'destroy'])->name('vouchers.destroy')->middleware('role:permission:vouchers.delete');

    Route::prefix('users')->name('settings.users.')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('index')->middleware('role:permission:users.view');
        Route::post('/', [UserController::class, 'store'])->name('store')->middleware('role:permission:users.create');
        Route::patch('/{user}', [UserController::class, 'update'])->name('update')->middleware('role:permission:users.edit');
        Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy')->middleware('role:permission:users.delete');
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::get('/', [SettingsController::class, 'general'])->name('general')->middleware('role:permission:settings.view');
        Route::get('/payment', fn () => redirect()->route('settings.general', ['tab' => 'payment']))->name('payment')->middleware('role:permission:settings.view');
        Route::get('/receipt', fn () => redirect()->route('settings.general', ['tab' => 'receipt']))->name('receipt')->middleware('role:permission:settings.view');
        Route::post('/receipt/upload-logo', [SettingsController::class, 'uploadLogo'])->name('receipt.upload-logo')->middleware('role:permission:settings.view');
        Route::post('/upload-promo-image', [SettingsController::class, 'uploadPromoImage'])->name('upload-promo-image')->middleware('role:permission:settings.view');
        Route::post('/upload-favicon', [SettingsController::class, 'uploadFavicon'])->name('upload-favicon')->middleware('role:permission:settings.general');
        Route::post('/upload-notification-sound', [SettingsController::class, 'uploadNotificationSound'])->name('upload-notification-sound')->middleware('role:permission:settings.update');
        Route::post('/upload-qris', [SettingsController::class, 'uploadQRIS'])->name('qris-upload')->middleware('role:permission:settings.payment');
        Route::post('/remove-qris', [SettingsController::class, 'removeQRIS'])->name('qris-remove')->middleware('role:permission:settings.payment');
        Route::post('/update', [SettingsController::class, 'update'])->name('update')->middleware('role:permission:settings.update');
    });

    Route::prefix('settings')->name('settings.')->group(function () {
        Route::prefix('payment-methods')->name('payment-methods.')->group(function () {
            Route::get('/', [PaymentMethodController::class, 'index'])->name('index')->middleware('role:permission:payment-methods.view');
            Route::post('/{paymentMethod}/toggle', [PaymentMethodController::class, 'toggle'])->name('toggle')->middleware('role:permission:payment-methods.toggle');
        });
    });

    Route::prefix('settings')->name('settings.')->middleware('role:permission:security.manage')->group(function () {
        Route::prefix('security')->name('security.')->group(function () {
            Route::get('/2fa/setup', [SettingsController::class, 'twoFactorSetup'])->name('2fa.setup');
            Route::post('/2fa/enable', [SettingsController::class, 'enableTwoFactor'])->name('2fa.enable');
            Route::post('/2fa/disable', [SettingsController::class, 'disableTwoFactor'])->name('2fa.disable');
        });
    });

    Route::prefix('settings')->name('settings.')->middleware('role:permission:settings.view')->group(function () {
        Route::get('/cabang', [BranchController::class, 'index'])->name('cabang');
        Route::post('/cabang/branch', [BranchController::class, 'storeBranch'])->name('cabang.branch.store');
        Route::put('/cabang/branch/{branch}', [BranchController::class, 'updateBranch'])->name('cabang.branch.update');
        Route::delete('/cabang/branch/{branch}', [BranchController::class, 'destroyBranch'])->name('cabang.branch.destroy');
        Route::post('/cabang/business-type', [BusinessTypeController::class, 'store'])->name('cabang.business-type.store');
        Route::put('/cabang/business-type/{businessType}', [BusinessTypeController::class, 'update'])->name('cabang.business-type.update');
        Route::delete('/cabang/business-type/{businessType}', [BusinessTypeController::class, 'destroy'])->name('cabang.business-type.destroy');
        Route::get('/roles', [RoleController::class, 'index'])->name('roles.index');
        Route::post('/roles', [RoleController::class, 'store'])->name('roles.store');
        Route::put('/roles/{role}', [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy');
    });

    Route::prefix('settings')->name('settings.')->middleware('role:permission:settings.view')->group(function () {
        Route::get('/shifts', fn () => redirect()->route('settings.general', ['tab' => 'shifts']))->name('shifts.index');
        Route::get('/shifts/create', [ShiftController::class, 'create'])->name('shifts.create');
        Route::post('/shifts', [ShiftController::class, 'store'])->name('shifts.store');
        Route::get('/shifts/{shift}/edit', [ShiftController::class, 'edit'])->name('shifts.edit');
        Route::put('/shifts/{shift}', [ShiftController::class, 'update'])->name('shifts.update');
        Route::delete('/shifts/{shift}', [ShiftController::class, 'destroy'])->name('shifts.destroy');
        Route::post('/shifts/schedule', [ShiftController::class, 'scheduleStore'])->name('shifts.schedule.store');
        Route::delete('/shifts/schedule/{shiftUser}', [ShiftController::class, 'scheduleDestroy'])->name('shifts.schedule.destroy');
    });

    Route::prefix('attendances')->name('attendances.')->middleware('auth')->group(function () {
        Route::get('/', [AttendanceController::class, 'index'])->name('index');
        Route::get('/current', [AttendanceController::class, 'current'])->name('current');
        Route::post('/check-in', [AttendanceController::class, 'checkIn'])->name('check-in');
        Route::post('/check-out', [AttendanceController::class, 'checkOut'])->name('check-out');
        Route::get('/report', [AttendanceController::class, 'report'])->name('report');
    });

    Route::prefix('reports')->name('reports.')->middleware('role:permission:reports.view')->group(function () {
        Route::get('/', function (Request $request) {
            $tab = $request->tab ?? 'sales';
            $stockSub = $request->stock_sub ?? 'current';
            $perPage = 50;

            $salesQuery = Sale::with('items')
                ->dateRange($request->from, $request->to)
                ->when($request->payment_method, fn($q, $v) => $q->where('payment_method', $v));

            $expensesQuery = Expense::query()
                ->dateRange($request->from, $request->to, 'expense_date')
                ->when($request->category, fn($q, $v) => $q->where('category', $v));

            $sales = match ($tab) {
                'sales' => (clone $salesQuery)->latest()->paginate($perPage),
                'financial' => (clone $salesQuery)->latest()->get(),
                default => collect(),
            };

            $expenses = match ($tab) {
                'expenses' => (clone $expensesQuery)->latest('expense_date')->paginate($perPage),
                'financial' => (clone $expensesQuery)->latest('expense_date')->get(),
                default => collect(),
            };

            $salesTotal = (clone $salesQuery)->selectRaw('COALESCE(SUM(total), 0) as total, COALESCE(SUM(discount), 0) as discount, COALESCE(SUM(tax), 0) as tax, COUNT(*) as count')->first();
            $expensesTotal = (clone $expensesQuery)->selectRaw('COALESCE(SUM(amount), 0) as total, COUNT(*) as count')->first();

            $categories = Expense::distinct('category')->whereNotNull('category')->pluck('category');
            $materials = RawMaterial::orderBy('name')->get();

            $stockTransactions = collect();
            if ($stockSub === 'mutasi' && $request->material_id) {
                $stockTransactions = StockTransaction::where('raw_material_id', $request->material_id)
                    ->when($request->from, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
                    ->when($request->to, fn($q, $v) => $q->whereDate('created_at', '<=', $v))
                    ->latest()->get();
            }

            $opnameQuery = StockTransaction::with('rawMaterial', 'user')->where('type', 'opname');
            if ($request->from) $opnameQuery->whereDate('created_at', '>=', $request->from);
            if ($request->to) $opnameQuery->whereDate('created_at', '<=', $request->to);
            if ($request->material_id) $opnameQuery->where('raw_material_id', $request->material_id);
            $opnameTransactions = $opnameQuery->latest()->get();

            $periods = collect();
            $group = $request->group;
            if ($group && $tab === 'financial') {
                $allSales = $sales;
                $allExpenses = $expenses;
                $allKeys = $allSales->pluck('created_at')->merge($allExpenses->pluck('expense_date'))->filter()->map(fn($d) => match ($group) {
                    'day' => $d instanceof \Carbon\Carbon ? $d->format('Y-m-d') : \Carbon\Carbon::parse($d)->format('Y-m-d'),
                    'week' => $d instanceof \Carbon\Carbon ? $d->format('o-W') : \Carbon\Carbon::parse($d)->format('o-W'),
                    'month' => $d instanceof \Carbon\Carbon ? $d->format('Y-m') : \Carbon\Carbon::parse($d)->format('Y-m'),
                    default => 'all',
                })->unique()->sort()->values();
                foreach ($allKeys as $key) {
                    $gSales = $allSales->filter(fn($s) => match ($group) {
                        'day' => $s->created_at->format('Y-m-d') === $key,
                        'week' => $s->created_at->format('o-W') === $key,
                        'month' => $s->created_at->format('Y-m') === $key,
                        default => true,
                    });
                    $gExpenses = $allExpenses->filter(fn($e) => match ($group) {
                        'day' => $e->expense_date->format('Y-m-d') === $key,
                        'week' => $e->expense_date->format('o-W') === $key,
                        'month' => $e->expense_date->format('Y-m') === $key,
                        default => true,
                    });
                    $rev = $gSales->sum('total');
                    $hpp = $gSales->sum(fn($s) => $s->items->sum(fn($i) => ($i->product ? $i->product->cost_price : 0) * $i->quantity));
                    $expAmt = $gExpenses->sum('amount');
                    $periods->push(['label' => $key, 'revenue' => $rev, 'hpp' => $hpp, 'laba_kotor' => $rev - $hpp, 'expenses' => $expAmt, 'laba_bersih' => $rev - $hpp - $expAmt]);
                }
            }

            return view('reports.index', compact(
                'tab', 'stockSub', 'sales', 'expenses', 'categories',
                'materials', 'stockTransactions', 'opnameTransactions', 'periods',
                'salesTotal', 'expensesTotal'
            ));
        })->name('index');

        Route::get('/sales/print', function (Request $request) {
            $sales = Sale::with('items.product')
                ->dateRange($request->from, $request->to)
                ->when($request->payment_method, fn($q, $v) => $q->where('payment_method', $v))
                ->latest()->get();
            $settings = SettingsController::getSettings();
            return view('reports.sales.print', compact('sales', 'settings'));
        })->name('sales.print')->middleware('role:permission:reports.sales');

        Route::get('/expenses/print', function (Request $request) {
            $expenses = Expense::query()
                ->dateRange($request->from, $request->to, 'expense_date')
                ->when($request->category, fn($q, $v) => $q->where('category', $v))
                ->latest('expense_date')->get();
            $settings = SettingsController::getSettings();
            return view('reports.expenses.print', compact('expenses', 'settings'));
        })->name('expenses.print')->middleware('role:permission:reports.expenses');

        Route::get('/stock/print', function (Request $request) {
            $materials = RawMaterial::orderBy('name')->get();
            $settings = SettingsController::getSettings();
            $transactions = collect();
            if ($request->material_id) {
                $transactions = StockTransaction::where('raw_material_id', $request->material_id)
                    ->when($request->from, fn($q, $v) => $q->whereDate('created_at', '>=', $v))
                    ->when($request->to, fn($q, $v) => $q->whereDate('created_at', '<=', $v))
                    ->latest()->get();
            }
            return view('reports.stock.print', compact('materials', 'transactions', 'settings'));
        })->name('stock.print')->middleware('role:permission:reports.stock');

        Route::get('/financial/print', function (Request $request) {
            $from = $request->from;
            $to = $request->to;
            $group = $request->group;
            $sales = Sale::with('items.product')->dateRange($from, $to)->latest()->get();
            $expenses = Expense::query()->dateRange($from, $to, 'expense_date')->latest('expense_date')->get();
            $periods = collect();
            if ($group && $sales->count() > 0) {
                $groupedSales = match ($group) {
                    'day' => $sales->groupBy(fn($s) => $s->created_at->format('Y-m-d')),
                    'week' => $sales->groupBy(fn($s) => $s->created_at->format('o-W')),
                    'month' => $sales->groupBy(fn($s) => $s->created_at->format('Y-m')),
                    default => collect([$sales]),
                };
                $groupedExpenses = match ($group) {
                    'day' => $expenses->groupBy(fn($e) => $e->expense_date->format('Y-m-d')),
                    'week' => $expenses->groupBy(fn($e) => $e->expense_date->format('o-W')),
                    'month' => $expenses->groupBy(fn($e) => $e->expense_date->format('Y-m')),
                    default => collect([$expenses]),
                };
                $allKeys = $groupedSales->keys()->merge($groupedExpenses->keys())->unique()->sort();
                foreach ($allKeys as $key) {
                    $gSales = $groupedSales->get($key, collect());
                    $gExpenses = $groupedExpenses->get($key, collect());
                    $rev = $gSales->sum('total');
                    $hpp = $gSales->sum(fn($s) => $s->items->sum(fn($i) => ($i->product ? $i->product->cost_price : 0) * $i->quantity));
                    $expAmt = $gExpenses->sum('amount');
                    $periods->push(['label' => $key, 'revenue' => $rev, 'hpp' => $hpp, 'laba_kotor' => $rev - $hpp, 'expenses' => $expAmt, 'laba_bersih' => $rev - $hpp - $expAmt]);
                }
            }
            $settings = SettingsController::getSettings();
            return view('reports.financial.print', compact('sales', 'expenses', 'periods', 'settings'));
        })->name('financial.print')->middleware('role:permission:reports.financial');

        Route::get('/raw-materials/print', function (Request $request) {
            $materials = RawMaterial::orderBy('name')->get();
            $settings = SettingsController::getSettings();
            $lowStockCount = $materials->filter(fn($m) => $m->isLowStock())->count();
            $totalStock = $materials->sum('current_stock');
            return view('reports.raw-materials.print', compact('materials', 'settings', 'lowStockCount', 'totalStock'));
        })->name('raw-materials.print')->middleware('role:permission:reports.raw-materials');

        Route::get('/stock-opname/print', function (Request $request) {
            $query = StockTransaction::with('rawMaterial', 'user')->where('type', 'opname');
            if ($request->from) $query->whereDate('created_at', '>=', $request->from);
            if ($request->to) $query->whereDate('created_at', '<=', $request->to);
            if ($request->material_id) $query->where('raw_material_id', $request->material_id);
            $transactions = $query->latest()->get();
            $settings = SettingsController::getSettings();
            return view('reports.stock-opname.print', compact('transactions', 'settings'));
        })->name('stock-opname.print')->middleware('role:permission:reports.stock-opname');
    });

    Route::get('/switch-branch/{branch}', function (Branch $branch) {
        session()->put('branch_id', $branch->id);
        $branch->load('businessTypes');
        if ($branch->businessTypes->isNotEmpty()) {
            session()->put('business_type_id', $branch->businessTypes->first()->id);
        }
        session()->regenerate();
        return back()->with('success', 'Beralih ke cabang '.$branch->name);
    })->name('switch-branch')->middleware('role:superadmin,owner');
});
