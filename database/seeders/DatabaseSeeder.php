<?php

namespace Database\Seeders;

use App\Models\{Member, ProductCategory, User, Team, Product, StockPriode, Supplier};
use Carbon\Carbon;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // created default team & get id
        $team = Team::create(['name' => 'Company']);
        $team = $team->id;

        // create default user admin
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'level' => 'admin',
            'email_verified_at' => now(),
            'current_team_id' =>  $team,
        ]);

        // create default user owner
        User::create([
            'name' => 'Owner',
            'email' => 'owner@example.com',
            'password' => bcrypt('password'),
            'level' => 'owner',
            'email_verified_at' => now(),
            'current_team_id' =>  $team,
        ]);


        // create ProductCategory default data ['Makanan', 'Minuman', 'Kebersihan']
        $makananId    = ProductCategory::create(['category' => 'Makanan', 'code' => 'MK'])->id;
        $minumanId    = ProductCategory::create(['category' => 'Minuman', 'code' => 'MN'])->id;
        $kebersihanId = ProductCategory::create(['category' => 'Kebersihan', 'code' => 'KB'])->id;

        // Seeder Product

        $products = [
            // 5 produk — KATEGORI: Makanan
            [
                "category_id" => $makananId,
                "name" => "Mi Instan Goreng Aceh (Indomie) - 1 pcs",
                "code" => "PRD-MK-0001",
                "brand" => "Indomie",
                "price_buy" => 2800,   // grosir per pcs
                "price_sell" => 3700
            ],
            [
                "category_id" => $makananId,
                "name" => "Kerupuk Udang Layar Kota Aceh - 1 pack",
                "code" => "PRD-MK-0002",
                "brand" => "Layar Kota",
                "price_buy" => 1000,
                "price_sell" => 1500
            ],
            [
                "category_id" => $makananId,
                "name" => "Biskuit Kelapa - 1 pack",
                "code" => "PRD-MK-0003",
                "brand" => "Roma",
                "price_buy" => 18000,
                "price_sell" => 25000
            ],
            [
                "category_id" => $makananId,
                "name" => "Kopi Bubuk Gayo (Grade commercial) - 250g",
                "code" => "PRD-MK-0004",
                "brand" => "Kopi Gayo",
                "price_buy" => 45000,
                "price_sell" => 60000
            ],
            [
                "category_id" => $makananId,
                "name" => "Ikan Asin Kecil - 1 pack",
                "code" => "PRD-MK-0005",
                "brand" => "IkanAsinAceh",
                "price_buy" => 12000,
                "price_sell" => 17000
            ],
            [
                "category_id" => $makananId,
                "name" => "Minyak Goreng Pouch 1L",
                "code" => "PRD-MK-0006",
                "brand" => "Tropical / Sovia / Sania",
                "price_buy" => 18000,
                "price_sell" => 23000
            ],
            [
                "category_id" => $makananId,
                "name" => "Margarine / Mentega Tawar 200-250g",
                "code" => "PRD-MK-0007",
                "brand" => "Blue Band / Filma",
                "price_buy" => 16000,
                "price_sell" => 21000
            ],
            [
                "category_id" => $makananId,
                "name" => "Gula Pasir 1 Kg",
                "code" => "PRD-MK-0008",
                "brand" => "Gulaku / Rose Brand",
                "price_buy" => 17500,
                "price_sell" => 20000
            ],

            // 5 produk — KATEGORI: Minuman
            [
                "category_id" => $minumanId,
                "name" => "Kopi Arabika Gayo - 1 Kg (green/roasted)",
                "code" => "PRD-MN-0001",
                "brand" => "Gayo (various roaster)",
                "price_buy" => 150000,
                "price_sell" => 190000
            ],
            [
                "category_id" => $minumanId,
                "name" => "Teh Botol Sosro 250 ml - per botol",
                "code" => "PRD-MN-0002",
                "brand" => "Sosro",
                "price_buy" => 3000,
                "price_sell" => 4500
            ],
            [
                "category_id" => $minumanId,
                "name" => "Susu Kental Manis Kaleng 370-490g",
                "code" => "PRD-MN-0003",
                "brand" => "Frisian Flag / Tiga Sapi",
                "price_buy" => 11000,
                "price_sell" => 15000
            ],
            [
                "category_id" => $minumanId,
                "name" => "Kopi Instan Sachet (Kopi 3 in 1) - per sachet",
                "code" => "PRD-MN-0004",
                "brand" => "Kapal Api / Nescafe",
                "price_buy" => 1800,
                "price_sell" => 2600
            ],
            [
                "category_id" => $minumanId,
                "name" => "Minuman Serbuk Instan (Teh/Jus) - 1 pack",
                "code" => "PRD-MN-0005",
                "brand" => "Various",
                "price_buy" => 8000,
                "price_sell" => 12000
            ],

            // 5 produk — KATEGORI: Kebersihan
            [
                "category_id" => $kebersihanId,
                "name" => "Sabun Cuci Piring 400 ml",
                "code" => "PRD-KB-0001",
                "brand" => "Sunlight / Mama Lemon",
                "price_buy" => 8500,
                "price_sell" => 12000
            ],
            [
                "category_id" => $kebersihanId,
                "name" => "Sikat / Alat Kebersihan (per pc)",
                "code" => "PRD-KB-0002",
                "brand" => "Generic",
                "price_buy" => 6000,
                "price_sell" => 9000
            ],
            [
                "category_id" => $kebersihanId,
                "name" => "Detergen Bubuk 1 Kg",
                "code" => "PRD-KB-0003",
                "brand" => "Rinso / So Klin",
                "price_buy" => 20000,
                "price_sell" => 25000
            ],
            [
                "category_id" => $kebersihanId,
                "name" => "Pembersih Lantai 800 ml - 1 botol",
                "code" => "PRD-KB-0004",
                "brand" => "Wipol / Bayclin",
                "price_buy" => 12000,
                "price_sell" => 16000
            ],
            [
                "category_id" => $kebersihanId,
                "name" => "Pewangi / Pelembut Pakaian 800 ml - 1 botol",
                "code" => "PRD-KB-0005",
                "brand" => "Molto / So Klin",
                "price_buy" => 13000,
                "price_sell" => 17000
            ],
        ];

        foreach ($products as $product) {
            $product = Product::create($product);
            StockPriode::create([
                'month' => Carbon::now()->month,
                'year' => Carbon::now()->year,
                'product_id' => $product->id,
            ]);
        }

        // Seeder Supplier

        $suppliers = [
            [
                "name" => "Merdeka Supply",
                "contact" => "+62 813-6161-6030",
                "address" => "Jl. Tgk. Imum Lueng Bata, Lamseupeung, Banda Aceh, Aceh"
            ],
            [
                "name" => "PT. Kelola Pangan Indonesia (Cabang Banda Aceh)",
                "contact" => "(0651) 7318958",
                "address" => "TPI Ulee Lheue / Banda Aceh (lokasi kantor-cabang), Banda Aceh, Aceh"
            ],
            [
                "name" => "Yayasan MPKG / Gayo Arabica Coffee (MPKG)",
                "contact" => "+62 823-6755-5559",
                "address" => "Gedung Pasar Lelang dan Komoditi, Jl. Pengulu Gayo, Paya Ilang, Bebesen, Kabupaten Aceh Tengah, Aceh"
            ],
            [
                "name" => "PT. Segar Kumala Indonesia (Cabang Aceh)",
                "contact" => "+62 21-4603125", // kontak pusat / korporat (untuk order cabang)
                "address" => "Jl. Kartini No.43-C, Peunayong, Kec. Kuta Alam, Kota Banda Aceh, Aceh 23127 (cabang Aceh)"
            ],
            [
                "name" => "Omura Powder Groceries (Bubuk Minuman)",
                "contact" => "081382688890",
                "address" => "Takengon, Kabupaten Aceh Tengah, Aceh"
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }

        // Seeder Member
        $members = [
            [
                'name' => 'M. Fadhlan',
                'address' => 'Jl. Politeknik Aceh No. 10, Batoh, Kuta Alam, Banda Aceh',
                'phone' => '0812-6060-1122',
            ],
            [
                'name' => 'Nur Aisyah',
                'address' => 'Jl. T. Imum Lueng Bata, Lamseupeung, Banda Aceh',
                'phone' => '0822-7888-4455',
            ],
            [
                'name' => 'Rizki Maulana',
                'address' => 'Jl. T. Nyak Arief, Batoh, Banda Aceh',
                'phone' => '0852-6111-3344',
            ],
            [
                'name' => 'Cut Intan',
                'address' => 'Jl. Inong Balee, Darussalam, Banda Aceh',
                'phone' => '0813-7000-8899',
            ],
            [
                'name' => 'Teuku Zulfikar',
                'address' => 'Jl. Mr. Mohd Hasan, Batoh, Banda Aceh',
                'phone' => '0878-9990-2211',
            ],
        ];

        foreach ($members as $member) {
            Member::create($member);
        }

        $this->call([
            ItemInSeeder::class,
        ]);
    }
}
