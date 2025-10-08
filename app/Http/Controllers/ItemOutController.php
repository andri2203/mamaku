<?php

namespace App\Http\Controllers;

use App\ItemOutStatusEnum;
use App\Models\ItemOut;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ItemOutController extends Controller
{
    // define function show Item Out view
    public function index()
    {
        // get all items in sort by created at descending
        $itemsOut = ItemOut::with(['user', 'details.product'])->orderBy('created_at', 'desc')->get();

        // get ItemOutStatus enum
        $statusEnum = ItemOutStatusEnum::cases();

        // define variable for view
        $data = [
            'title' => 'Barang Keluar',
            'itemsOut' => $itemsOut,
            'statusEnum' => $statusEnum,
        ];

        // return view for items out index
        return view('items_out.index', $data);
    }

    // define function to show form create view
    public function create()
    {
        // get all products sort by name ascending
        $products = \App\Models\Product::where('quantity', '>', 0)->orderBy('name', 'asc')->get();

        // get ItemOutStatus enum
        $statusEnum = ItemOutStatusEnum::cases();

        // define variable for view
        $data = [
            'title' => 'Barang Keluar',
            'products' => $products,
            'statusEnum' => $statusEnum,
        ];

        // return view for items out create
        return view('items_out.create', $data);
    }

    // define function to store data
    public function store(Request $request)
    {
        // validate the request
        $validated = $request->validate([
            'status' => [Rule::enum(ItemOutStatusEnum::class)],
            'note' => 'required|max:255',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.note' => 'required|max:255',
        ]);

        try {
            DB::beginTransaction();
            $itemOut = ItemOut::create([
                'user_id' => Auth::user()->id,
                'total_item' => count($validated['items']),
                'note' => $validated['note'],
                'status' => $validated['status'],
            ]);

            // create item out details
            foreach ($validated['items'] as $item) {
                $itemOut->details()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'note' => $item['note'],
                ]);

                // update product quantity
                $product = \App\Models\Product::find($item['product_id']);
                $product->quantity -= $item['quantity'];
                $product->save();

                // update stock priode final stock
                $month = now()->month;
                $year  = now()->year;
                $stockPriode = \App\Models\StockPriode::where('product_id', $item['product_id'])
                    ->where('month', $month)
                    ->where('year', $year)
                    ->first();
                if ($stockPriode) {
                    $stockPriode->final_stock = $product->quantity;
                    $stockPriode->save();
                } else {
                    // create new stock priode if not exists
                    \App\Models\StockPriode::create([
                        'month' => $month,
                        'year' => $year,
                        'product_id' => $item['product_id'],
                        'starting_stock' => $product->quantity + $item['quantity'], // add back the quantity that was just deducted
                        'final_stock' => $product->quantity,
                    ]);
                }
            }
            //code...
            DB::commit();
            return redirect()->route('items-out.index')->with('success', 'Barang keluar berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('items-out.create')->with('error', 'Terjadi kesalahan saat menambahkan barang keluar: ' . $e->getMessage());
        }
    }

    // define function to show form edit view
    public function edit($id)
    {
        // find item out by id
        $itemOut = ItemOut::with(['user', 'details.product'])->findOrFail($id);

        if (!$itemOut) {
            return redirect()->route('items-out.index')->with('error', 'Barang tidak ditemukan.');
        }

        // check if priode is same as current month and year
        $currentMonth = now()->month;
        $currentYear  = now()->year;
        if ($itemOut->created_at->month != $currentMonth || $itemOut->created_at->year != $currentYear) {
            return redirect()->route('items-in.index')->with('error', 'Barang masuk hanya bisa dihapus pada periode yang sama.');
        }

        // get all products sort by name ascending
        $products = \App\Models\Product::where('quantity', '>', 0)->orderBy('name', 'asc')->get();

        // get ItemOutStatus enum
        $statusEnum = ItemOutStatusEnum::cases();

        // define variable for view
        $data = [
            'title' => 'Barang Keluar',
            'products' => $products,
            'statusEnum' => $statusEnum,
            'itemOut' => $itemOut,
            'id' => $id,
        ];

        // return view for items out create
        return view('items_out.edit', $data);
    }

    // define function to update data
    public function update(Request $request, $id)
    {
        // find item out by id
        $itemOut = ItemOut::with(['user', 'details.product'])->findOrFail($id);

        if (!$itemOut) {
            return redirect()->route('items-out.index')->with('error', 'Barang tidak ditemukan.');
        }

        // check if priode is same as current month and year
        $currentMonth = now()->month;
        $currentYear  = now()->year;
        if ($itemOut->created_at->month != $currentMonth || $itemOut->created_at->year != $currentYear) {
            return redirect()->route('items-in.index')->with('error', 'Barang masuk hanya bisa dihapus pada periode yang sama.');
        }

        // validate the request
        $validated = $request->validate([
            'status' => [Rule::enum(ItemOutStatusEnum::class)],
            'items' => 'required|array|min:1',
            'note' => 'required|max:255',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.note' => 'required|max:255',
        ]);

        try {
            DB::beginTransaction();
            // revert product quantities from existing details
            foreach ($itemOut->details as $detail) {
                $product = \App\Models\Product::find($detail->product_id);
                $product->quantity += $detail->quantity;
                $product->save();

                // update stock priode final stock
                $month = now()->month;
                $year  = now()->year;
                $stockPriode = \App\Models\StockPriode::where('product_id', $detail->product_id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->first();
                if ($stockPriode) {
                    $stockPriode->final_stock = $product->quantity;
                    $stockPriode->save();
                } else {
                    // create new stock priode if not exists
                    \App\Models\StockPriode::create([
                        'month' => $month,
                        'year' => $year,
                        'product_id' => $detail->product_id,
                        'starting_stock' => $product->quantity - $detail->quantity, // subtract the quantity that was just added back
                        'final_stock' => $product->quantity,
                    ]);
                }
            }

            // Update Item Out
            $itemOut->update([
                'total_item' => count($validated['items']),
                'note' => $validated['note'],
                'status' => $validated['status'],
            ]);

            // delete existing details
            $itemOut->details()->delete();

            // create new item out details
            foreach ($validated['items'] as $item) {
                $itemOut->details()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'note' => $item['note'],
                ]);

                // update product quantity and price_buy
                $product = \App\Models\Product::find($item['product_id']);
                $product->quantity -= $item['quantity'];
                $product->save();

                // update stock priode final stock
                $month = now()->month;
                $year  = now()->year;
                $stockPriode = \App\Models\StockPriode::where('product_id', $item['product_id'])
                    ->where('month', $month)
                    ->where('year', $year)
                    ->first();
                if ($stockPriode) {
                    $stockPriode->final_stock = $product->quantity;
                    $stockPriode->save();
                } else {
                    // create new stock priode if not exists
                    \App\Models\StockPriode::create([
                        'month' => $month,
                        'year' => $year,
                        'product_id' => $item['product_id'],
                        'starting_stock' => $product->quantity + $item['quantity'], // add back the quantity that was just deducted
                        'final_stock' => $product->quantity,
                    ]);
                }
            }

            // commit transaction
            DB::commit();

            // redirect to items-out.index with success message
            return redirect()->route('items-out.index')->with('success', 'Barang keluar berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('items-out.create')->with('error', 'Terjadi kesalahan saat menambahkan barang keluar: ' . $e->getMessage());
        }
    }

    // define function to destroy data
    public function destroy($id)
    {
        // find item out by id
        $itemOut = ItemOut::with(['user', 'details.product'])->findOrFail($id);


        // redirect to items-out.index if item out not found
        if (!$itemOut) {
            return redirect()->route('items-out.index')->with('error', 'Barang keluar tidak ditemukan.');
        }

        // check if priode is same as current month and year
        $currentMonth = now()->month;
        $currentYear  = now()->year;
        if ($itemOut->created_at->month != $currentMonth || $itemOut->created_at->year != $currentYear) {
            return redirect()->route('items-in.index')->with('error', 'Barang masuk hanya bisa dihapus pada periode yang sama.');
        }

        // try catch to delete the item out with DB transaction
        try {
            DB::beginTransaction();
            // revert product quantities from existing details
            foreach ($itemOut->details as $detail) {
                $product = \App\Models\Product::find($detail->product_id);
                $product->quantity += $detail->quantity;
                $product->save();

                // update stock priode final stock
                $month = now()->month;
                $year  = now()->year;
                $stockPriode = \App\Models\StockPriode::where('product_id', $detail->product_id)
                    ->where('month', $month)
                    ->where('year', $year)
                    ->first();
                if ($stockPriode) {
                    $stockPriode->final_stock = $product->quantity;
                    $stockPriode->save();
                } else {
                    // create new stock priode if not exists
                    \App\Models\StockPriode::create([
                        'month' => $month,
                        'year' => $year,
                        'product_id' => $detail->product_id,
                        'starting_stock' => $product->quantity - $detail->quantity, // subtract the quantity that was just added back
                        'final_stock' => $product->quantity,
                    ]);
                }
            }
            // delete item out details
            $itemOut->details()->delete();
            // delete item out
            $itemOut->delete();
            // commit transaction
            DB::commit();
            // redirect to items-out.index with success message
            return redirect()->route('items-out.index')->with('success', 'Barang keluar berhasil dihapus.');
        } catch (\Exception $e) {
            // rollback transaction on error
            DB::rollBack();
            // redirect back to index with error message
            return redirect()->route('items-out.index')->with('error', 'Terjadi kesalahan saat menghapus barang keluar: ' . $e->getMessage());
        }
    }
}
