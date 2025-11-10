<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>

    <!-- Styles -->
    @vite(['resources/css/app.css'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <style>
        .bg-gradient-text {
            background: linear-gradient(to right, #ec4899, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            color: transparent;
        }

        @media print {
            * {
                -webkit-print-color-adjust: exact !important;
                /* Chrome, Safari */
                print-color-adjust: exact !important;
                /* Firefox */
                color-adjust: exact !important;
                /* Edge lama */
            }

            @page {
                size: auto;
                /* mengikuti setting kertas printer */
                margin: 10mm;
            }

            body {
                background: white;
                margin: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body class="bg-gray-100" onload="window.print()"
    x-data='{ transaction: @json($transaction) }'>

    <div class="max-w-3xl mx-auto bg-white p-8 shadow print:shadow-none print:p-0 print:rounded-none">
        <!-- Header Invoice -->
        <div class="flex justify-between items-end border-b pb-4 mb-4">
            <div>
                <div class="flex items-center gap-3 mb-2">
                    <!-- SVG Icon: Inventory Rack -->
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64"
                        class="w-10 h-10">
                        <defs>
                            <linearGradient id="grad1" x1="0%" y1="0%" x2="100%" y2="100%">
                                <stop offset="0%" style="stop-color:#ec4899;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#8b5cf6;stop-opacity:1" />
                            </linearGradient>
                        </defs>
                        <rect x="8" y="8" width="48" height="48" rx="4" ry="4" fill="url(#grad1)" />
                        <g stroke="white" stroke-width="2" fill="none">
                            <rect x="16" y="16" width="12" height="12" rx="2" />
                            <rect x="36" y="16" width="12" height="12" rx="2" />
                            <rect x="16" y="36" width="12" height="12" rx="2" />
                            <rect x="36" y="36" width="12" height="12" rx="2" />
                        </g>
                    </svg>

                    <!-- Gradient Text -->
                    <h1 class="text-2xl font-extrabold tracking-wide bg-gradient-text">
                        UMKM Manajemen
                    </h1>
                </div>
                <h1 class="text-2xl font-bold">INVOICE</h1>
                <p class="text-sm text-gray-500">
                    <span class="inline-flex justify-between w-20"><span>No</span> <span>:</span></span>
                    <span x-text="'TR-' + transaction.id.toString().padStart(4,'0')"></span>
                </p>
            </div>
            <div class="text-right">
                <p class="font-medium text-gray-700" x-text="transaction.user.name"></p>
                <p class="text-sm text-gray-500">Tanggal:
                    <span x-text="new Date(transaction.created_at).toLocaleDateString()"></span>
                </p>
            </div>
        </div>
        <div x-show="transaction.member != null" class="border-b pb-4 mb-4">
            <h2 class="text-2xl font-bold">Member</h2>
            <div class="flex items-center space-x-4">
                <div>
                    <p class="font-medium text-gray-800" x-text="transaction.member.name"></p>
                    <p class="text-sm text-gray-500" x-text="transaction.member.phone"></p>
                    <p class="text-sm text-gray-500" x-text="transaction.member.address"></p>
                </div>
            </div>
        </div>

        <!-- Detail Items -->
        <table class="w-full text-left border border-gray-200 mb-4 print:border-collapse">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-3 py-2 border">Produk</th>
                    <th class="px-3 py-2 border">Qty</th>
                    <th class="px-3 py-2 border">Harga</th>
                    <th class="px-3 py-2 border">Diskon</th>
                    <th class="px-3 py-2 border">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="detail in transaction.details" :key="detail.id">
                    <tr>
                        <td class="px-3 py-2 border" x-text="detail.product.name"></td>
                        <td class="px-3 py-2 border" x-text="detail.quantity"></td>
                        <td class="px-3 py-2 border">
                            <div class="inline-flex justify-between w-full">
                                <span>Rp</span> <span x-text="detail.price.toLocaleString()"></span>
                            </div>
                        </td>
                        <td class="px-3 py-2 border">
                            <div class="inline-flex justify-between w-full">
                                <span>Rp</span> <span x-text="detail.discount.toLocaleString()"></span>
                            </div>
                        </td>
                        <td class="px-3 py-2 border">
                            <div class="inline-flex justify-between w-full">
                                <span>Rp</span> <span x-text="detail.subtotal.toLocaleString()"></span>
                            </div>
                        </td>
                    </tr>
                </template>
            </tbody>
            <tfoot class="bg-gray-50" x-data="{
                total: transaction.details.reduce((acc, item) => acc + item.subtotal, 0).toLocaleString(),
            }">
                <tr>
                    <td colspan="4" class="px-3 py-2 border text-right font-semibold">Total</td>
                    <td class="px-3 py-2 border">
                        <div class="inline-flex justify-between w-full">
                            <span>Rp</span> <span x-text="total"></span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="px-3 py-2 border text-right font-semibold">Diskon</td>
                    <td class="px-3 py-2 border">
                        <div class="inline-flex justify-between w-full">
                            <span>Rp</span> <span x-text="transaction.discount.toLocaleString()"></span>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4" class="px-3 py-2 border text-right font-bold">Total Bayar</td>
                    <td class="px-3 py-2 border font-bold">
                        <div class="inline-flex justify-between w-full">
                            <span>Rp</span> <span x-text="transaction.total_price.toLocaleString()"></span>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>

        <!-- Footer -->
        <div class="text-center text-sm text-gray-500 mt-6 border-t pt-4">
            <p>Terima kasih atas pembelian Anda 🙏</p>
            <p>Barang yang sudah dibeli tidak dapat dikembalikan</p>
        </div>
    </div>

</body>

</html>
