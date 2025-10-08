@extends('layout')

@section('title', $title)

@section('content')
@php
use Illuminate\Support\Js;
@endphp
@if ($isReportShowed)
<main class="overflow-x-auto w-full p-4 border border-pink-200 bg-white rounded-xl">
    <div class="inline-flex justify-between items-center w-full mb-4">
        <a href="{{ route('stock-report.index') }}"
            class="inline-block bg-pink-500 text-white px-4 py-2 mb-2 rounded-lg hover:bg-pink-600">
            Kembali
        </a>

        <a href="{{ route('stock-report.cetak', [
                'product' => $product_id_selected,
                'month' => $month_selected,
                'year' => $year_selected,
            ]) }}" target="_blank"
            class="inline-block bg-pink-500 text-white px-4 py-2 mb-2 rounded-lg hover:bg-pink-600">
            Cetak Laporan
        </a>
    </div>
    <h2 class="text-xl font-semibold mb-2">{{ $table_body['product_name'] }} - {{ $table_body['product_code'] }}</h2>
    <h3 class="text-base font-medium italic mb-4">Stock Awal periode : {{ $table_body['starting_stock'] }}</h3>
    <table class="min-w-full border border-pink-200 bg-white rounded-b-xl overflow-hidden">
        <thead class="bg-pink-100">
            <tr>
                <th class="px-4 py-2 border-b border-pink-200 text-left">Kode Transaksi</th>
                <th class="px-4 py-2 border-b border-pink-200 text-left">Tanggal</th>
                <th class="px-4 py-2 border-b border-pink-200 text-left">Jumlah Masuk</th>
                <th class="px-4 py-2 border-b border-pink-200 text-left">Jumlah Keluar</th>
                <th class="px-4 py-2 border-b border-pink-200 text-left">Stok Akhir</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($table_body['stock'] as $stock)
            <tr class="{{ $loop->even ? 'bg-pink-50' : '' }}">
                <td class="px-4 py-2 border-b border-pink-200">{{ $stock['invoice'] }}</td>
                <td class="px-4 py-2 border-b border-pink-200">{{ \Carbon\Carbon::parse($stock['tanggal'])->format('d M Y') }}</td>
                <td class="px-4 py-2 border-b border-pink-200">{{ $stock['masuk'] }}</td>
                <td class="px-4 py-2 border-b border-pink-200">{{ $stock['keluar'] }}</td>
                <td class="px-4 py-2 border-b border-pink-200">{{ $stock['sisa'] }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="px-4 py-2 text-center text-gray-500">Tidak ada data transaksi untuk produk ini pada bulan dan tahun yang dipilih.</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot class="bg-pink-100 font-semibold">
            <tr>
                <td colspan="4" class="px-4 py-2 border-t border-pink-200 text-right">Stok Akhir periode:</td>
                <td class="px-4 py-2 border-t border-pink-200">{{ $table_body['final_stock'] }}</td>
            </tr>
        </tfoot>
    </table>
</main>
@else
<main x-data="{
    products: {{ Js::from($products) }},
    categories: {{ Js::from($categories) }},
    selectedCategory: null,
    selectedProductId: null,
    searchQuery: '',
    page: 1,
    perPage: 10,
    get filteredProducts() {
        let filtered = this.products;

        if (this.selectedCategory) {
            filtered = filtered.filter(product => product.category_id === this.selectedCategory);
        }

        if (this.searchQuery) {
            const query = this.searchQuery.toLowerCase();
            filtered = filtered.filter(product =>
                product.name.toLowerCase().includes(query) ||
                product.sku.toLowerCase().includes(query)
            );
        }

        // reset page jika hasil tidak cukup untuk halaman aktif
        if ((this.page - 1) * this.perPage >= filtered.length) {
            this.page = 1;
        }

        return filtered;
    },
    get paged() {
        const start = (this.page - 1) * this.perPage;
        return this.filteredProducts.slice(start, start + this.perPage);
    },
    get totalPages() {
        return Math.ceil(this.filteredProducts.length / this.perPage) || 1;
    }
}" class="grid grid-cols-5 gap-4">
    <!-- Sidebar Kategori -->
    <section class="col-span-1 flex flex-col gap-y-2 w-full">
        <h2 class="text-lg font-semibold text-center py-2 border border-pink-200 bg-white rounded-lg">Kategori</h2>
        <button class="px-4 py-2 border border-pink-200 rounded-lg cursor-pointer transition"
            :class="{
                'bg-white hover:bg-pink-200': selectedCategory != null,
                'bg-pink-200 hover:bg-pink-400 hover:text-white': selectedCategory == null
            }" @click="selectedCategory = null; page = 1">
            Semua
        </button>
        <template x-for="(category, index) in categories" :key="'CT-' + index">
            <button class="px-4 py-2 border border-pink-200 rounded-lg cursor-pointer transition"
                :class="{
                    'bg-white hover:bg-pink-200': selectedCategory != category.id,
                    'bg-pink-200 hover:bg-pink-400 hover:text-white': selectedCategory === category.id
                }"
                x-text="category.category"
                @click="selectedCategory = category.id; page = 1">
            </button>
        </template>
    </section>

    <!-- Form & Konten -->
    <form action="{{ route('stock-report.detail') }}" method="post" @submit="loading = true" class="col-span-4 flex flex-col gap-y-2 w-full">
        @csrf
        <section class="flex flex-wrap gap-4 justify-between items-center w-full px-4 py-2 border border-pink-200 bg-white rounded-lg">
            <div class="inline-flex items-center">
                <label for="month" class="block text-gray-700 font-bold me-2">Bulan:</label>
                <select id="month" name="month" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                    <option value="" {{ $month_selected == null ? 'selected' : '' }}>Pilih Bulan</option>
                    @foreach ($months as $index => $month)
                    @php $month_num = $index + 1; @endphp
                    <option value="{{ $month_num }}" {{ $month_selected == $month_num ? 'selected' : '' }}>
                        {{ $month }}
                    </option>
                    @endforeach
                </select>
                @error('month')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="inline-flex items-center">
                <label for="year" class="block text-gray-700 font-bold me-2">Tahun:</label>
                <select id="year" name="year" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                    <option value="" {{ $year_selected == null ? 'selected' : '' }}>Pilih Tahun</option>
                    @for ($year = $years; $year >= 2000; $year--)
                    <option value="{{ $year }}" {{ $year_selected == $year ? 'selected' : '' }}>
                        {{ $year }}
                    </option>
                    @endfor
                </select>
                @error('year')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit"
                class="bg-pink-500 text-white px-4 py-2 rounded-lg hover:bg-pink-600">
                Tampilkan Laporan
            </button>
        </section>

        <!-- Search -->
        <input type="text"
            placeholder="Cari produk berdasarkan nama atau SKU"
            x-model="searchQuery"
            class="w-full px-4 py-2 border border-pink-200 bg-white rounded-lg focus:outline-pink-400">

        <!-- Hidden input buat kirim ID product -->
        <input type="hidden" name="product_id" :value="selectedProductId">

        <!-- Produk -->
        <section class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <template x-if="paged.length > 0">
                <template x-for="(product, index) in paged" :key="'PR-' + index">
                    <button type="button"
                        class="border border-pink-300 rounded-lg p-4 flex flex-col gap-y-2"
                        @click="selectedProductId = product.id"
                        :class="selectedProductId === product.id ? 'border-pink-400 bg-pink-300' : 'bg-white hover:bg-pink-200 transition-colors'">
                        <h3 class="text-lg font-semibold" x-text="product.name"></h3>
                        <p class="text-sm text-gray-600" x-text="'Kode: ' + product.code"></p>
                        <p class="text-sm text-gray-600" x-text="'Kategori: ' + (categories.find(cat => cat.id === product.category_id)?.category || 'Tidak Diketahui')"></p>
                        <p class="text-sm text-gray-600" x-text="'Stok: ' + product.quantity"></p>
                    </button>
                </template>
            </template>
            <template x-if="paged.length === 0">
                <p class="col-span-full text-center text-gray-500">Tidak ada produk yang ditemukan.</p>
            </template>
        </section>

        <!-- Pagination -->
        <div class="flex flex-col md:flex-row justify-between items-center w-full px-4 py-2 border border-pink-200 bg-white rounded-lg mt-4 gap-4">
            <div class="text-sm text-gray-500">
                Menampilkan <span x-text="paged.length"></span> dari <span x-text="filteredProducts.length"></span> data
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="page = Math.max(1, page - 1)" :disabled="page === 1"
                    class="px-3 py-1 rounded bg-pink-100 text-pink-700 hover:bg-pink-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    &laquo;
                </button>
                <template x-for="i in totalPages" :key="i">
                    <button type="button" @click="page = i"
                        :class="page === i ? 'bg-pink-500 text-white' : 'bg-pink-100 text-pink-700 hover:bg-pink-200'"
                        class="px-3 py-1 rounded transition-colors font-semibold">
                        <span x-text="i"></span>
                    </button>
                </template>
                <button type="button" @click="page = Math.min(totalPages, page + 1)" :disabled="page === totalPages"
                    class="px-3 py-1 rounded bg-pink-100 text-pink-700 hover:bg-pink-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    &raquo;
                </button>
            </div>
            <div>
                <label class="text-sm text-gray-500 mr-2">Baris per halaman:</label>
                <select x-model="perPage"
                    class="border border-pink-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-pink-400">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </form>
</main>
@endif
@endsection
