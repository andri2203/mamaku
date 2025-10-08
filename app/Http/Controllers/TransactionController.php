<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    // define function to show Transaction data
    public function index()
    {
        // get all Transaction Data
        $transactions = Transaction::with(['user', 'member', 'details.product'])->orderByDesc('created_at')->get();
        // dd($transactions->toArray());
        // defne variable for view
        $data = [
            'title' => 'Transaksi',
            'transactions' => $transactions,
        ];

        // return to view transaction.index
        return view('transactions.index', $data);
    }

    public function detail($id)
    {
        // get transaction data by id
        $transaction = Transaction::with(['user', 'member', 'details.product'])->findOrFail($id);

        // redirect to transaction.index if transaksi not found
        if (!$transaction) {
            return redirect()->route('transaction.index')->with('error', 'Transaksi tidak ditemukan.');
        }

        // define variable for view
        $data = [
            'title' => 'Detail Transaksi',
            'transaction' => $transaction,
            'id' => $id,
        ];

        return view('transactions.detail', $data);
    }

    public function cetak($id)
    {
        // get transaction data by id
        $transaction = Transaction::with(['user', 'member', 'details.product'])->findOrFail($id);

        // redirect to transaction.index if transaksi not found
        if (!$transaction) {
            return redirect()->route('transaction.index')->with('error', 'Transaksi tidak ditemukan.');
        }

        // define variable for view
        $data = [
            'title' => 'Cetak : TR-' . str_pad($transaction->id, 4, '0', STR_PAD_LEFT),
            'transaction' => $transaction,
            'id' => $id,
        ];

        return view('transactions.cetak', $data);
    }

    // define function to show create form
    public function create()
    {
        // get all products categories
        $categories = \App\Models\ProductCategory::all();
        // get all Products with stocks is not 0
        $products = \App\Models\Product::where('quantity', '>', 0)->orderBy('name', 'asc')->get();
        // get all Member sort by name asc
        $members = Member::orderBy('name', 'asc')->get();

        // define variable to view
        $data = [
            'title' => 'Tambah Transaksi',
            'products' => $products,
            'categories' => $categories,
            'members' => $members,
        ];

        // return to the view transactions.create
        return view('transactions.create', $data);
    }

    // define function to store transaction data
    public function store(Request $request)
    {
        $validated = $request->validate([
            'member_id' => 'nullable|exists:members,id',
            'discount' => 'nullable|numeric|min:0',
            'is_paid' => 'nullable|boolean',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|integer|min:0',
            'items.*.discount' => 'required|numeric|min:0',
        ]);

        $user_id = Auth::user()->id;
        $total_item = count($validated['items']);
        $total_price = 0;
        foreach ($validated['items'] as $item) {
            $total_price += ($item['quantity'] * $item['price']) - $item['discount'];
        }

        $createTransaction = [
            'user_id' => $user_id,
            'total_item' => $total_item,
            'discount' => intval($validated['discount']),
            'total_price' =>  $total_price - intval($validated['discount']),
            'is_paid' => $validated['is_paid'] ?? false,
        ];

        if (isset($validated['member_id'])) {
            $createTransaction['member_id'] = $validated['member_id'];
        }

        try {
            DB::beginTransaction();
            $transaction = Transaction::create($createTransaction);

            // create transaction details
            foreach ($validated['items'] as $item) {
                $transaction->details()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount' => $item['discount'],
                    'subtotal' => ($item['quantity'] * $item['price']) - $item['discount'],
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

            DB::commit();

            return redirect()->route('transaction.detail', ['id' => $transaction->id])->with('success', 'Transaksi berhasil dibuat.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('transaction.create')->withInput()->with('error', 'Terjadi kesalahan saat membuat transaksi: ' . $th->getMessage());
        }
    }

    public function edit($id)
    {
        // get transaction data by id
        $transaction = Transaction::with(['user', 'member', 'details.product'])->findOrFail($id);

        // redirect to transaction.index if transaksi not found
        if (!$transaction) {
            return redirect()->route('transaction.index')->with('error', 'Transaksi tidak ditemukan.');
        }

        // check if priode is same as current month and year
        $currentMonth = now()->month;
        $currentYear  = now()->year;
        if ($transaction->created_at->month != $currentMonth || $transaction->created_at->year != $currentYear) {
            return redirect()->route('items-in.index')->with('error', 'Barang masuk hanya bisa dihapus pada periode yang sama.');
        }

        // get all products categories
        $categories = \App\Models\ProductCategory::all();
        // get all Products with stocks is not 0
        $products = \App\Models\Product::where('quantity', '>', 0)->orderBy('name', 'asc')->get();
        // get all Member sort by name asc
        $members = Member::orderBy('name', 'asc')->get();

        // define variable to view
        $data = [
            'title' => 'Edit Transaksi : TR-' . str_pad($transaction->id, 4, '0', STR_PAD_LEFT),
            'id' => $id,
            'transaction' => $transaction,
            'products' => $products,
            'categories' => $categories,
            'members' => $members,
        ];

        // return to the view transactions.create
        return view('transactions.edit', $data);
    }


    public function update(Request $request, $id)
    {
        // get transaction data by id
        $transaction = Transaction::with(['user', 'member', 'details.product'])->findOrFail($id);

        // redirect to transaction.index if transaksi not found
        if (!$transaction) {
            return redirect()->route('transaction.index')->with('error', 'Transaksi tidak ditemukan.');
        }

        // check if priode is same as current month and year
        $currentMonth = now()->month;
        $currentYear  = now()->year;
        if ($transaction->created_at->month != $currentMonth || $transaction->created_at->year != $currentYear) {
            return redirect()->route('items-in.index')->with('error', 'Barang masuk hanya bisa dihapus pada periode yang sama.');
        }

        $validated = $request->validate([
            'member_id' => 'nullable|exists:members,id',
            'discount' => 'nullable|numeric|min:0',
            'is_paid' => 'nullable|boolean',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|integer|min:0',
            'items.*.discount' => 'required|numeric|min:0',
        ]);

        $user_id = Auth::user()->id;
        $total_item = count($validated['items']);
        $total_price = 0;
        foreach ($validated['items'] as $item) {
            $total_price += ($item['quantity'] * $item['price']) - $item['discount'];
        }

        $updateTransaction = [
            'user_id' => $user_id,
            'total_item' => $total_item,
            'discount' => intval($validated['discount']),
            'total_price' =>  $total_price - intval($validated['discount']),
            'is_paid' => $validated['is_paid'] ?? false,
        ];

        if (isset($validated['member_id'])) {
            $updateTransaction['member_id'] = $validated['member_id'];
        }

        try {
            DB::beginTransaction();
            // revert product quantities from existing details
            foreach ($transaction->details as $detail) {
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

            $transaction->update($updateTransaction);

            // delete existing details
            $transaction->details()->delete();

            // create transaction details
            foreach ($validated['items'] as $item) {
                $transaction->details()->create([
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'discount' => $item['discount'],
                    'subtotal' => ($item['quantity'] * $item['price']) - $item['discount'],
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

            DB::commit();

            return redirect()->route('transaction.detail', ['id' => $transaction->id])->with('success', 'Transaksi berhasil dibuat.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->route('transaction.create')->withInput()->with('error', 'Terjadi kesalahan saat membuat transaksi: ' . $th->getMessage());
        }
    }

    // define function to destroy data
    public function destroy($id)
    {
        // find transaksi by id
        $transaction = Transaction::with(['user', 'member', 'details.product'])->findOrFail($id);

        // redirect to transaction.index if transaksi not found
        if (!$transaction) {
            return redirect()->route('transaction.index')->with('error', 'Transaksi tidak ditemukan.');
        }

        // check if priode is same as current month and year
        $currentMonth = now()->month;
        $currentYear  = now()->year;
        if ($transaction->created_at->month != $currentMonth || $transaction->created_at->year != $currentYear) {
            return redirect()->route('items-in.index')->with('error', 'Barang masuk hanya bisa dihapus pada periode yang sama.');
        }

        // try catch to delete the transaksi with DB transaction
        try {
            DB::beginTransaction();
            // revert product quantities from existing details
            foreach ($transaction->details as $detail) {
                $product = \App\Models\Product::find($detail->product_id);
                $product->quantity += $detail->quantity;
                $product->save();
            }
            // delete transaksi details
            $transaction->details()->delete();
            // delete transaksi
            $transaction->delete();
            // commit transaction
            DB::commit();
            // redirect to transaction.index with success message
            return redirect()->route('transaction.index')->with('success', 'Transaksi berhasil dihapus.');
        } catch (\Exception $e) {
            // rollback transaction on error
            DB::rollBack();
            // redirect back to index with error message
            return redirect()->route('transaction.index')->with('error', 'Terjadi kesalahan saat menghapus Transaksi: ' . $e->getMessage());
        }
    }
}
