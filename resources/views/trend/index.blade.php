@extends('layout')

@section('title', $title)

@section('content')
<section class="flex flex-col gap-6 items-start justify-start p-6 rounded-xl shadow-lg bg-white border border-pink-200 w-fit max-w-6xl mx-auto mb-4">
    <form action="{{ route('trend.proses') }}" @submit="loading = true" method="post" class="inline-flex items-start justify-between w-full gap-x-2">
        @csrf
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

<main
    class="p-6 rounded-xl shadow-lg bg-white border border-pink-200 w-full max-w-6xl mx-auto mb-4"
    x-data="{
    trends: {{ Illuminate\Support\Js::from($trends) }},
    currentPage: 1,
    perPage: 5,
    get totalPages() {
      return Math.ceil(this.trends.length / this.perPage);
    },
    get paginatedData() {
      const start = (this.currentPage - 1) * this.perPage;
      return this.trends.slice(start, start + this.perPage);
    }
  }">
    <div class="flex justify-between items-center w-full mb-4">
        <h2 class="text-xl font-semibold text-gray-800">Trend Produk : {{ $periode }}</h2>
        <a href="{{ route('trend.cetak', $cta_cetak) }}" class="bg-pink-500 text-white px-4 py-2 rounded hover:bg-pink-600" target="_blank">
            Cetak Laporan
        </a>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
        <table class="w-full border-collapse text-sm text-gray-700">
            <thead class="bg-pink-100 text-gray-800">
                <tr>
                    <th class="py-3 px-4 text-left">Product Name</th>
                    <th class="py-3 px-4 text-left">Brand</th>
                    <th class="py-3 px-4 text-right">Total Quantity</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="item in paginatedData" :key="item.product_id">
                    <tr class="border-b hover:bg-pink-50 transition">
                        <td class="py-2 px-4" x-text="item.product.name"></td>
                        <td class="py-2 px-4" x-text="item.product.brand"></td>
                        <td class="py-2 px-4 text-right font-medium text-pink-600" x-text="item.total_quantity"></td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="flex justify-between items-center mt-4 text-sm">
        <button
            class="px-3 py-1 rounded bg-pink-500 text-white disabled:opacity-50"
            :disabled="currentPage === 1"
            @click="currentPage--">
            Kembali
        </button>

        <span class="text-gray-600">
            Halaman <span x-text="currentPage"></span> dari <span x-text="totalPages"></span>
        </span>

        <button
            class="px-3 py-1 rounded bg-pink-500 text-white disabled:opacity-50"
            :disabled="currentPage === totalPages"
            @click="currentPage++">
            Lanjut
        </button>
    </div>
</main>
@endsection
