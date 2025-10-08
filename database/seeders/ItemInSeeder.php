<?php

namespace Database\Seeders;

use App\Models\ItemIn;
use App\Models\ItemInDetail;
use App\Models\{Supplier, Product};
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ItemInSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua supplier dan product yang sudah ada
        $suppliers = Supplier::all();
        $products  = Product::all();

        // Kalau supplier atau produk kosong, hentikan seeding
        if ($suppliers->isEmpty() || $products->isEmpty()) {
            $this->command->warn('⚠️ Supplier atau Product belum ada. Jalankan SupplierSeeder dan ProductSeeder dulu.');
            return;
        }

        // Buat 5 transaksi pembelian barang (ItemIn)
        foreach ($suppliers->take(5) as $supplier) {
            $itemIn = ItemIn::create([
                'supplier_id' => $supplier->id,
                'total_item'  => 0, // nanti dihitung dari detail
                'total_price' => 0, // nanti dihitung dari detail
                'is_paid'     => fake()->boolean(80), // 80% transaksi sudah dibayar
            ]);

            $totalItem  = 0;
            $totalPrice = 0;

            // ambil 2–3 produk random per supplier
            $chosenProducts = $products->random(rand(2, 3));

            foreach ($chosenProducts as $product) {
                $quantity = fake()->numberBetween(10, 50);
                $price    = $product->price_buy; // harga beli sesuai product
                $subtotal = $quantity * $price;

                ItemInDetail::create([
                    'item_in_id' => $itemIn->id,
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'price'      => $price,
                    'subtotal'   => $subtotal,
                ]);

                // update stok product
                $product->increment('quantity', $quantity);

                // update StockPriode final_stock
                $latestStockPriode = $product->stockPriodes()->latest('month')->first();
                $latestStockPriode->increment('final_stock', $quantity);
                $totalItem  += $quantity;
                $totalPrice += $subtotal;
            }

            // update total_item dan total_price di ItemIn
            $itemIn->update([
                'total_item'  => $chosenProducts->count(),
                'total_price' => $totalPrice,
            ]);
        }

        $this->command->info('✅ ItemIn & ItemInDetail seeding selesai!');
    }
}
