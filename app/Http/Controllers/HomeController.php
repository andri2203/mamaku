<?php

namespace App\Http\Controllers;

use App\Models\{ItemInDetail, ItemOutDetail, Product, Transaction, TransactionDetail};
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    private function getMonth(int $month): string
    {
        $bulan = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];

        return $bulan[$month - 1];
    }

    // define faunction to show dashboard
    public function index()
    {
        $currentMonth = Carbon::now()->month;
        $currentYear  = Carbon::now()->year;

        // get total transaction price
        $totalPrice = Transaction::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->sum('total_price');

        // get total Produk Masuk
        $itemIn = ItemInDetail::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)->select(DB::raw('SUM(quantity) as total_quantity'))->pluck('total_quantity');

        // get total Produk Keluar
        $itemOut = ItemOutDetail::whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)->select(DB::raw('SUM(quantity) as total_quantity'))->pluck('total_quantity');

        // get Detail Transaksi
        $transactionDetail = TransactionDetail::with('product:id,name') // ambil relasi product, hanya id & name
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear);

        // get total Produk Keluar
        $itemSell = $transactionDetail->select(DB::raw('SUM(quantity) as total_quantity'))->pluck('total_quantity');

        // get Detail Transaksi for trend
        $trendProducts = $transactionDetail->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('product_id')
            ->orderByDesc('total_quantity')
            ->limit(10)
            ->get();

        // get produk dengan stock 0
        $products = Product::where('quantity', "<=", 5)->orderByDesc('quantity')->get(['name', 'quantity']);

        $data = [
            'title' => 'Beranda',
            'title_periode' => $this->getMonth($currentMonth) . " " . $currentYear,
            'total_income' =>  $totalPrice,
            'itemIn' =>  $itemIn,
            'itemOut' =>  $itemOut,
            'itemSell' =>  $itemSell,
            'products' =>  $products,
            'trend_product' => [
                'labels' =>  $trendProducts->pluck('product.name')->toArray(),
                'data' =>  $trendProducts->pluck('total_quantity')->toArray(),
            ]
        ];
        return view('dashboard', $data);
    }
}
