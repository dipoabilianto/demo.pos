<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Order;
use App\Models\Sale;
use App\Models\Voucher;
use App\Services\SettingService;
use Illuminate\View\View;

class ReceiptController extends Controller
{
    public function __construct(
        private SettingService $settingService,
    ) {}
    public function orderConsumer(Order $order): View
    {
        $order->load('items.product', 'voucher', 'transaction', 'user');
        $settings = $this->settingService->getSettings();
        $branch = Branch::find(session('branch_id'));
        return view('receipts.consumer', compact('order', 'settings', 'branch'));
    }

    public function orderKitchen(Order $order): View
    {
        $order->load('items.product');
        $settings = $this->settingService->getSettings();
        $branch = Branch::find(session('branch_id'));
        return view('receipts.kitchen', compact('order', 'settings', 'branch'));
    }

    public function saleConsumer(Sale $sale): View
    {
        $sale->load('items', 'voucher');
        $order = new Order();
        $order->order_number = $sale->invoice_number;
        $order->customer_name = 'Walk-in Customer';
        $order->subtotal = $sale->subtotal;
        $order->discount = $sale->discount ?? 0;
        $order->tax = $sale->tax ?? 0;
        $order->total = $sale->total;
        $order->payment_method = $sale->payment_method;
        $order->notes = $sale->notes;
        $order->created_at = $sale->created_at;
        $order->voucher_code = $sale->voucher_code;
        $order->voucher = $sale->voucher;
        $order->items = $sale->items->map(function ($item) {
            $i = new Order();
            $i->quantity = $item->quantity;
            $i->product_name = $item->product_name;
            $i->price = $item->price;
            $i->subtotal = $item->subtotal;
            $i->product = $item->product;
            return $i;
        });

        $settings = $this->settingService->getSettings();
        $branch = Branch::find(session('branch_id'));
        return view('receipts.consumer', compact('order', 'settings', 'branch'));
    }

    public function saleKitchen(Sale $sale): View
    {
        $sale->load('items', 'voucher');
        $order = new Order();
        $order->order_number = $sale->invoice_number;
        $order->customer_name = 'Walk-in Customer';
        $order->created_at = $sale->created_at;
        $order->notes = $sale->notes;
        $order->items = $sale->items->map(function ($item) {
            $i = new Order();
            $i->quantity = $item->quantity;
            $i->product_name = $item->product_name;
            $i->price = $item->price;
            $i->subtotal = $item->subtotal;
            $i->product = $item->product;
            return $i;
        });

        $settings = $this->settingService->getSettings();
        $branch = Branch::find(session('branch_id'));
        return view('receipts.kitchen', compact('order', 'settings', 'branch'));
    }
}