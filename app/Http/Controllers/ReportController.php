<?php

namespace App\Http\Controllers;

use App\Models\{Product, Transaction, TransactionDetail};
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    static private $months = [
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

    private static $report_types = [
        'stock-barang' => [
            'header' => 'Stock Barang',
            'table_header' => [
                ['key' => 'code', 'label' => 'Kode Produk'],
                ['key' => 'name', 'label' => 'Nama Produk'],
                ['key' => 'brand', 'label' => 'Merek'],
                ['key' => 'quantity', 'label' => 'Jumlah'],
                ['key' => 'aksi', 'label' => 'Aksi'],
            ],
        ],
        'penjualan' => [
            'header' => 'Penjualan',
            'table_header' => [
                ['key' => 'invoice', 'label' => 'Invoice'],
                ['key' => 'created_at', 'label' => 'Tanggal'],
                ['key' => 'member', 'label' => 'Member'],
                ['key' => 'total_item', 'label' => 'Total Item'],
                ['key' => 'total', 'label' => 'Total'],
                ['key' => 'discount', 'label' => 'Diskon'],
                ['key' => 'grand_total', 'label' => 'Grand Total'],
                ['key' => 'is_paid', 'label' => 'Status Pembayaran'],
                ['key' => 'admin', 'label' => 'Admin'],
                ['key' => 'aksi', 'label' => 'Aksi'],
            ],
        ],
    ];

    static private function report(string $report_key, $month, $year)
    {
        $isCurrentPeriode = $month == Carbon::now()->month && $year == Carbon::now()->year;

        if ($report_key === 'stock-barang') {
            $product = Product::with([
                'category',
                'stockPriodes' => function ($query) use ($month, $year) {
                    $query->whereMonth('created_at', $month)
                        ->whereYear('created_at', $year);
                },
            ])
                ->orderBy('code')
                ->get();

            return $product->map(function ($item) use ($isCurrentPeriode, $month, $year) {
                $quantity = 0;

                if (Carbon::now()->month == $month) {
                    $quantity = $item->quantity;
                }

                $quantity = (function () use ($item, $month, $year) {
                    $stockPriodes = $item->stockPriodes->sortBy('created_at')->values();

                    // If stockPriodes is not empty, just return the selected fields
                    if ($stockPriodes->isNotEmpty()) {
                        return $stockPriodes->map(function ($item) {
                            return [
                                'starting_stock' => $item->starting_stock,
                                'final_stockes' => $item->final_stock,
                            ];
                        })->toArray()[0]['final_stockes'];
                    }

                    // If empty, try to get previous periode's final_stock
                    $prevPriode = $item->stockPriodes()
                        ->where(function ($query) use ($month, $year) {
                            // Get all before current month/year
                            $query->where(function ($q) use ($month, $year) {
                                $q->whereYear('created_at', '<', $year)
                                    ->orWhere(function ($q2) use ($month, $year) {
                                        $q2->whereYear('created_at', $year)
                                            ->whereMonth('created_at', '<', $month);
                                    });
                            });
                        })
                        ->orderBy('created_at', 'desc')
                        ->first();

                    return $prevPriode ? $prevPriode->final_stock : 0;
                })();

                $data = [
                    'code' => $item->code,
                    'name' => $item->name,
                    'brand' => $item->brand,
                    'quantity' => $quantity,
                ];

                if ($isCurrentPeriode) {
                    $data['aksi'] = route('product.edit', ['product' => $item->id]);
                }

                return $data;
            });
        } elseif ($report_key === 'penjualan') {
            $transaction = Transaction::whereMonth('created_at', $month)
                ->whereYear('created_at', $year)
                ->orderByDesc('created_at')
                ->get();

            return $transaction->map(function ($transaction) use ($isCurrentPeriode) {
                $data = [
                    'invoice' => 'TR-' . str_pad($transaction->id, 4, '0', STR_PAD_LEFT),
                    'created_at' => $transaction->created_at->format('d/m/Y H:i'),
                    'member' => $transaction->member ? $transaction->member->name : 'Pelanggan',
                    'total_item' => $transaction->total_item,
                    'total' => 'Rp ' . number_format($transaction->total_price + $transaction->discount, 0, ',', '.'),
                    'discount' => 'Rp ' . number_format($transaction->discount, 0, ',', '.'),
                    'grand_total' => 'Rp ' . number_format($transaction->total_price, 0, ',', '.'),
                    'admin' => $transaction->user->name,
                    'is_paid' => $transaction->is_paid == true ? 'Dibayar' : 'Belum Dibayar',
                ];

                if ($isCurrentPeriode) {
                    $data['aksi'] = route('transaction.detail', ['id' => $transaction->id]);
                }

                return $data;
            });
        }
    }

    public function index(Request $request)
    {
        // define variable for view
        $data = [
            'title' => 'Laporan',
            'isReportShowed' => false,
            'data' => [],
            'months' => self::$months,
            'years' => Carbon::now()->year,
            'report_types' => self::$report_types,
            'report_type_selected' => null,
            'month_selected' => Carbon::now()->month,
            'year_selected' => Carbon::now()->year,
        ];

        if ($request->isMethod('POST')) {
            $validated = $request->validate([
                'report_type' => 'required',
                'month' => 'required',
                'year' => 'required',
            ]);

            $data['isReportShowed'] = true;

            foreach ($validated as $key => $value) {
                $data[$key . '_selected'] = $value;
            }

            $data['table'] = [
                'header' => self::$report_types[$validated['report_type']]['table_header'],
                'rows' => self::report($validated['report_type'], $validated['month'], $validated['year']),
            ];

            $data['title'] = $data['title'] . ' - ' . self::$report_types[$validated['report_type']]['header'] . ' - ' . self::$months[$validated['month'] - 1] . ' ' . $validated['year'];
        }


        return view('reports.index', $data);
    }

    // define function to show print report page
    public function cetak($report_key, $month, $year)
    {
        // validate report key
        if (!array_key_exists($report_key, self::$report_types)) {
            return redirect()->route('report.index')->with('error', 'Jenis laporan tidak valid.');
        }

        // define data table without aksi column
        $data_table = [
            'header' => array_filter(self::$report_types[$report_key]['table_header'], function ($column) {
                return $column['key'] !== 'aksi';
            }),
            'rows' => self::report($report_key, $month, $year)->map(function ($row) {
                return collect($row)->except(['aksi'])->toArray();
            }),
        ];

        // define variable for view
        $data = [
            'title' => self::$report_types[$report_key]['header'] . ' - ' . self::$months[$month - 1] . ' ' . $year,
            'table' => $data_table,
        ];

        return view('reports.cetak', $data);
    }

    /* Trend Section */
    static private function trend_data($currentMonth, $currentYear)
    {


        return TransactionDetail::query()
            ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
            ->with(['product:id,name,brand'])
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->groupBy('product_id')
            ->havingRaw('SUM(quantity) > 0')
            ->orderByDesc('total_quantity')
            ->get();
    }

    public function trend_index(Request $request)
    {
        $currentMonth = Carbon::now()->month;
        $currentYear  = Carbon::now()->year;
        $trendProducts = self::trend_data($currentMonth, $currentYear);

        $data = [
            'title' => 'Trend Produk',
            'periode' =>  self::$months[$currentMonth - 1] . ' - ' . $currentYear,
            'months' => self::$months,
            'years' => Carbon::now()->year,
            'trends' => $trendProducts,
            'month_selected' => Carbon::now()->month,
            'year_selected' => Carbon::now()->year,
            'cta_cetak' => [
                'month' => $currentMonth,
                'year' => $currentYear,
            ],
        ];

        if ($request->isMethod('POST')) {
            $validated = $request->validate([
                'month' => 'required',
                'year' => 'required',
            ]);

            if ($validated['month'] + 1 == Carbon::now()->month && $validated['year'] == Carbon::now()->year) {
                return redirect()->route('trend.index');
            }

            $currentMonth = $validated['month'];
            $currentYear  = $validated['year'];
            $trendProducts = self::trend_data($currentMonth, $currentYear);

            $data['trends'] = $trendProducts;
            $data['month_selected'] = $currentMonth;
            $data['year_selected'] = $currentYear;
            $data['periode'] = self::$months[$currentMonth - 1] . ' - ' . $currentYear;
            $data['cta_cetak'] = [
                'month' => $currentMonth,
                'year' => $currentYear,
            ];
        }


        return view('trend.index', $data);
    }

    public function trend_cetak($month, $year)
    {
        $trendProducts = self::trend_data($month, $year);

        $data = [
            'title' => 'Cetak Trend Produk',
            'periode' =>  self::$months[$month - 1] . ' - ' . $year,
            'trends' => $trendProducts,
        ];

        return view('trend.cetak', $data);
    }
}
