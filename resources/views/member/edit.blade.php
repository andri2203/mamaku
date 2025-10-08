@extends('layout')

@section('title', $title)

@section('content')
<main>
    <section class="flex flex-col items-start justify-start p-4 rounded-lg shadow-md bg-white border border-pink-200">
        <!-- back Button to member -->
        <a href="{{ route('member.index') }}" class="bg-pink-500 text-white px-4 py-2 mb-4 rounded hover:bg-pink-600">Kembali</a>

        <form action="{{ route('member.update', ['id'=>$id]) }}" @submit="loading = true" method="POST" class="w-full">
            @csrf
            @method('PUT')
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-bold mb-2">Nama Member:</label>
                <input type="text" id="name" name="name" value="{{ $member->name }}" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                @error('name')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="phone" class="block text-gray-700 font-bold mb-2">Kontak:</label>
                <input type="text" id="phone" name="phone" value="{{ $member->phone }}" class="w-full px-3 py-2 border border-gray-300 rounded" required>
                @error('phone')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label for="address" class="block text-gray-700 font-bold mb-2">Alamat:</label>
                <textarea id="address"
                    name="address"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded"
                    required>{{ $member->address }}</textarea>
                @error('address')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="bg-pink-500 text-white px-4 py-2 rounded hover:bg-pink-600">Simpan</button>
        </form>
    </section>
</main>

@endsection
