<?php

namespace App\Http\Controllers;

use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    // function to show all product categories
    public function index()
    {
        // get all product categories from database sorted by name
        $productCategories = ProductCategory::orderBy('category', 'asc')->get();

        // Define data to be passed to the view
        $data = [
            'title' => 'Kategori Produk',
            'productCategories' => $productCategories,
        ];

        return view('product_categories.index', $data);
    }

    // function to show create product category form
    public function create()
    {
        $data = [
            'title' => 'Tambah Kategori Produk',
        ];

        return view('product_categories.create', $data);
    }

    // function to store new product category
    public function store(Request $request)
    {
        // validate the request
        $credential = $request->validate([
            'category' => 'required|string|max:255|unique:product_categories,category',
            'code' => 'required|string|max:255|unique:product_categories,code',
        ]);

        // create new product category
        ProductCategory::create([
            'category' => $credential['category'],
            'code' => $credential['code'],
        ]);

        return redirect()->route('product-categories.index')->with('success', 'Kategori produk berhasil ditambahkan.');
    }

    // function to show edit product category form
    public function edit($id)
    {
        // get product category by id
        $productCategory = ProductCategory::findOrFail($id);
        $data = [
            'title' => 'Edit Kategori Produk',
            'productCategory' => $productCategory,
        ];

        return view('product_categories.edit', $data);
    }

    // function to update product category
    public function update(Request $request, $id)
    {
        // get product category by id
        $productCategory = ProductCategory::findOrFail($id);

        // validate the request
        $credential = $request->validate([
            'category' => 'required|string|max:255|unique:product_categories,category,' . $productCategory->id,
            'code' => 'required|string|max:255|unique:product_categories,code,' . $productCategory->id,
        ]);

        // dont allow to change code if the category has products
        if ($productCategory->products()->count() > 0 && $credential['code'] !== $productCategory->code) {
            return redirect()->route('product-categories.index')->with('error', 'Kode kategori produk tidak dapat diubah karena telah memiliki produk.');
        }

        // update product category
        $productCategory->update([
            'category' => $credential['category'],
            'code' => $credential['code'],
        ]);
        return redirect()->route('product-categories.index')->with('success', 'Kategori produk berhasil diupdate.');
    }

    // function to delete product category
    public function destroy($id)
    {
        // get product category by id
        $productCategory = ProductCategory::findOrFail($id);
        // check if product category has products
        if ($productCategory->products()->count() > 0) {
            return redirect()->route('product-categories.index')->with('error', 'Kategori produk tidak dapat dihapus karena telah memiliki produk.');
        }
        // delete product category
        $productCategory->delete();
        return redirect()->route('product-categories.index')->with('success', 'Kategori produk berhasil dihapus.');
    }
}
