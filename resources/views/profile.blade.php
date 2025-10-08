@extends('layout')

@section('title', $title)

@section('content')
<main class="w-full grid grid-cols-2 gap-x-6 p-6 border border-pink-400 bg-white rounded-xl shadow-md">
    <section class="w-full border-r border-pink-400">
        <div class="flex flex-col items-center">
            @if(Auth::user()->photo == null)
            <div class="w-36 h-36 rounded-full bg-pink-200 flex items-center justify-center text-xl font-bold text-pink-600 mb-2">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            @else
            <img src="/file/{{ $user->photo }}" alt="User Photo" class="w-36 h-36 rounded-full object-cover mb-2">
            @endif
            <div class="flex items-center gap-x-1 mb-2">
                <h2 class="text-xl font-semibold">{{ $user->name }}</h2>
                <p class="text-sm text-white bg-pink-400 px-2 py-0.5 rounded-full">{{ ucfirst($user->level) }}</p>
            </div>
            <p class="text-gray-600 mb-1"><strong>Email:</strong> {{ $user->email }}</p>
            <p class="text-gray-600"><strong>Member Since:</strong> {{ $user->created_at->format('d M Y H:i:s') }}</p>
            <p class="text-gray-600"><strong>Last Update:</strong> {{ $user->updated_at->format('d M Y H:i:s') }}</p>
        </div>
        <!-- edit button -->
        <div class="mt-4 text-center">
            <a href="{{ route('admin.edit', ['id' => $user->id, 'redirect_to' => 'profile.index']) }}" class="px-4 py-2 bg-pink-600 text-white rounded-lg hover:bg-pink-700 transition">Edit Profil</a>
        </div>
    </section>
    <section class="w-full flex flex-col gap-y-4">
        <form action="{{ route('profile.two_factor_secret') }}" method="POST" @submit="loading = true" onsubmit="return confirm('Apakah Anda yakin ingin mengubah kode rahasia dua faktor? Pastikan Anda telah mencatat kode baru tersebut.');">
            @csrf
            @method('PUT')
            <!-- two factor secret code -->
            <label for="two_factor_secret" class="block text-gray-700 font-semibold mb-2">Two Factor Secret Code</label>
            <div class="inline-flex w-full rounded-lg overflow-hidden border border-gray-300">
                <input type="number" id="two_factor_secret" name="two_factor_secret" value="{{ $user->two_factor_secret }}" class="w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-400" placeholder="Minimal 6 karakter, hanya huruf dan angka">
                <button class="px-4 py-2 bg-gray-400 text-white hover:bg-gray-500 transition">Ubah</button>
            </div>
            @error('two_factor_secret')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @else
            <p class="text-sm text-gray-500 mt-1">Catatan: Simpan kode rahasia ini di tempat yang aman. Kode ini digunakan untuk mengatur ulang autentikasi dua faktor Anda jika diperlukan.</p>
            @enderror
        </form>
        <form action="{{ route('profile.two_factor_recovery') }}" method="POST" @submit="loading = true" onsubmit="return confirm('Apakah Anda yakin ingin mengubah kode rahasia dua faktor? Pastikan Anda telah mencatat kode baru tersebut.');">
            @csrf
            @method('PUT')
            <!-- two factor recovery code -->
            <label for="two_factor_recovery" class="block text-gray-700 font-semibold mb-2">Two Factor Recovery Code</label>
            <div class="inline-flex w-full rounded-lg overflow-hidden border border-gray-300">
                <input type="number" id="two_factor_recovery" name="two_factor_recovery" value="{{ $user->two_factor_recovery_codes }}" class="w-full px-3 py-2 focus:outline-none focus:ring-2 focus:ring-pink-400" placeholder="Minimal 6 karakter, hanya huruf dan angka">
                <button class="px-4 py-2 bg-gray-400 text-white hover:bg-gray-500 transition">Ubah</button>
            </div>
            @error('two_factor_recovery')
            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @else
            <p class="text-sm text-gray-500 mt-1">Catatan: Simpan kode pemulihan ini di tempat yang aman. Kode ini digunakan untuk mereset ulang password anda</p>
            @enderror
        </form>
    </section>
</main>
@endsection
