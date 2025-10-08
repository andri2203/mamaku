@extends('layout')
@section('title', $title)

@section('content')
<main>
    <section class="flex flex-col items-start justify-start p-4 rounded-lg shadow-md bg-white border border-pink-200">
        <!-- back Button to supplier -->
        <a href="{{ route('supplier.index') }}" class="bg-pink-500 text-white px-4 py-2 mb-4 rounded hover:bg-pink-600">Kembali</a>

        <form action="{{ route('supplier.store') }}" @submit="loading = true" method="POST" class="w-full">
            @csrf
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-bold mb-2">Nama Supplier:</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="contact" class="block text-gray-700 font-bold mb-2">Kontak:</label>
                <input type="text" id="contact" name="contact" value="{{ old('contact') }}" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                @error('contact')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="address" class="block text-gray-700 font-bold mb-2">Alamat:</label>
                <input type="text" id="address" name="address" value="{{ old('address') }}" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                @error('address')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="bg-pink-500 text-white px-4 py-2 rounded hover:bg-pink-600">Simpan</button>
        </form>
    </section>
</main>
@endsection
