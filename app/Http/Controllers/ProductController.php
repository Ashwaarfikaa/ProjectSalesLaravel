<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'q'          => ['nullable', 'string', 'max:100'],
            'category'   => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        $products = Product::with('category')
            ->when($filters['q'] ?? null, function ($query, $q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('sku', 'like', "%{$q}%");
                });
            })
            ->when($filters['category'] ?? null, fn ($query, $c) => $query->where('category_id', $c))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('products.index', compact('products', 'filters', 'categories'));
    }

    public function create()
    {
        $categories = Category::orderBy('name')->get();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules(), $this->messages());

        Product::create($validated);
        return redirect()->route('products.index')->with('success', 'Produk baru berhasil ditambahkan!');
    }

    public function show(Product $product)
    {
        return view('products.show', [
            'product'         => $product,
            'recentSales'     => $product->saleItems()->with('sale')->latest('id')->limit(10)->get(),
            'recentPurchases' => $product->purchaseItems()->with('purchase')->latest('id')->limit(10)->get(),
        ]);
    }

    public function lowStock()
    {
        $products = Product::whereColumn('stock', '<=', 'minimum_stock')
            ->with('category')
            ->orderBy('stock', 'asc')
            ->orderBy('name')
            ->paginate(10);

        return view('products.low-stock', compact('products'));
    }

    public function edit(Product $product)
    {
        $categories = Category::orderBy('name')->get();
        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate($this->rules($product), $this->messages());

        $product->update($validated);
        return redirect()->route('products.show', $product)->with('success', 'Data produk berhasil diperbarui!');
    }

    public function destroy(Product $product)
    {
        // Produk yang pernah masuk transaksi tidak boleh hard-delete
        // agar riwayat pembelian/penjualan tetap bisa ditelusuri.
        if ($product->saleItems()->exists() || $product->purchaseItems()->exists()) {
            return back()->with('error', 'Produk sudah pernah masuk transaksi dan tidak dapat dihapus.');
        }

        $product->delete();
        return redirect()->route('products.index')->with('success', 'Produk berhasil dihapus!');
    }

    private function rules(?Product $product = null): array
    {
        $uniqueSku = 'required|string|max:50|unique:products,sku';
        if ($product) {
            $uniqueSku .= ',' . $product->id;
        }

        return [
            'sku'            => [$uniqueSku],
            'name'           => ['required', 'string', 'max:150'],
            'category_id'    => ['required', 'exists:categories,id'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price'  => ['required', 'numeric', 'min:0'],
            'stock'          => ['required', 'integer', 'min:0'],
            'minimum_stock'  => ['required', 'integer', 'min:0'],
        ];
    }

    private function messages(): array
    {
        return [
            'sku.required'           => 'SKU wajib diisi.',
            'sku.unique'             => 'SKU sudah dipakai produk lain, gunakan kode berbeda.',
            'name.required'          => 'Nama produk wajib diisi.',
            'category_id.required'   => 'Pilih kategori produk.',
            'category_id.exists'     => 'Kategori tidak tersedia.',
            'purchase_price.required'=> 'Harga beli wajib diisi.',
            'purchase_price.min'     => 'Harga beli tidak boleh negatif.',
            'selling_price.required' => 'Harga jual wajib diisi.',
            'selling_price.min'      => 'Harga jual tidak boleh negatif.',
            'stock.required'         => 'Stok awal wajib diisi.',
            'stock.min'              => 'Stok tidak boleh negatif.',
            'minimum_stock.required' => 'Stok minimum wajib diisi.',
        ];
    }
}
