@extends('layout')

@section('title', $title)

@section('content')
<main>
    <section class="flex flex-col gap-6 items-start justify-start p-6 rounded-xl shadow-lg bg-white border border-pink-200 w-full max-w-6xl mx-auto mb-4">
        <h2 class="text-lg font-semibold tracking-tighter">Pilih Laporan untuk ditampilkan</h2>
        <form action="{{ route('report.proses') }}" @submit="loading = true" method="post" class="inline-flex items-start justify-between w-full gap-x-2">
            @csrf
            <div class="flex-1 inline-flex items-center">
                <label for="report_type" class="block whitespace-nowrap text-gray-700 font-bold me-2">Jenis Laporan: {{$report_type_selected}}</label>
                <select id="report_type" name="report_type" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                    <option value="" {{ $report_type_selected == null ? '' : 'selected' }}>Pilih Jenis Laporan</option>
                    @foreach ($report_types as $key => $report_type)
                    <option value="{{ $key }}" {{ $report_type_selected == $key ? 'selected' : '' }}>
                        {{ $report_type['header'] }}
                    </option>
                    @endforeach
                </select>
                @error('report_type')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div class="inline-flex items-center">
                <label for="month" class="block text-gray-700 font-bold me-2">Bulan:</label>
                <select id="month" name="month" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                    <option value="" {{ $month_selected == null ? '' : 'selected' }}>Pilih Bulan</option>
                    @foreach ($months as $index => $month)
                    @php
                    $month_num = $index + 1;
                    @endphp
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
                    <option value="" {{ $year_selected == null ? '' : 'selected' }}>Pilih Tahun</option>
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
            <button type="submit" class="bg-pink-500 text-white px-4 py-2 rounded hover:bg-pink-600">Tampilkan Laporan</button>
        </form>
    </section>
    @if($isReportShowed)

    <section x-data="{
        search: '',
        sortBy: 'id',
        sortAsc: true,
        tableHeader: {{ Illuminate\Support\Js::from($table['header']) }},
        tableHeaderKeys: {{ Illuminate\Support\Js::from(array_column($table['header'], 'key')) }},
        data: {{ Illuminate\Support\Js::from($table['rows']) }},
        page: 1,
        perPage: 10,
        get filtered() {
            let rows = this.data;

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
    }" class="w-full" x-init="console.log(tableHeaderKeys)">
        <section class="flex flex-col gap-6 items-start justify-start p-6 rounded-xl shadow-lg bg-white border border-pink-200 w-full max-w-6xl mx-auto mt-6">
            <h2 class="text-xl font-bold text-center w-full">
                Menampilkan Laporan {{ $report_types[$report_type_selected]['header'] }} periode {{ $months[$month_selected -1] }} {{ $year_selected }}
            </h2>

            <div class="flex flex-col md:flex-row w-full justify-between items-center gap-4">
                <input x-model="search" type="text" placeholder="Cari data..." class="border border-pink-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-pink-400 w-full md:w-1/3 transition-all" />
                <a href="{{ route('report.cetak', ['report_key' => $report_type_selected, 'month' => $month_selected, 'year' => $year_selected]) }}" class="bg-pink-500 text-white px-4 py-2 rounded hover:bg-pink-600" target="_blank">
                    Cetak Laporan
                </a>
            </div>

            <div class="overflow-x-auto w-full">
                <table class="w-full table-auto border-collapse rounded-lg overflow-hidden shadow border border-pink-300">
                    <thead>
                        <tr class="bg-pink-100 text-pink-800">
                            <template x-for="(col, idx) in tableHeader" :key="idx">
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
                            <template x-for="(row, idx) in paged" :key="'rows-' + idx">
                                <tr class="hover:bg-pink-50 transition-colors">
                                    <template x-for="(key, idxc) in tableHeaderKeys" :key="'rows-' + idx + '-' + idxc">
                                        <td class="border border-pink-300 px-4 py-2" :class="key === 'aksi' ? 'text-center' : ''">
                                            <template x-if="key === 'aksi'">
                                                <a :href="row[key]" class="bg-pink-100 text-pink-700 px-3 py-1 rounded-l hover:bg-pink-200 transition-colors text-sm font-medium" target="_blank">
                                                    Detail
                                                </a>
                                            </template>
                                            <template x-if="key !== 'aksi'">
                                                <span x-text="row[key]"></span>
                                            </template>
                                        </td>
                                    </template>
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
    </section>
    @endif
</main>
@endsection
