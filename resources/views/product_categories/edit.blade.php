@extends('layout')

@section('title', $title)

@section('content')
<main>
    <section class="flex flex-col items-start justify-start p-4 rounded-lg shadow-md bg-white border border-pink-200">
        <a href="{{ route('product-categories.index') }}" class="bg-pink-500 text-white px-4 py-2 mb-4 rounded hover:bg-pink-600">Kembali ke Daftar Kategori</a>
        <div class="w-full">
            <form action="{{ route('product-categories.update', $productCategory->id) }}" @submit="loading = true" method="POST" class="w-full">
                @csrf
                @method('PUT')
                <div class="mb-4">
                    <label for="category" class="block text-gray-700 font-bold mb-2">Nama Kategori:</label>
                    <input type="text" id="category" name="category" value="{{ old('category', $productCategory->category) }}" class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:ring focus:border-blue-300" required>
                    @error('category')
                    <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="code" class="block text-gray-700 font-bold mb-2">Kode Produk:</label>
                    <input type="text" id="code" name="code" value="{{ old('code', $productCategory->code) }}" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                    @error('code')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" class="bg-pink-500 text-white px-4 py-2 rounded hover:bg-pink-600">Update Kategori</button>
            </form>
        </div>
    </section>
</main>
@endsection
