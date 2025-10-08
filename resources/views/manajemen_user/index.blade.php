@extends('layout')

@section('title', $title)

@section('content')
<main class="flex flex-col justify-start items-start w-full" x-data="{
    users: {{ Illuminate\Support\Js::from($users) }},
    search: '',
    get filteredUsers() {
        if (this.search.trim() === '') {
            return this.users;
        }
        return this.users.filter(user => user.name.toLowerCase().includes(this.search.toLowerCase().trim()) || user.email.toLowerCase().includes(this.search.toLowerCase().trim()) || user.team.name.toLowerCase().includes(this.search.toLowerCase().trim()));
    }
}">
    <div class="inline-flex justify-between items-center w-full gap-x-4 mb-4 border-b border-pink-300 pb-4">
        <a href="{{ route('admin.create') }}" class="px-4 py-2 bg-pink-400 text-white rounded-xl hover:bg-pink-600 transition">Tambah Admin</a>

        <input type="text" class="flex-1 px-4 py-2 bg-white border border-pink-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-pink-400" placeholder="Cari Nama/Email/Team..." x-model="search">
    </div>

    <div class="grid grid-cols-3 gap-4 w-full">
        <div class="col-span-3 text-pink-700 mb-4 border-dashed border-pink-700" x-show="filteredUsers.length === 0">
            Tidak ada admin yang ditemukan.
        </div>
        <template x-for="(user, idx) in filteredUsers" :key="'user-' + idx">
            <div class="col-span-1 p-4 bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="flex flex-col items-center gap-3 mb-4">
                    <div x-show="user.photo == null" class="w-24 h-24 rounded-full bg-pink-200 flex items-center justify-center text-xl font-bold text-pink-600" x-text="user.name.charAt(0).toUpperCase()"></div>
                    <img x-show="user.photo != null" loading="lazy" :src="'/file/' + user.photo" alt="User Photo" class="w-24 h-24 rounded-full object-cover">
                    <div>

                        <div class="flex items-center gap-x-1">
                            <p class="font-semibold text-gray-800" x-text="user.name"></p>
                            <p class="text-sm text-white bg-pink-400 px-2 py-0.5 rounded-full" x-text="user.level"></p>
                        </div>
                        <div class="flex items-center gap-x-1">
                            <p class="text-sm text-gray-500" x-text="'Email : ' + user.email"></p>
                            <span x-show="user.email_verified_at" class="text-xs text-green-600">(Verifikasi)</span>
                            <span x-show="!user.email_verified_at" class="text-xs text-red-600">(Belum Verifikasi)</span>
                        </div>
                        <p class="text-sm text-gray-500" x-text="'Team : ' + user.team.name"></p>
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <a :href="'/admin/' + user.id + '/edit'" @click="loading = true" class="px-3 py-1.5 bg-yellow-400 text-white rounded-xl hover:bg-yellow-600 transition">Edit</a>
                    <form :action="'/admin/' + user.id" method="post" @submit="loading = true" onsubmit="return confirm('Yakin ingin menghapus admin ini?')">
                        @csrf
                        @method('DELETE')
                        <button class="px-3 py-1.5 bg-red-400 text-white rounded-xl hover:bg-red-600 transition">Hapus</button>
                    </form>
                </div>
            </div>
        </template>
    </div>
</main>
@endsection
