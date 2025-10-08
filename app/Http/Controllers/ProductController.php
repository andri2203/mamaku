<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\Models\{Product, ProductCategory, StockPriode};
use Illuminate\Support\Carbon;

class ProductController extends Controller
{
    // define carbon now variable
    private $now;

    public function __construct()
    {
        $this->now = Carbon::now();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //get all products, sort by latest and include category relationship
        $products = Product::with('category')->orderBy('code')->get();
        // get all categories
        $categories = ProductCategory::all();

        // Define data to be passed to the view
        $data = [
            'title' => 'Produk',
            'products' => $products,
            'categories' => $categories,
        ];

        return view('products.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Defie data to be passed to the view
        $data = [
            'title' => 'Tambah Produk',
            'categories' => ProductCategory::all(),
            'brands' => Product::getAllBrands(),
        ];

        // get all categories and take only id, name


        return view('products.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the request with all fillable 'name', 'code', 'brand', 'quantity', 'price_buy', 'price_sell', 'discount'
        $validated = $request->validate([
            'category_id' => 'required|exists:product_categories,id',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:100',
        ]);

        $category = ProductCategory::find($validated['category_id']);

        if (!$category) {
            return redirect()->route('product.index')->with('error', 'Kategori produk tidak ditemukan.');
        }

        // define variable periode month and year
        $month = $this->now->month;
        $year  = $this->now->year;

        // Create a new product and try catch error
        try {
            // generate product code with 'PRD-' + product category code + '-' + str_pad with 4 digits
            $validated['code'] = 'PRD-' . $category->code . '-' . str_pad(Product::where('category_id', $category->id)->count() + 1, 4, '0', STR_PAD_LEFT);
            // create the product
            $product = Product::create($validated);
            // create stock priode for the product
            StockPriode::create([
                'month' => $month,
                'year' => $year,
                'product_id' => $product->id,
            ]);

            // Redirect to products index with success message
            return redirect()->route('product.index')->with('success', 'Produk berhasil ditambahkan.');
        } catch (\Exception $e) {
            return redirect()->route('product.index')->with('error', 'Terjadi kesalahan saat menambahkan produk: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Defie data to be passed to the view
        $data = [
            'title' => 'Edit Produk',
            'product' => Product::findOrFail($id),
            'categories' => ProductCategory::all(),
            'brands' => Product::getAllBrands(),
        ];

        return view('products.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Define the product to be updated
        $product = Product::findOrFail($id);

        // Validate the request with all fillable 'name', 'code', 'brand', 'quantity', 'price_buy', 'price_sell', 'discount'
        $validated = $request->validate([
            'category_id' => 'required|exists:product_categories,id',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:100',
            'price_buy' => 'required|numeric|min:0',
            'price_sell' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0|max:100',
        ]);
        // Update the product with validated data and try caatch error
        try {
            // check if category_id is changed
            if ($product->category_id != $validated['category_id']) {
                $category = ProductCategory::find($validated['category_id']);

                if (!$category) {
                    return redirect()->route('product.index')->with('error', 'Kategori produk tidak ditemukan.');
                }

                // generate new product code with 'PRD-' + product category code + '-' + str_pad with 4 digits
                $validated['code'] = 'PRD-' . $category->code . '-' . str_pad(Product::where('category_id', $category->id)->count(), 4, '0', STR_PAD_LEFT);
            }

            $product->update($validated);

            return redirect()->route('product.index')->with('success', 'Produk berhasil diperbarui.');
        } catch (\Exception $e) {
            return redirect()->route('product.index')->with('error', 'Terjadi kesalahan saat memperbarui produk: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // Define the product to be deleted
        $product = Product::findOrFail($id);
        // check if the product has any related itemInDetails or itemOutDetails
        if ($product->itemInDetails()->exists() || $product->itemOutDetails()->exists() || $product->transactionDetails()->exists()) {
            return redirect()->route('product.index')->with('error', 'Produk tidak dapat dihapus karena memiliki riwayat transaksi.');
        }
        // Delete the product and try catch error
        try {
            $product->stockPriodes()->delete();
            $product->delete();
            return redirect()->route('product.index')->with('success', 'Produk berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('product.index')->with('error', 'Terjadi kesalahan saat menghapus produk: ' . $e->getMessage());
        }
    }
}
