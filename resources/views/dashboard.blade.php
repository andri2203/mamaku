@extends('layout')

@section('title', $title)

@section('content')
<main
    id="dashboard-page" class="w-full grid grid-cols-2 lg:grid-cols-4 gap-4">
    <!-- announcement -->
    <section class="col-span-4 inline-flex justify-center py-2 text-purple-800 bg-purple-100 border border-purple-600 rounded-xl">
        Semua data yang ditampilkan merupakan periode {{ $title_periode }}
    </section>
    <!-- Card: Pemasukkan -->
    <section
        class="col-span-1 flex flex-col items-start justify-between rounded-2xl shadow-md bg-gradient-to-br from-pink-100 via-pink-50 to-purple-100 border border-pink-200 p-6 hover:shadow-lg transition">

        <h1 class="inline-flex items-center text-pink-700 font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                viewBox="0 0 24 24"
                class="size-7 text-pink-500 me-3">
                <path d="M4.5 3.75a3 3 0 0 0-3 3v.75h21v-.75a3 3 0 0 0-3-3h-15Z" />
                <path fill-rule="evenodd"
                    d="M22.5 9.75h-21v7.5a3 3 0 0 0 3 3h15a3 3 0 0 0 3-3v-7.5Zm-18 3.75a.75.75 0 0 1 .75-.75h6a.75.75 0 0 1 0 1.5h-6a.75.75 0 0 1-.75-.75Zm.75 2.25a.75.75 0 0 0 0 1.5h3a.75.75 0 0 0 0-1.5h-3Z"
                    clip-rule="evenodd" />
            </svg>
            <span class="text-lg">Pemasukkan</span>
        </h1>

        <p class="text-2xl font-bold text-pink-700 mt-4" x-data="{
            value: 0,
            target: {{ $total_income }}, // angka tujuan
            duration: 1000,
            start() {
                let startTime = performance.now();
                let animate = (time) => {
                    let progress = Math.min((time - startTime) / this.duration, 1);
                    this.value = Math.floor(progress * this.target);
                    if (progress < 1) requestAnimationFrame(animate);
                };
                requestAnimationFrame(animate);
            }
        }"
            x-init="start()">Rp <span x-text="value.toLocaleString()"></span></p>
        <a href="*" class="mt-4 inline-block bg-pink-500 text-white px-4 py-2 rounded-lg hover:bg-pink-600 transition">
            Lihat Detail
        </a>
    </section>

    <!-- Card: Produk Masuk -->
    <section
        class="col-span-1 flex flex-col items-start justify-between rounded-2xl shadow-md bg-gradient-to-br from-pink-100 via-pink-50 to-purple-100 border border-pink-200 p-6 hover:shadow-lg transition">

        <h1 class="inline-flex items-center text-pink-700 font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                viewBox="0 0 24 24"
                class="size-7 text-purple-500 me-3">
                <path fill-rule="evenodd"
                    d="M12 2.25a.75.75 0 0 1 .75.75v11.69l3.22-3.22a.75.75 0 1 1 1.06 1.06l-4.5 4.5a.75.75 0 0 1-1.06 0l-4.5-4.5a.75.75 0 1 1 1.06-1.06l3.22 3.22V3a.75.75 0 0 1 .75-.75Zm-9 13.5a.75.75 0 0 1 .75.75v2.25a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5V16.5a.75.75 0 0 1 1.5 0v2.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V16.5a.75.75 0 0 1 .75-.75Z"
                    clip-rule="evenodd" />
            </svg>
            <span class="text-lg">Produk Masuk</span>
        </h1>

        <p class="text-2xl font-bold text-pink-700 mt-4" x-data="{
            value: 0,
            target: {{ $itemIn }}, // angka tujuan
            duration: 1000,
            start() {
                let startTime = performance.now();
                let animate = (time) => {
                    let progress = Math.min((time - startTime) / this.duration, 1);
                    this.value = Math.floor(progress * this.target);
                    if (progress < 1) requestAnimationFrame(animate);
                };
                requestAnimationFrame(animate);
            }
        }"
            x-init="start()"><span x-text="value.toLocaleString()"></span> Item</p>
        <a href="*" class="mt-4 inline-block bg-pink-500 text-white px-4 py-2 rounded-lg hover:bg-pink-600 transition">
            Lihat Detail
        </a>
    </section>

    <!-- Card: Produk Keluar -->
    <section
        class="col-span-1 flex flex-col items-start justify-between rounded-2xl shadow-md bg-gradient-to-br from-pink-100 via-pink-50 to-purple-100 border border-pink-200 p-6 hover:shadow-lg transition">

        <h1 class="inline-flex items-center text-pink-700 font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                viewBox="0 0 24 24"
                class="size-7 text-pink-500 me-3">
                <path fill-rule="evenodd"
                    d="M11.47 2.47a.75.75 0 0 1 1.06 0l4.5 4.5a.75.75 0 0 1-1.06 1.06l-3.22-3.22V16.5a.75.75 0 0 1-1.5 0V4.81L8.03 8.03a.75.75 0 0 1-1.06-1.06l4.5-4.5ZM3 15.75a.75.75 0 0 1 .75.75v2.25a1.5 1.5 0 0 0 1.5 1.5h13.5a1.5 1.5 0 0 0 1.5-1.5V16.5a.75.75 0 0 1 1.5 0v2.25a3 3 0 0 1-3 3H5.25a3 3 0 0 1-3-3V16.5a.75.75 0 0 1 .75-.75Z"
                    clip-rule="evenodd" />
            </svg>
            <span class="text-lg">Produk Keluar</span>
        </h1>

        <p class="text-2xl font-bold text-pink-700 mt-4" x-data="{
            value: 0,
            target: {{ $itemOut }}, // angka tujuan
            duration: 1000,
            start() {
                let startTime = performance.now();
                let animate = (time) => {
                    let progress = Math.min((time - startTime) / this.duration, 1);
                    this.value = Math.floor(progress * this.target);
                    if (progress < 1) requestAnimationFrame(animate);
                };
                requestAnimationFrame(animate);
            }
        }"
            x-init="start()"><span x-text="value.toLocaleString()"></span> Item</p>
        <a href="*" class="mt-4 inline-block bg-pink-500 text-white px-4 py-2 rounded-lg hover:bg-pink-600 transition">
            Lihat Detail
        </a>
    </section>

    <!-- Card: Produk -->
    <section
        class="col-span-1 flex flex-col items-start justify-between rounded-2xl shadow-md bg-gradient-to-br from-pink-100 via-pink-50 to-purple-100 border border-pink-200 p-6 hover:shadow-lg transition">

        <h1 class="inline-flex items-center text-pink-700 font-semibold">
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                viewBox="0 0 24 24"
                class="size-7 text-purple-500 me-3">
                <path
                    d="M5.223 2.25c-.497 0-.974.198-1.325.55l-1.3 1.298A3.75 3.75 0 0 0 7.5 9.75c.627.47 1.406.75 2.25.75.844 0 1.624-.28 2.25-.75.626.47 1.406.75 2.25.75.844 0 1.623-.28 2.25-.75a3.75 3.75 0 0 0 4.902-5.652l-1.3-1.299a1.875 1.875 0 0 0-1.325-.549H5.223Z" />
                <path fill-rule="evenodd"
                    d="M3 20.25v-8.755c1.42.674 3.08.673 4.5 0A5.234 5.234 0 0 0 9.75 12c.804 0 1.568-.182 2.25-.506a5.234 5.234 0 0 0 2.25.506c.804 0 1.567-.182 2.25-.506 1.42.674 3.08.675 4.5.001v8.755h.75a.75.75 0 0 1 0 1.5H2.25a.75.75 0 0 1 0-1.5H3Zm3-6a.75.75 0 0 1 .75-.75h3a.75.75 0 0 1 .75.75v3a.75.75 0 0 1-.75.75h-3a.75.75 0 0 1-.75-.75v-3Zm8.25-.75a.75.75 0 0 0-.75.75v5.25c0 .414.336.75.75.75h3a.75.75 0 0 0 .75-.75v-5.25a.75.75 0 0 0-.75-.75h-3Z"
                    clip-rule="evenodd" />
            </svg>
            <span class="text-lg">Produk Terjual</span>
        </h1>

        <p class="text-2xl font-bold text-pink-700 mt-4" x-data="{
            value: 0,
            target: {{ $itemSell }}, // angka tujuan
            duration: 1000,
            start() {
                let startTime = performance.now();
                let animate = (time) => {
                    let progress = Math.min((time - startTime) / this.duration, 1);
                    this.value = Math.floor(progress * this.target);
                    if (progress < 1) requestAnimationFrame(animate);
                };
                requestAnimationFrame(animate);
            }
        }"
            x-init="start()"><span x-text="value.toLocaleString()"></span> Item</p>
        <a href="*" class="mt-4 inline-block bg-pink-500 text-white px-4 py-2 rounded-lg hover:bg-pink-600 transition">
            Lihat Detail
        </a>
    </section>

    <!-- Card: Trend Produk -->
    <section x-data="pieChart({{ Illuminate\Support\Js::from($trend_product) }})" x-init="renderChart()"
        class="col-span-2 row-span-2 flex flex-col items-start justify-between rounded-2xl shadow-md bg-gradient-to-br from-pink-100 via-pink-50 to-purple-100 border border-pink-200 p-6 hover:shadow-lg transition">
        <h1 class="inline-flex items-center text-pink-700 font-semibold mb-2">
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                viewBox="0 0 24 24"
                class="size-7 text-purple-500 me-3">
                <path fill-rule="evenodd" d="M15.22 6.268a.75.75 0 0 1 .968-.431l5.942 2.28a.75.75 0 0 1 .431.97l-2.28 5.94a.75.75 0 1 1-1.4-.537l1.63-4.251-1.086.484a11.2 11.2 0 0 0-5.45 5.173.75.75 0 0 1-1.199.19L9 12.312l-6.22 6.22a.75.75 0 0 1-1.06-1.061l6.75-6.75a.75.75 0 0 1 1.06 0l3.606 3.606a12.695 12.695 0 0 1 5.68-4.974l1.086-.483-4.251-1.632a.75.75 0 0 1-.432-.97Z" clip-rule="evenodd" />
            </svg>

            <span class="text-lg">Trend Produk (10 Teratas)</span>
        </h1>
        <canvas id="myPieChart"></canvas>
    </section>

    <!-- Card: Produk Stock Kosong -->
    <section
        class="col-span-2 h-fit flex flex-col rounded-2xl shadow-md bg-gradient-to-br from-pink-100 via-pink-50 to-purple-100 border border-pink-200 p-6 hover:shadow-lg transition">

        <!-- Header -->
        <h1 class="inline-flex items-center text-pink-700 font-semibold mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                viewBox="0 0 24 24"
                class="size-7 text-purple-500 me-3">
                <path fill-rule="evenodd"
                    d="M7.502 6h7.128A3.375 3.375 0 0 1 18 9.375v9.375a3 3 0 0 0 3-3V6.108c0-1.505-1.125-2.811-2.664-2.94a48.972 48.972 0 0 0-.673-.05A3 3 0 0 0 15 1.5h-1.5a3 3 0 0 0-2.663 1.618c-.225.015-.45.032-.673.05C8.662 3.295 7.554 4.542 7.502 6ZM13.5 3A1.5 1.5 0 0 0 12 4.5h4.5A1.5 1.5 0 0 0 15 3h-1.5Z"
                    clip-rule="evenodd" />
                <path fill-rule="evenodd"
                    d="M3 9.375C3 8.339 3.84 7.5 4.875 7.5h9.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-9.75A1.875 1.875 0 0 1 3 20.625V9.375ZM6 12a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75H6.75a.75.75 0 0 1-.75-.75V12Zm2.25 0a.75.75 0 0 1 .75-.75h3.75a.75.75 0 0 1 0 1.5H9a.75.75 0 0 1-.75-.75ZM6 15a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75H6.75a.75.75 0 0 1-.75-.75V15Zm2.25 0a.75.75 0 0 1 .75-.75h3.75a.75.75 0 0 1 0 1.5H9a.75.75 0 0 1-.75-.75ZM6 18a.75.75 0 0 1 .75-.75h.008a.75.75 0 0 1 .75.75v.008a.75.75 0 0 1-.75.75H6.75a.75.75 0 0 1-.75-.75V18Zm2.25 0a.75.75 0 0 1 .75-.75h3.75a.75.75 0 0 1 0 1.5H9a.75.75 0 0 1-.75-.75Z"
                    clip-rule="evenodd" />
            </svg>
            <span class="text-lg">Produk Stock Kosong (Jumlah dibawah 5)</span>
        </h1>

        <!-- Content -->
        <div class="flex-1 w-full space-y-3 overflow-y-auto" x-data>
            @forelse ($products as $product)
            <div
                :style="`animation-delay: {{ $loop->index * 0.1 }}s`"
                class="flex justify-between items-center bg-white/70 rounded-lg px-4 py-2 text-sm border border-pink-100
                   opacity-0 animate-fadeInLeft">
                <span class="text-gray-700 font-medium">{{ $product->name }}</span>
                <span class="text-xs px-2 py-1 rounded-full bg-pink-200 text-pink-800 font-semibold">{{ $product->quantity }}</span>
            </div>
            @empty
            <p class="text-gray-500 italic">Semua produk memiliki stok tersedia 🎉</p>
            @endforelse
        </div>

        <!-- Footer -->
        <div class="mt-4 w-full">
            <a href="{{ route('items-in.create') }}"
                class="w-full inline-flex justify-center items-center px-4 py-2 text-sm font-medium rounded-lg bg-pink-500 text-white hover:bg-pink-600 transition">
                Tambah Barang Masuk
            </a>
        </div>
    </section>
</main>

@endsection
