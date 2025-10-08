<?php

namespace App\Http\Controllers;

use App\Models\ItemIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ItemInController extends Controller
{
    // define function to show list of items in
    public function index()
    {
        // get all items in sort by created at descending
        $itemsIn = ItemIn::with(['supplier', 'details.product'])->orderBy('created_at', 'desc')->get();

        // define variable for view
        $data = [
            'title' => 'Barang Masuk',
            'itemsIn' => $itemsIn,
        ];

        // return view for items in index
        return view('items_in.index', $data);
    }

    // define function to show create form
    public function create()
    {
        // define variable for view
        $data = [
            'title' => 'Tambah Barang Masuk',
        ];

        // get all suppliers sort by name ascending
        $data['suppliers'] = \App\Models\Supplier::orderBy('name', 'asc')->get();

        // get all products sort by name ascending
        $data['products'] = \App\Models\Product::orderBy('name', 'asc')->get();

        // return view for items in create
        return view('items_in.create', $data);
    }

    // define function to store new item in
    public function store(Request $request)
    {
        // validate the request
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            // check if is_paid is boolean
            'is_paid' => 'nullable|boolean',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|integer|min:0',
        ]);

        // calculate total item and total price
        $totalPrice = 0;
        foreach ($validated['items'] as $item) {
            $totalPrice += $item['quantity'] * $item['price'];
        }

        // try catch to store the item in with DB transaction
        try {
            DB::beginTransaction();
            // create new item in
            $itemIn = ItemIn::create([
                'supplier_id' => $validated['supplier_id'],
                'total_item' => count($validated['items']),
                'total_price' => $totalPrice,
                'is_paid' => $validated['is_paid'] ?? false,
            ]);
            // create item in details
            foreach ($validated['items'] as $item) {
                $itemIn->details()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['quantity'] * $item['price'],
                ]);

                // update product quantity and price_buy
                $product = \App\Models\Product::find($item['product_id']);
                $product->quantity += $item['quantity'];
                $product->price_buy = $item['price'];
                $product->save();

                // check stock priode for the product, if not exist create new if exist update only final stock
                $month = now()->month;
                $year  = now()->year;
                $stockPriode = \App\Models\StockPriode::where('product_id', $product->id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->first();

                if ($stockPriode) {
                    $stockPriode->final_stock += $item['quantity'];
                    $stockPriode->save();
                } else {
                    \App\Models\StockPriode::create([
                        'product_id' => $product->id,
                        'month' => $month,
                        'year' => $year,
                        'starting_stock' => $product->quantity - $item['quantity'],
                        'final_stock' => $product->quantity,
                    ]);
                }
            }

            DB::commit();
            return redirect()->route('items-in.index')->with('success', 'Barang masuk berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('items-in.create')->with('error', 'Terjadi kesalahan saat menambahkan barang masuk: ' . $e->getMessage());
        }
    }

    // define function to show edit form
    public function edit($id)
    {
        // find item in by id
        $itemIn = ItemIn::with(['supplier', 'details.product'])->findOrFail($id);

        // redirect to items-in.index if item in not found
        if (!$itemIn) {
            return redirect()->route('items-in.index')->with('error', 'Barang masuk tidak ditemukan.');
        }

        // check if priode is same as current month and year
        $currentMonth = now()->month;
        $currentYear  = now()->year;
        if ($itemIn->created_at->month != $currentMonth || $itemIn->created_at->year != $currentYear) {
            return redirect()->route('items-in.index')->with('error', 'Barang masuk hanya bisa dihapus pada periode yang sama.');
        }

        // define variable for view
        $data = [
            'title' => 'Edit Barang Masuk',
            'itemIn' => $itemIn,
            'id' => $id,
        ];

        // get all suppliers sort by name ascending
        $data['suppliers'] = \App\Models\Supplier::orderBy('name', 'asc')->get();

        // get all products sort by name ascending
        $data['products'] = \App\Models\Product::orderBy('name', 'asc')->get();

        // return view for items in edit
        return view('items_in.edit', $data);
    }

    // define function to update item in
    public function update(Request $request, $id)
    {
        // find item in by id
        $itemIn = ItemIn::with(['supplier', 'details.product'])->findOrFail($id);

        // redirect to items-in.index if item in not found
        if (!$itemIn) {
            return redirect()->route('items-in.index')->with('error', 'Barang masuk tidak ditemukan.');
        }

        // check if priode is same as current month and year
        $currentMonth = now()->month;
        $currentYear  = now()->year;
        if ($itemIn->created_at->month != $currentMonth || $itemIn->created_at->year != $currentYear) {
            return redirect()->route('items-in.index')->with('error', 'Barang masuk hanya bisa dihapus pada periode yang sama.');
        }

        // validate the request
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            // check if is_paid is boolean
            'is_paid' => 'nullable|boolean',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|integer|min:0',
        ]);

        // calculate total item and total price
        $totalPrice = 0;
        foreach ($validated['items'] as $item) {
            $totalPrice += $item['quantity'] * $item['price'];
        }


        // try catch to update the item in with DB transaction
        try {
            DB::beginTransaction();
            // revert product quantities from existing details
            foreach ($itemIn->details as $detail) {
                $product = \App\Models\Product::find($detail->product_id);
                $product->quantity -= $detail->quantity;
                $product->save();

                // update stock priode final stock
                $month = now()->month;
                $year  = now()->year;
                $stockPriode = \App\Models\StockPriode::where('product_id', $product->id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->first();
                if ($stockPriode) {
                    $stockPriode->final_stock -= $detail->quantity;
                    $stockPriode->save();
                } else {
                    \App\Models\StockPriode::create([
                        'product_id' => $product->id,
                        'month' => $month,
                        'year' => $year,
                        'starting_stock' => $product->quantity - $detail->quantity,
                        'final_stock' => $product->quantity,
                    ]);
                }
            }

            // update item in
            $itemIn->update([
                'supplier_id' => $validated['supplier_id'],
                'total_item' => count($validated['items']),
                'total_price' => $totalPrice,
                'is_paid' => $validated['is_paid'] ?? false,
            ]);

            // delete existing details
            $itemIn->details()->delete();

            // create new item in details
            foreach ($validated['items'] as $item) {
                $itemIn->details()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['quantity'] * $item['price'],
                ]);

                // update product quantity and price_buy
                $product = \App\Models\Product::find($item['product_id']);
                $product->quantity += $item['quantity'];
                $product->price_buy = $item['price'];
                $product->save();

                // check stock priode for the product, if not exist create new if exist update only final stock
                $month = now()->month;
                $year  = now()->year;
                $stockPriode = \App\Models\StockPriode::where('product_id', $product->id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->first();

                if ($stockPriode) {
                    $stockPriode->final_stock += $item['quantity'];
                    $stockPriode->save();
                } else {
                    \App\Models\StockPriode::create([
                        'product_id' => $product->id,
                        'month' => $month,
                        'year' => $year,
                        'starting_stock' => $product->quantity - $item['quantity'],
                        'final_stock' => $product->quantity,
                    ]);
                }
            }
            // commit transaction
            DB::commit();
            // redirect to items-in.index with success message
            return redirect()->route('items-in.index')->with('success', 'Barang masuk berhasil diperbarui.');
        } catch (\Exception $e) {
            // rollback transaction on error
            DB::rollBack();
            // redirect back to edit form with error message
            return redirect()->route('items-in.edit', $id)->with('error', 'Terjadi kesalahan saat memperbarui barang masuk: ' . $e->getMessage());
        }
    }

    // define function to delete item in
    public function destroy($id)
    {
        // find item in by id
        $itemIn = ItemIn::with(['supplier', 'details.product'])->findOrFail($id);

        // redirect to items-in.index if item in not found
        if (!$itemIn) {
            return redirect()->route('items-in.index')->with('error', 'Barang masuk tidak ditemukan.');
        }

        // check if priode is same as current month and year
        $currentMonth = now()->month;
        $currentYear  = now()->year;
        if ($itemIn->created_at->month != $currentMonth || $itemIn->created_at->year != $currentYear) {
            return redirect()->route('items-in.index')->with('error', 'Barang masuk hanya bisa dihapus pada periode yang sama.');
        }

        // try catch to delete the item in with DB transaction
        try {
            DB::beginTransaction();
            // revert product quantities from existing details
            foreach ($itemIn->details as $detail) {
                $product = \App\Models\Product::find($detail->product_id);
                $product->quantity -= $detail->quantity;
                $product->save();

                // update stock priode final stock
                $month = now()->month;
                $year  = now()->year;
                $stockPriode = \App\Models\StockPriode::where('product_id', $product->id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->first();

                if ($stockPriode) {
                    $stockPriode->final_stock -= $detail->quantity;
                    $stockPriode->save();
                }
            }
            // delete item in details
            $itemIn->details()->delete();
            // delete item in
            $itemIn->delete();
            // commit transaction
            DB::commit();
            // redirect to items-in.index with success message
            return redirect()->route('items-in.index')->with('success', 'Barang masuk berhasil dihapus.');
        } catch (\Exception $e) {
            // rollback transaction on error
            DB::rollBack();
            // redirect back to index with error message
            return redirect()->route('items-in.index')->with('error', 'Terjadi kesalahan saat menghapus barang masuk: ' . $e->getMessage());
        }
    }
}
