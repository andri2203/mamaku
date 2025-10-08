@extends('layout')

@section('title', $title)

@section('content')

<main>
    <section
        x-data="{
            products: {{ Illuminate\Support\Js::from($products) }},
            categories: {{ Illuminate\Support\Js::from($categories) }},
            selectedCategory: '',
            search: '',
            showDetail: false,
            detailProduct: null,
            get filteredProducts() {
                let filtered = this.products;
                if (this.selectedCategory) {
                    filtered = filtered.filter(p => p.category_id == this.selectedCategory);
                }
                if (this.search) {
                    filtered = filtered.filter(p =>
                        p.code.toLowerCase().includes(this.search.toLowerCase()) ||
                        p.name.toLowerCase().includes(this.search.toLowerCase()) ||
                        p.brand.toLowerCase().includes(this.search.toLowerCase())
                    );
                }
                return filtered;
            },
            openDetail(product) {
                this.detailProduct = product;
                this.showDetail = true;
            },
            closeDetail() {
                this.showDetail = false;
                this.detailProduct = null;
            }
        }"
        class="flex flex-col items-start justify-start p-4 rounded-lg shadow-md bg-white border border-pink-200">
        <div class="flex flex-col md:flex-row gap-2 mb-4 w-full">
            <a href="{{ route('product.create') }}" class="bg-pink-500 text-sm text-white px-4 py-2 rounded hover:bg-pink-600">Tambah Produk</a>
            <div class="flex gap-2 w-full">
                <select x-model="selectedCategory" class="border border-gray-300 rounded px-2 py-1">
                    <option value="">Semua Kategori</option>
                    <template x-for="cat in categories" :key="cat.id">
                        <option :value="cat.id" x-text="cat.category"></option>
                    </template>
                </select>
                <input x-model="search" type="text" placeholder="Cari produk..." class="border border-gray-300 rounded px-2 py-1 w-full" />
            </div>
        </div>
        <div class="w-full overflow-x-auto">
            <table class="min-w-full border border-gray-300 rounded-lg">
                <thead>
                    <tr class="bg-pink-100">
                        <th class="px-4 py-2 border-b border-gray-300 text-left">Kode</th>
                        <th class="px-4 py-2 border-b border-gray-300 text-left">Kategori</th>
                        <th class="px-4 py-2 border-b border-gray-300 text-left">Produk</th>
                        <th class="px-4 py-2 border-b border-gray-300 text-left">Merek</th>
                        <th class="px-4 py-2 border-b border-gray-300 text-left">Qty</th>
                        <th class="px-4 py-2 border-b border-gray-300 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="filteredProducts.length === 0">
                        <tr>
                            <td colspan="6" class="px-4 py-2 border-b border-gray-300 text-center">Belum ada produk yang ditambahkan</td>
                        </tr>
                    </template>
                    <template x-for="(product, index) in filteredProducts" :key="product.id">
                        <tr :class="index % 2 === 0 ? 'bg-white' : 'bg-gray-50'">
                            <td class="px-4 py-2 border-b border-gray-300" x-text="product.code"></td>
                            <td class="px-4 py-2 border-b border-gray-300" x-text="categories.find(c => c.id === product.category_id)?.category"></td>
                            <td class="px-4 py-2 border-b border-gray-300" x-text="product.name"></td>
                            <td class="px-4 py-2 border-b border-gray-300" x-text="product.brand"></td>
                            <td class="px-4 py-2 border-b border-gray-300" x-text="product.quantity"></td>
                            <td class="px-4 py-2 border-b border-gray-300">
                                <button @click="openDetail(product)" class="text-green-500 hover:underline mr-2">Detail</button>
                                <a :href="'/products/' + product.id + '/edit'" class="text-blue-500 hover:underline">Edit</a>
                                |
                                <form :action="'/products/' + product.id" @submit="loading = true" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline" onclick="return confirm('Yakin mau hapus produk ini?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Modal Detail Produk -->
        <div
            x-show="showDetail"
            x-transition
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50"
            style="display: none;">
            <div class="bg-white rounded-lg shadow-lg w-96 p-6 relative" @click.away="closeDetail()">
                <h2 class="text-lg font-bold mb-4">Detail Produk</h2>
                <template x-if="detailProduct">
                    <div>
                        <p><span class="font-semibold">Harga Beli:</span> Rp <span x-text="detailProduct.price_buy.toLocaleString('id-ID')"></span></p>
                        <p><span class="font-semibold">Harga Jual:</span> Rp <span x-text="detailProduct.price_sell.toLocaleString('id-ID')"></span></p>
                        <p><span class="font-semibold">Diskon:</span> <span x-text="detailProduct.discount"></span>%</p>
                    </div>
                </template>
                <button
                    @click="closeDetail()"
                    class="mt-4 px-4 py-2 bg-pink-500 text-white rounded hover:bg-pink-600">
                    Tutup
                </button>
            </div>
        </div>
    </section>
</main>
@endsection
