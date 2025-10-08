@extends('layout')

@section('title', $title)

@section('content')
<main>
    <!-- edit form contains with this all fillable 'name', 'code', 'brand', 'quantity', 'price_buy', 'price_sell', 'discount' -->
    <section class="flex flex-col items-start justify-start p-4 rounded-lg shadow-md bg-white border border-pink-200">
        <!-- back Button to products -->
        <a href="{{ route('product.index') }}" class="bg-pink-500 text-white px-4 py-2 mb-4 rounded hover:bg-pink-600">Kembali</a>

        <form action="{{ route('product.update', $product->id) }}" @submit="loading = true" method="POST" class="w-full">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label for="category_id" class="block text-gray-700 font-bold mb-2">Kategori:</label>
                <select id="category_id" name="category_id" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                    <option value="" disabled>Pilih Kategori</option>
                    @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>{{ $category->category }}</option>
                    @endforeach
                </select>
                @error('category_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-bold mb-2">Nama Produk:</label>
                <input type="text" id="name" name="name" value="{{ $product->name }}" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="brand" class="block text-gray-700 font-bold mb-2">Merek:</label>
                <div x-data="{ selectedBrand: '{{  $product->brand }}' }">
                    <select
                        id="brand_select"
                        name="brand_select"
                        class="w-full px-3 py-2 border border-gray-300 rounded mb-2"
                        x-model="selectedBrand"
                        @change="$refs.brandInput.value = selectedBrand">
                        <option value="" disabled {{  $product->brand ? '' : 'selected' }}>Pilih Merek</option>
                        @foreach ($brands as $brand)
                        <option value="{{ $brand }}" {{  $product->brand == $brand ? 'selected' : '' }}>{{ $brand }}</option>
                        @endforeach
                    </select>
                    <label for="brand" class="block text-gray-700 font-bold mb-2">Ganti Merek Baru:</label>
                    <input
                        type="text"
                        id="brand"
                        name="brand"
                        x-ref="brandInput"
                        x-model="selectedBrand"
                        value="{{  $product->brand }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded"
                        required
                        placeholder="Atau isi merek baru">
                </div>
                @error('brand')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="price_buy" class="block text-gray-700 font-bold mb-2">Harga Beli:</label>
                <input type="number" id="price_buy" name="price_buy" value="{{ $product->price_buy }}" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                @error('price_buy')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="price_sell" class="block text-gray-700 font-bold mb-2">Harga Jual:</label>
                <input type="number" id="price_sell" name="price_sell" value="{{ $product->price_sell }}" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                @error('price_sell')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="discount" class="block text-gray-700 font-bold mb-2">Diskon (%):</label>
                <input type="number" id="discount" name="discount" value="{{ $product->discount }}" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                @error('discount')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center justify-between">
                <button type="submit" class="bg-pink-500 text-white px-4 py-2 rounded hover:bg-pink-600">Simpan</button>
                <a href="{{ route('product.index') }}" class="text-gray-500 hover:underline">Batal</a>
            </div>
        </form>

    </section>
</main>
@endsection
