<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Product, ProductCategory};
use Carbon\Carbon;

class StockReportController extends Controller
{
    // define months static property
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

    // define table header static property for product stock report
    private static $table_header = [
        ['key' => 'invoice', 'label' => 'Invoice'],
        ['key' => 'tanggal', 'label' => 'Tanggal'],
        ['key' => 'masuk', 'label' => 'Masuk'],
        ['key' => 'keluar', 'label' => 'Keluar'],
        ['key' => 'sisa', 'label' => 'Sisa'],
        ['key' => 'aksi', 'label' => 'Aksi'],
    ];

    // define static method to get product stock report
    static private function report($product_id, $month, $year)
    {
        // get product by id with all relations including itemInDetails, itemOutDetails, transactionDetails & StockPriode
        $product = Product::with([
            'itemInDetails' => function ($query) use ($month, $year) {
                $query->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year);
            },
            'itemOutDetails' => function ($query) use ($month, $year) {
                $query->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year);
            },
            'transactionDetails' => function ($query) use ($month, $year) {
                $query->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year);
            },
            'stockPriodes' => function ($query) use ($month, $year) {
                $query->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year);
            },
        ])->find($product_id);

        if (!$product) {
            return null;
        }

        $productStock = collect();

        // loop all relations and merge to productStock collection
        foreach ($product->itemInDetails as $itemIn) {
            $productStock->push([
                'invoice' => 'In-' . str_pad($itemIn->itemIn->id, 4, '0', STR_PAD_LEFT),
                'tanggal' => Carbon::parse($itemIn->created_at)->format('d-m-Y H:i:s'),
                'masuk' => $itemIn->quantity,
                'keluar' => 0,
                'sisa' => 0,
                'aksi' => '',
            ]);
        }

        foreach ($product->itemOutDetails as $itemIn) {
            $productStock->push([
                'invoice' => 'Out-' . str_pad($itemIn->itemOut->id, 4, '0', STR_PAD_LEFT),
                'tanggal' => Carbon::parse($itemIn->created_at)->format('d-m-Y H:i:s'),
                'masuk' => 0,
                'keluar' => $itemIn->quantity,
                'sisa' => 0,
                'aksi' => '',
            ]);
        }

        foreach ($product->transactionDetails as $itemIn) {
            $productStock->push([
                'invoice' => 'TR-' . str_pad($itemIn->transaction->id, 4, '0', STR_PAD_LEFT),
                'tanggal' => Carbon::parse($itemIn->created_at)->format('d-m-Y H:i:s'),
                'masuk' => 0,
                'keluar' => $itemIn->quantity,
                'sisa' => 0,
                'aksi' => '',
            ]);
        }

        $stock_priodes = (function () use ($product, $month, $year) {
            $stockPriodes = $product->stockPriodes->sortBy('created_at')->values();

            // If stockPriodes is not empty, just return the selected fields
            if ($stockPriodes->isNotEmpty()) {
                return $stockPriodes->map(function ($item) {
                    return [
                        'starting_stock' => $item->starting_stock,
                        'final_stock' => $item->final_stock,
                    ];
                })->toArray();
            }

            // If empty, try to get previous periode's final_stock
            $prevPriode = $product->stockPriodes()
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

            return [
                'starting_stock' => $prevPriode ? $prevPriode->final_stock : 0,
                'final_stock' => 0,
            ];
        })();


        $stock_priode = isset($stock_priodes[0]) ? $stock_priodes[0] : $stock_priodes;

        $stocks = $productStock->sortBy('tanggal')->values();
        for ($index = 0; $index < $stocks->count(); $index++) {
            $item = $stocks[$index];

            if ($index === 0) {
                $item['sisa'] = $stock_priode['starting_stock'] + $item['masuk'] - $item['keluar'];
            } else {
                $item['sisa'] = $stocks[$index - 1]['sisa'] + $item['masuk'] - $item['keluar'];
            }

            $stocks[$index] = $item;
        }

        return [
            'product_name' => $product->name . ' (' . $product->brand . ')',
            'product_code' => $product->code,
            'stock' => $stocks->toArray(),
            ...$stock_priode,
        ];
    }

    // define function to show index page
    public function index(Request $request)
    {
        // get all products
        $products = Product::all();

        // get all categories
        $categories = ProductCategory::all();

        $data = [
            'title' => 'Laporan Stock Barang',
            'months' => self::$months,
            'isReportShowed' => false,
            'years' => Carbon::now()->year,
            'table_header' => self::$table_header,
            'table_body' => [],
            'products' => $products,
            'categories' => $categories,
            'product_id_selected' => null,
            'month_selected' => Carbon::now()->month,
            'year_selected' => Carbon::now()->year,
        ];

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'product_id' => 'required|exists:products,id',
                'month' => 'required',
                'year' => 'required',
            ]);

            foreach ($validated as $key => $value) {
                $data[$key . '_selected'] = $value;
            }

            $data['isReportShowed'] = true;

            $reports = self::report($validated['product_id'], $validated['month'], $validated['year']);

            if (!$reports) {
                return redirect()->route('stock-report.index')->with('success', 'Produk tidak ditemukan.');
            }
            $data['title'] = $data['title']  . ' - ' . self::$months[$validated['month'] - 1] . ' ' . $validated['year'];
            $data['table_body'] = $reports;
        }

        return view('reports.stock', $data);
    }

    public function cetak($product, $month, $year)
    {
        $reports = self::report($product, $month, $year);

        if (!$reports) {
            return redirect()->route('stock-report.index')->with('success', 'Produk tidak ditemukan.');
        }

        $data = [
            'title' => 'Laporan Stock Barang - ' . $reports['product_name'] . ' - ' . self::$months[$month - 1] . ' ' . $year,
            'table' => [
                // exclude aksi column
                'header' => array_filter(self::$table_header, function ($column) {
                    return $column['key'] !== 'aksi';
                }),
                'rows' => $reports['stock'],
                'foot' => [
                    'starting_stock' => $reports['starting_stock'],
                    'final_stock' => $reports['final_stock'],
                ],
            ],
        ];

        return view('reports.cetak', $data);
    }
}
