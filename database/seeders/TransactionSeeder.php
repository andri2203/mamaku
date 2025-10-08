<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // get all members
        $members = \App\Models\Member::all();
        $users   = \App\Models\User::first(); // ambil user pertama untuk user_id
        $products = \App\Models\Product::all();

        if ($members->isEmpty() || $products->isEmpty()) {
            $this->command->warn('⚠️ Member atau Product belum ada. Jalankan MemberSeeder dan ProductSeeder dulu.');
            return;
        }

        // buat 5 transaksi penjualan (Transaction)
        foreach ($members->take(5) as $member) {
            $transaction = \App\Models\Transaction::create([
                'member_id'  => $member->id,
                'user_id'    => $users->id,
                'total_item' => 0, // nanti dihitung dari detail
                'total_price' => 0, // nanti dihitung dari detail
                'discount'   => fake()->numberBetween(0, 20), // diskon 0-20%
                'is_paid'    => fake()->boolean(90), // 90% transaksi sudah dibayar
            ]);
            $totalItem  = 0;
            $totalPrice = 0;
            // ambil 2-3 produk random per member
            $chosenProducts = $products->random(rand(2, 3));
            foreach ($chosenProducts as $product) {
                $quantity = fake()->numberBetween(1, 5);
                if ($product->quantity < $quantity) {
                    $quantity = $product->quantity; // kalau stok kurang, jual sesuai stok
                }
                if ($quantity == 0) {
                    continue; // kalau stok habis, skip produk ini
                }
                $price    = $product->price_sell; // harga jual sesuai product
                $discount = $product->discount; // diskon sesuai product
                $subtotal = ($quantity * $price) - ($quantity * $price * $discount / 100);
                \App\Models\TransactionDetail::create([
                    'transaction_id' => $transaction->id,
                    'product_id'  => $product->id,
                    'quantity'    => $quantity,
                    'price'       => $price,
                    'discount'    => $discount,
                    'subtotal'    => $subtotal,
                ]);
                // update stok product
                $product->decrement('quantity', $quantity);
                $totalItem  += $quantity;
                $totalPrice += $subtotal;
                // update StockPriode final_stock
                $latestStockPriode = $product->stockPriodes()->latest('month')->first();
                $latestStockPriode->decrement('final_stock', $quantity);
            }
            // update total_item dan total_price di Transaction
            $transaction->update([
                'total_item'  => $chosenProducts->count(),
                'total_price' => $totalPrice,
            ]);
        }
    }
}
