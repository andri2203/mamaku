@extends('layout')

@section('title', $title)

@section('content')
<main class="w-full grid grid-cols-3 gap-4" x-data="{teams: {{ Illuminate\Support\Js::from($teams) }}, isEditing: false, selectedTeam: null}">
    <form x-show="!isEditing" @submit="loading = true" action="{{ route('team.create') }}" method="post" class="col-span-1 p-4 h-fit bg-white border border-pink-400 rounded-lg" x-transition>
        @csrf
        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-bold mb-2">Nama Team:</label>
            <input type="text" id="name" name="name" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400" required>
        </div>
        <button type="submit" class="bg-pink-500 text-white px-4 py-2 rounded-lg hover:bg-pink-600">Buat Team</button>
    </form>
    <form x-show="isEditing" @submit="loading = true" :action="'/team/' + selectedTeam.id + '/edit'" method="post" class="col-span-1 p-4 h-fit bg-white border border-pink-400 rounded-lg" x-transition>
        @csrf
        @method('PUT')
        <div class="mb-4">
            <label for="name" class="block text-gray-700 font-bold mb-2">Nama Team:</label>
            <input type="text" id="name" name="name" :value="selectedTeam ? selectedTeam.name : ''" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400" required>
        </div>
        <div class="inline-flex gap-x-2">
            <button type="button" class="bg-gray-500 text-white px-4 py-2 rounded-lg hover:bg-gray-600 mr-2" @click="isEditing = false; selectedTeam = null;">Batal</button>
            <button type="submit" class="bg-pink-500 text-white px-4 py-2 rounded-lg hover:bg-pink-600">Edit Team</button>
        </div>
    </form>
    <section class="col-span-2">
        <h1 class="text-2xl font-bold mb-4">Daftar Team</h1>

        <div class="grid grid-cols-2 gap-4">
            <template x-for="(team, idx) in teams" :key="'team-' + idx">
                <div class="flex flex-col gap-y-2 p-4 h-fit bg-white border border-pink-400 rounded-lg shadow-sm shadow-pink-600">
                    <div class="text-lg font-semibold mb-2" x-text="team.name"></div>
                    <div class="text-sm text-gray-600 mb-2" x-text="team.users_count + ' anggota'">0 anggota</div>
                    <div class="flex gap-x-2">
                        <button type="button" class="bg-blue-500 text-white px-2 py-1 rounded" @click="isEditing = true; selectedTeam = team;">Edit</button>
                        <form :action="'/team/' + team.id" method="post" @submit="loading = true" onsubmit="return confirm('Yakin ingin menghapus admin ini?')" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-500 text-white px-2 py-1 rounded">Hapus</button>
                        </form>
                    </div>
                </div>
            </template>
        </div>
    </section>
</main>
@endsection
