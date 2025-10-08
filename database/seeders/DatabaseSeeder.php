<?php

namespace Database\Seeders;

use App\Models\{
    Member,
    ProductCategory,
    User,
    Team,
    Product,
    StockPriode,
    Supplier
};
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database safely (idempotent).
     */
    public function run(): void
    {
        // === TEAM DEFAULT ===
        $team = Team::firstOrCreate(['name' => 'Company']);
        $teamId = $team->id;

        // === USER DEFAULT ===
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'level' => 'admin',
            ],
            [
                'name' => 'Owner',
                'email' => 'owner@example.com',
                'level' => 'owner',
            ],
        ];

        foreach ($users as $data) {
            User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => bcrypt('password'),
                    'level' => $data['level'],
                    'email_verified_at' => now(),
                    'current_team_id' => $teamId,
                ]
            );
        }

        // === PRODUCT CATEGORIES ===
        $categories = [
            ['category' => 'Makanan', 'code' => 'MK'],
            ['category' => 'Minuman', 'code' => 'MN'],
            ['category' => 'Kebersihan', 'code' => 'KB'],
        ];

        $categoryIds = [];
        foreach ($categories as $cat) {
            $categoryIds[$cat['code']] = ProductCategory::firstOrCreate($cat)->id;
        }

        // === PRODUCTS ===
        $products = [
            // Makanan
            ["MK", "Mi Instan Goreng Aceh (Indomie) - 1 pcs", "PRD-MK-0001", "Indomie", 2800, 3700],
            ["MK", "Kerupuk Udang Layar Kota Aceh - 1 pack", "PRD-MK-0002", "Layar Kota", 1000, 1500],
            ["MK", "Biskuit Kelapa - 1 pack", "PRD-MK-0003", "Roma", 18000, 25000],
            ["MK", "Kopi Bubuk Gayo (Grade commercial) - 250g", "PRD-MK-0004", "Kopi Gayo", 45000, 60000],
            ["MK", "Ikan Asin Kecil - 1 pack", "PRD-MK-0005", "IkanAsinAceh", 12000, 17000],
            ["MK", "Minyak Goreng Pouch 1L", "PRD-MK-0006", "Tropical / Sovia / Sania", 18000, 23000],
            ["MK", "Margarine / Mentega Tawar 200-250g", "PRD-MK-0007", "Blue Band / Filma", 16000, 21000],
            ["MK", "Gula Pasir 1 Kg", "PRD-MK-0008", "Gulaku / Rose Brand", 17500, 20000],

            // Minuman
            ["MN", "Kopi Arabika Gayo - 1 Kg (green/roasted)", "PRD-MN-0001", "Gayo", 150000, 190000],
            ["MN", "Teh Botol Sosro 250 ml - per botol", "PRD-MN-0002", "Sosro", 3000, 4500],
            ["MN", "Susu Kental Manis Kaleng 370-490g", "PRD-MN-0003", "Frisian Flag / Tiga Sapi", 11000, 15000],
            ["MN", "Kopi Instan Sachet (Kopi 3 in 1) - per sachet", "PRD-MN-0004", "Kapal Api / Nescafe", 1800, 2600],
            ["MN", "Minuman Serbuk Instan (Teh/Jus) - 1 pack", "PRD-MN-0005", "Various", 8000, 12000],

            // Kebersihan
            ["KB", "Sabun Cuci Piring 400 ml", "PRD-KB-0001", "Sunlight / Mama Lemon", 8500, 12000],
            ["KB", "Sikat / Alat Kebersihan (per pc)", "PRD-KB-0002", "Generic", 6000, 9000],
            ["KB", "Detergen Bubuk 1 Kg", "PRD-KB-0003", "Rinso / So Klin", 20000, 25000],
            ["KB", "Pembersih Lantai 800 ml - 1 botol", "PRD-KB-0004", "Wipol / Bayclin", 12000, 16000],
            ["KB", "Pewangi / Pelembut Pakaian 800 ml - 1 botol", "PRD-KB-0005", "Molto / So Klin", 13000, 17000],
        ];

        foreach ($products as [$catCode, $name, $code, $brand, $buy, $sell]) {
            $product = Product::firstOrCreate(
                ['code' => $code],
                [
                    'category_id' => $categoryIds[$catCode],
                    'name' => $name,
                    'brand' => $brand,
                    'price_buy' => $buy,
                    'price_sell' => $sell,
                ]
            );

            // Seed stock period untuk produk jika belum ada
            StockPriode::firstOrCreate([
                'month' => Carbon::now()->month,
                'year' => Carbon::now()->year,
                'product_id' => $product->id,
            ]);
        }

        // === SUPPLIERS ===
        $suppliers = [
            ["Merdeka Supply", "+62 813-6161-6030", "Jl. Tgk. Imum Lueng Bata, Lamseupeung, Banda Aceh, Aceh"],
            ["PT. Kelola Pangan Indonesia (Cabang Banda Aceh)", "(0651) 7318958", "TPI Ulee Lheue / Banda Aceh, Aceh"],
            ["Yayasan MPKG / Gayo Arabica Coffee (MPKG)", "+62 823-6755-5559", "Paya Ilang, Bebesen, Aceh Tengah, Aceh"],
            ["PT. Segar Kumala Indonesia (Cabang Aceh)", "+62 21-4603125", "Jl. Kartini No.43-C, Peunayong, Banda Aceh"],
            ["Omura Powder Groceries (Bubuk Minuman)", "081382688890", "Takengon, Kabupaten Aceh Tengah, Aceh"],
        ];

        foreach ($suppliers as [$name, $contact, $address]) {
            Supplier::firstOrCreate(['name' => $name], compact('contact', 'address'));
        }

        // === MEMBERS ===
        $members = [
            ['M. Fadhlan', 'Jl. Politeknik Aceh No. 10, Batoh, Banda Aceh', '0812-6060-1122'],
            ['Nur Aisyah', 'Jl. T. Imum Lueng Bata, Lamseupeung, Banda Aceh', '0822-7888-4455'],
            ['Rizki Maulana', 'Jl. T. Nyak Arief, Batoh, Banda Aceh', '0852-6111-3344'],
            ['Cut Intan', 'Jl. Inong Balee, Darussalam, Banda Aceh', '0813-7000-8899'],
            ['Teuku Zulfikar', 'Jl. Mr. Mohd Hasan, Batoh, Banda Aceh', '0878-9990-2211'],
        ];

        foreach ($members as [$name, $address, $phone]) {
            Member::firstOrCreate(['phone' => $phone], compact('name', 'address'));
        }

        // === ITEM IN SEEDER ===
        $this->call([
            ItemInSeeder::class,
        ]);

        $this->command->info('✅ Database seeding selesai tanpa duplikasi!');
    }
}
