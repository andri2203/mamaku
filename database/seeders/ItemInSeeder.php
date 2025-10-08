<?php

namespace Database\Seeders;

use App\Models\{ItemIn, ItemInDetail, Supplier, Product, StockPriode};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;
use function fake;

class ItemInSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = Supplier::all();
        $products  = Product::all();

        // Berhenti jika tidak ada data pendukung
        if ($suppliers->isEmpty() || $products->isEmpty()) {
            $this->command->warn('⚠️ Supplier atau Product belum ada. Jalankan SupplierSeeder dan ProductSeeder dulu.');
            return;
        }

        foreach ($suppliers->take(5) as $supplier) {
            $itemIn = ItemIn::create([
                'supplier_id' => $supplier->id,
                'total_item'  => 0,
                'total_price' => 0,
                'is_paid'     => fake()->boolean(80),
            ]);

            $totalItem  = 0;
            $totalPrice = 0;

            $chosenProducts = $products->random(min(3, $products->count()));

            foreach ($chosenProducts as $product) {
                $quantity = fake()->numberBetween(10, 50);
                $price    = $product->price_buy ?? 0;
                $subtotal = $quantity * $price;

                ItemInDetail::create([
                    'item_in_id' => $itemIn->id,
                    'product_id' => $product->id,
                    'quantity'   => $quantity,
                    'price'      => $price,
                    'subtotal'   => $subtotal,
                ]);

                // Update stok produk (jika kolom quantity ada)
                if ($product->isFillable('quantity')) {
                    $product->increment('quantity', $quantity);
                }

                // Update StockPriode jika tersedia
                try {
                    $latestStockPriode = $product->stockPriodes()->latest('month')->first();

                    if ($latestStockPriode instanceof StockPriode) {
                        $latestStockPriode->increment('final_stock', $quantity);
                    }
                } catch (\Throwable $e) {
                    Log::warning("⚠️ Gagal update StockPriode untuk produk {$product->id}: {$e->getMessage()}");
                }

                $totalItem  += $quantity;
                $totalPrice += $subtotal;
            }

            // Update total pada ItemIn
            $itemIn->update([
                'total_item'  => $totalItem,
                'total_price' => $totalPrice,
            ]);
        }

        $this->command->info('✅ ItemIn & ItemInDetail seeding selesai!');
    }
}
