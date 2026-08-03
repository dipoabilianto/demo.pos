<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::with('category');

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%")
                  ->orWhere('sku', 'like', "%{$request->search}%");
        }
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->stock_status === 'low') {
            $query->lowStock();
        }

        $products = $query->latest()->paginate(15);
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'sku' => 'nullable|string|max:50|unique:products',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'is_unlimited' => 'nullable|boolean',
            'stock' => 'nullable|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if (($validated['sale_price'] ?? null) && $validated['sale_price'] >= $validated['price']) {
            return back()->withErrors('Harga sale harus lebih kecil dari harga jual.')->withInput();
        }
        if (($validated['cost_price'] ?? null) && $validated['cost_price'] > $validated['price']) {
            return back()->withErrors('Harga modal tidak boleh melebihi harga jual.')->withInput();
        }

        $validated['is_unlimited'] = $validated['is_unlimited'] ?? false;
        if ($validated['is_unlimited']) {
            $validated['stock'] = 0;
        }
        $validated['stock'] ??= 0;
        $validated['branch_id'] = session('branch_id');

        $validated['image'] = $this->handleImage($request);

        Product::create($validated);

        return redirect()->route('products.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit(Product $product): View
    {
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'sku' => 'nullable|string|max:50|unique:products,sku,' . $product->id,
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'is_unlimited' => 'nullable|boolean',
            'stock' => 'nullable|integer|min:0',
            'min_stock' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ]);

        if (($validated['sale_price'] ?? null) && $validated['sale_price'] >= $validated['price']) {
            return back()->withErrors('Harga sale harus lebih kecil dari harga jual.')->withInput();
        }
        if (($validated['cost_price'] ?? null) && $validated['cost_price'] > $validated['price']) {
            return back()->withErrors('Harga modal tidak boleh melebihi harga jual.')->withInput();
        }

        $validated['is_unlimited'] = $validated['is_unlimited'] ?? false;
        if ($validated['is_unlimited']) {
            $validated['stock'] = 0;
        }
        $validated['stock'] ??= 0;

        $image = $this->handleImage($request);
        if ($image) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $image;
        }

        $product->update($validated);

        return redirect()->route('products.index')->with('success', 'Produk berhasil diperbarui.');
    }

    private function handleImage(Request $request): ?string
    {
        if ($request->hasFile('image')) {
            return $request->file('image')->store('products', 'public');
        }

        if ($request->filled('image_data')) {
            $data = $request->input('image_data');
            if (str_starts_with($data, 'data:image')) {
                $data = substr($data, strpos($data, ',') + 1);
            }
            $decoded = base64_decode($data, true);
            if ($decoded === false) return null;

            $f = finfo_open();
            $mime = finfo_buffer($f, $decoded, FILEINFO_MIME_TYPE);
            finfo_close($f);

            if (! in_array($mime, ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
                return null;
            }

            $filename = 'products/' . uniqid() . '.' . match ($mime) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                default => 'webp',
            };
            Storage::disk('public')->put($filename, $decoded);
            return $filename;
        }

        return null;
    }

    public function toggleSoldOut(Product $product): JsonResponse
    {
        $product->update(['is_sold_out' => !$product->is_sold_out]);
        return response()->json([
            'is_sold_out' => $product->fresh()->is_sold_out,
        ]);
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->update(['is_active' => false]);
        return redirect()->route('products.index')->with('success', 'Produk berhasil dinonaktifkan.');
    }
}