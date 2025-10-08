@extends('layout')

@section('title', $title)

@section('content')
<main
    class="w-full mx-auto space-y-6"
    x-data='{ transaction: @json($transaction) }'>
    <div class="inline-flex justify-between items-center w-full">
        <a href="{{ route('transaction.index') }}" class="bg-pink-500 text-white px-6 py-3 rounded-lg shadow hover:bg-pink-600 transition-colors font-semibold">
            Kembali
        </a>

        <a href="{{ route('transaction.cetak', ['id' => $id]) }}" class="bg-pink-500 text-white px-6 py-3 rounded-lg shadow hover:bg-pink-600 transition-colors font-semibold" target="_blank">
            Cetak
        </a>
    </div>

    <!-- Header -->
    <section class="bg-white shadow rounded-2xl p-6">
        <div class="inline-flex justify-between items-center w-full">
            <div>

                <h1 class="text-2xl font-bold text-gray-800 mb-2">
                    Detail Transaksi #<span x-text="'TR-' + transaction.id.toString().padStart(4, '0')"></span>
                </h1>
                <p class="text-sm text-gray-500">
                    Dibuat pada <span x-text="new Date(transaction.created_at).toLocaleString()"></span>
                </p>
            </div>
            <div class="inline-flex">
                <a :href="'/transaction/' + transaction.id + '/edit'" class="bg-green-500 text-white px-3 py-1 hover:bg-green-600 transition-colors text-sm font-medium">Edit</a>
                <form :action="'/transaction/' + transaction.id" method="POST" class="inline" @submit="loading = true">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('Yakin ingin menghapus data ini?')" class="bg-red-500 text-white px-3 py-1 rounded-r hover:bg-red-600 transition-colors text-sm font-medium cursor-pointer">Hapus</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Info Utama -->
    <section class="bg-white shadow rounded-2xl p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <h2 class="text-lg font-semibold text-gray-700 mb-3">Informasi Transaksi</h2>
            <ul class="space-y-2 text-gray-600">
                <li>Total Item: <span class="font-medium" x-text="transaction.total_item"></span></li>
                <li>Total: Rp <span class="font-medium" x-text="transaction.details.reduce((acc, item) => acc + item.subtotal, 0).toLocaleString()"></span></li>
                <li>Diskon: Rp <span class="font-medium" x-text="transaction.discount.toLocaleString()"></span></li>
                <li>Grand Total: Rp <span class="font-medium" x-text="transaction.total_price.toLocaleString()"></span></li>
                <li>Status Bayar:
                    <span class="px-2 py-1 rounded text-white text-sm"
                        :class="transaction.is_paid ? 'bg-green-500' : 'bg-red-500'"
                        x-text="transaction.is_paid ? 'Lunas' : 'Belum Lunas'">
                    </span>
                </li>
            </ul>
        </div>

        <div>
            <h2 class="text-lg font-semibold text-gray-700 mb-3">Dibuat Oleh</h2>
            <div class="flex items-center space-x-4 mb-4">
                <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                    <span class="text-lg" x-text="transaction.user.name.charAt(0)"></span>
                </div>
                <div>
                    <p class="font-medium text-gray-800" x-text="transaction.user.name"></p>
                    <p class="text-sm text-gray-500" x-text="transaction.user.email"></p>
                    <p class="text-xs text-gray-400">Level: <span x-text="transaction.user.level"></span></p>
                </div>
            </div>

            <div x-show="transaction.member != null">
                <h2 class="text-lg font-semibold text-gray-700 mb-3">Member</h2>
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                        <span class="text-lg" x-text="transaction.member.name.charAt(0)"></span>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800" x-text="transaction.member.name"></p>
                        <p class="text-sm text-gray-500" x-text="transaction.member.phone"></p>
                        <p class="text-sm text-gray-500" x-text="transaction.member.address"></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Detail Items -->
    <section class="bg-white shadow rounded-2xl p-6">
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Detail Item</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-left border border-gray-200 rounded-lg overflow-hidden">
                <thead class="bg-gray-100 text-gray-700">
                    <tr>
                        <th class="px-4 py-2">Produk</th>
                        <th class="px-4 py-2">Qty</th>
                        <th class="px-4 py-2">Harga</th>
                        <th class="px-4 py-2">Diskon</th>
                        <th class="px-4 py-2">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="detail in transaction.details" :key="detail.id">
                        <tr class="border-t border-gray-200">
                            <td class="px-4 py-2" x-text="detail.product.name"></td>
                            <td class="px-4 py-2" x-text="detail.quantity"></td>
                            <td class="px-4 py-2">Rp <span x-text="detail.price.toLocaleString()"></span></td>
                            <td class="px-4 py-2">Rp <span x-text="detail.discount.toLocaleString()"></span></td>
                            <td class="px-4 py-2">Rp <span x-text="detail.subtotal.toLocaleString()"></span></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </section>

</main>
@endsection
