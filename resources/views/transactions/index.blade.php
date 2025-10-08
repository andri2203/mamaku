@extends("layout")

@section("title", $title)

@section("content")
<main x-data="{
    search: '',
    sortBy: 'id',
    sortAsc: true,
    transaction: {{ Illuminate\Support\Js::from($transactions->map(function($transaction) {
            return [
                'id' => $transaction->id,
                'created_at' => $transaction->created_at->format('d/m/Y H:i'),
                'admin' => $transaction->user->name,
                'member' => $transaction->member? $transaction->member->name : 'Pelanggan',
                'total_item' => $transaction->total_item,
                'total_price' => number_format($transaction->total_price, 0, ',', '.'),
                'discount' => number_format($transaction->discount, 0, ',', '.'),
                'is_paid' => $transaction->is_paid,
                'details' => [
                    'product' => $transaction->details->map(function($detail) {
                        return [
                            'id' => $detail->product->id,
                            'name' => $detail->product->name,
                            'qty' => $detail->quantity,
                            'price' => number_format($detail->price, 0, ',', '.'),
                        ];
                    })->toArray(),
                    'total_price' => number_format($transaction->details->sum(fn($d) => $d->quantity * $d->price), 0, ',', '.'),
                    ]
            ];
        })) }},
    page: 1,
    perPage: 10,
    get filtered() {
        let rows = this.transaction;
        if (this.search) {
            rows = rows.filter(row =>
                Object.values(row).some(val =>
                    String(val).toLowerCase().includes(this.search.toLowerCase())
                )
            );
        }
        rows = rows.sort((a, b) => {
            if (a[this.sortBy] < b[this.sortBy]) return this.sortAsc ? -1 : 1;
            if (a[this.sortBy] > b[this.sortBy]) return this.sortAsc ? 1 : -1;
            return 0;
        });
        return rows;
    },
    get paged() {
        const start = (this.page - 1) * this.perPage;
        return this.filtered.slice(start, start + this.perPage);
    },
    get totalPages() {
        return Math.ceil(this.filtered.length / this.perPage) || 1;
    }
}">
    <section class="flex flex-col gap-6 items-start justify-start p-6 rounded-xl shadow-lg bg-white border border-pink-200 w-full max-w-6xl mx-auto mt-6">
        <div class="flex flex-col md:flex-row w-full justify-between items-center gap-4">
            <a href="{{ route('transaction.create') }}" class="bg-pink-500 text-white px-6 py-3 rounded-lg shadow hover:bg-pink-600 transition-colors font-semibold">
                <svg class="inline mr-2 w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah
            </a>
            <input x-model="search" type="text" placeholder="Cari data..." class="border border-pink-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-pink-400 w-full md:w-1/3 transition-all" />
        </div>

        <div class="overflow-x-auto w-full">
            <table class="w-full table-auto border-collapse rounded-lg overflow-hidden shadow border border-pink-300">
                <thead>
                    <tr class="bg-pink-100 text-pink-800">
                        <template x-for="(col, idx) in [
                            {key:'invoice', label:'Invoice'},
                            {key:'created_at', label:'Tanggal'},
                            {key:'member', label:'Member'},
                            {key:'total_item', label:'Total Item'},
                            {key:'discount', label:'Diskon'},
                            {key:'total_price', label:'Total Harga'},
                            {key:'is_paid', label:'Status Pembayaran'},
                            {key:'admin', label:'Admin'},
                            {key:'aksi', label:'Aksi'},
                        ]">
                            <th
                                :key="col.key"
                                class="border border-pink-300 px-4 py-3 text-left cursor-pointer select-none font-semibold"
                                @click="col.key !== 'aksi' && (sortBy = col.key, sortAsc = sortBy === col.key ? !sortAsc : true)"
                                :class="col.key !== 'aksi' ? 'hover:bg-pink-200 transition-colors' : ''">
                                <span x-text="col.label"></span>
                                <template x-if="sortBy === col.key && col.key !== 'aksi'">
                                    <span>
                                        <svg x-show="sortAsc" class="inline w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 15l7-7 7 7" />
                                        </svg>
                                        <svg x-show="!sortAsc" class="inline w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </span>
                                </template>
                            </th>
                        </template>
                    </tr>
                </thead>
                <tbody>
                    <template x-if="paged.length">
                        <template x-for="row in paged" :key="row.id">
                            <tr class="hover:bg-pink-50 transition-colors">
                                <td class="border border-pink-300 px-4 py-2 text-center" x-text="'TR-' + row.id.toString().padStart(4, '0')"></td>
                                <td class="border border-pink-300 px-4 py-2 text-center" x-text="row.created_at"></td>
                                <td class="border border-pink-300 px-4 py-2" x-text="row.member"></td>
                                <td class="border border-pink-300 px-4 py-2 text-center" x-text="row.total_item"></td>
                                <td class="border border-pink-300 px-4 py-2 text-right">Rp <span x-text="row.discount.toLocaleString()"></span></td>
                                <td class="border border-pink-300 px-4 py-2 text-right">Rp <span x-text="row.total_price.toLocaleString()"></span></td>
                                <td class="border border-pink-300 px-4 py-2 text-center">
                                    <span x-show="row.is_paid" class="text-green-600 font-semibold">Dibayar</span>
                                    <span x-show="!row.is_paid" class="text-red-600 font-semibold">Belum Dibayar</span>
                                </td>
                                <td class="border border-pink-300 px-4 py-2 text-right"><span x-text="row.admin"></span></td>
                                <td class="border border-pink-300 px-4 py-2 text-center">
                                    <div class="inline-flex">
                                        <a :href="'/transaction/' + row.id + '/detail'" class="bg-pink-100 text-pink-700 px-3 py-1 rounded-l hover:bg-pink-200 transition-colors text-sm font-medium" target="_blank">Detail</a>
                                        <a :href="'/transaction/' + row.id + '/edit'" class="bg-pink-500 text-white px-3 py-1 hover:bg-pink-600 transition-colors text-sm font-medium">Edit</a>
                                        <form :action="'/transaction/' + row.id" method="POST" class="inline" @submit="loading = true">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" onclick="return confirm('Yakin ingin menghapus data ini?')" class="bg-red-500 text-white px-3 py-1 rounded-r hover:bg-red-600 transition-colors text-sm font-medium cursor-pointer">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </template>
                    <template x-if="!paged.length">
                        <tr>
                            <td colspan="8" class="border border-pink-300 px-4 py-6 text-center text-pink-500 font-semibold">Tidak ada data Transaksi.</td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <div class="flex flex-col md:flex-row justify-between items-center w-full mt-4 gap-4">
            <div class="text-sm text-gray-500">
                Menampilkan <span x-text="paged.length"></span> dari <span x-text="filtered.length"></span> data
            </div>
            <div class="flex items-center gap-2">
                <button @click="page = Math.max(1, page - 1)" :disabled="page === 1"
                    class="px-3 py-1 rounded bg-pink-100 text-pink-700 hover:bg-pink-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    &laquo;
                </button>
                <template x-for="i in totalPages" :key="i">
                    <button @click="page = i"
                        :class="page === i ? 'bg-pink-500 text-white' : 'bg-pink-100 text-pink-700 hover:bg-pink-200'"
                        class="px-3 py-1 rounded transition-colors font-semibold">
                        <span x-text="i"></span>
                    </button>
                </template>
                <button @click="page = Math.min(totalPages, page + 1)" :disabled="page === totalPages"
                    class="px-3 py-1 rounded bg-pink-100 text-pink-700 hover:bg-pink-200 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                    &raquo;
                </button>
            </div>
            <div>
                <label class="text-sm text-gray-500 mr-2">Baris per halaman:</label>
                <select x-model="perPage" class="border border-pink-300 rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-pink-400">
                    <option value="5">5</option>
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </section>
</main>
@endsection
