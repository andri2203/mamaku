@extends('layout')

@section('title', $title)

@section('content')
<main>
    <a href="{{ route('admin.index') }}" @click="loading= true" class="inline-flex items-center gap-x-2 mb-4 text-pink-600 hover:text-pink-800 transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
        </svg>
        Kembali
    </a>
    <form action="{{ route('admin.update', $action_param) }}" method="post" enctype="multipart/form-data" @submit="loading = true" class="flex justify-start items-center w-full p-6 bg-white border border-pink-400 rounded">
        @csrf
        @method('PUT')
        <div class="p-4 mr-6 flex flex-col gap-y-4 relative" x-data="{
            photo: null,
            photoUrl: '{{ $user->photo }}',
            updatePhotoUrl(event) {
                this.photo = event.target.files[0];
                this.photoUrl = URL.createObjectURL(this.photo);
            }
        }">
            <label x-show="!photo && !photoUrl" for="photo" class="w-36 h-36 rounded-full bg-pink-200 flex items-center justify-center text-4xl font-bold text-pink-600 mb-4 cursor-pointer hover:bg-pink-300 transition">A</label>
            <div x-show="photo || photoUrl" class="relative w-36 h-36">
                <button type="button" @click="photo = null; photoUrl = null" class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600 transition z-10 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <input type="file" @change="updatePhotoUrl($event)" name="photo" id="photo" class="hidden" accept="image/*">
                    </svg>
                </button>
                <img :src="'/file/' +photoUrl" alt="User Photo" class="w-36 h-36 rounded-full object-cover mb-4">
            </div>
            <input type="file" @change="photo = $event.target.files[0]" name="photo" id="photo" class="hidden" accept="image/*">
            <p class="text-sm text-gray-500">Foto Profil (Opsional)</p>
            @error('photo')
            <div class="text-sm text-red-500" role="alert">
                {{ $message }}
            </div>
            @enderror
        </div>

        <div class="flex flex-col gap-y-4 w-full">
            <h1 class="text-2xl font-bold text-gray-800 mb-4">Info Admin</h1>

            <div class="flex flex-col gap-y-1">
                <label for="name" class="font-semibold text-gray-700">Nama</label>
                <input type="text" name="name" id="name" class="px-4 py-2 border border-pink-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400" value="{{ old('name', $user->name) }}" autocomplete="off" required>
                @error('name')
                <div class="text-sm text-red-500" role="alert">
                    {{ $message }}
                </div>
                @enderror
            </div>


            <div class="inline-flex justify-start items-start gap-x-8">
                <div class="flex-1 flex flex-col gap-y-1">
                    <label for="email" class="font-semibold text-gray-700">Email</label>
                    <input type="email" name="email" id="email" class="px-4 py-2 border border-pink-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400" value="{{ old('email', $user->email) }}" autocomplete="off" required>
                    @error('email')
                    <div class="text-sm text-red-500" role="alert">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <div class="flex flex-col gap-y-1">
                    <label class="font-semibold text-gray-700">Peran</label>
                    <!-- role radio button input with UserRoleEnum -->
                    <div class="flex items-center gap-x-4 py-2">
                        @foreach ($roles as $role)
                        <div class="flex items-center gap-x-2">
                            <input type="radio" name="level" id="level-{{ $role->value }}" value="{{ $role->value }}" class="w-4 h-4 text-pink-400 border-pink-300 focus:ring-pink-400" {{ old('level', $user->level) == $role->value? 'checked':'' }} required>
                            <label for="level-{{ $role->value }}" class="text-gray-700">{{ $role->name }}</label>
                        </div>
                        @endforeach
                    </div>
                    @error('level')
                    <div class="text-sm text-red-500" role="alert">
                        {{ $message }}
                    </div>
                    @enderror
                </div>

                <div class="flex flex-col gap-y-1">
                    <label for="rule" class="font-semibold text-gray-700">Team</label>
                    <select name="team_id" id="team_id" class="px-4 py-2 border border-pink-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400">
                        <option value="" disabled selected>Pilih Team</option>
                        @foreach ($teams as $team)
                        <option value="{{ $team->id }}" {{ old('team_id', $user->current_team_id) == $team->id? 'selected':'' }}>{{ $team->name }}</option>
                        @endforeach
                    </select>
                    @error('team_id')
                    <div class="text-sm text-red-500" role="alert">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

            <div class="inline-flex justify-start items-start gap-x-8">
                <div class="flex-1 flex flex-col gap-y-1">
                    <label for="password_old" class="font-semibold text-gray-700">Kata Sandi Lama</label>
                    <input type="password" name="password_old" id="password_old" class="px-4 py-2 border border-pink-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400" placeholder="Kosongkan jika tidak ingin mengubah kata sandi">
                    @error('password_old')
                    <div class="text-sm text-red-500" role="alert">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <div class="flex-1 flex flex-col gap-y-1">
                    <label for="password" class="font-semibold text-gray-700">Kata Sandi Baru</label>
                    <input type="password" name="password" id="password" class="px-4 py-2 border border-pink-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400" placeholder="Kosongkan jika tidak ingin mengubah kata sandi">
                    @error('password')
                    <div class="text-sm text-red-500" role="alert">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
                <div class="flex-1 flex flex-col gap-y-1">
                    <label for="password_confirmation" class="font-semibold text-gray-700">Konfirmasi Kata Sandi Baru</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="px-4 py-2 border border-pink-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400" placeholder="Kosongkan jika tidak ingin mengubah kata sandi">
                    @error('password_confirmation')
                    <div class="text-sm text-red-500" role="alert">
                        {{ $message }}
                    </div>
                    @enderror
                </div>
            </div>

            <button type="submit" class="px-4 py-2 bg-pink-400 text-white rounded-xl hover:bg-pink-600 transition w-fit mt-4">Simpan</button>
        </div>
    </form>
</main>
@endsection
