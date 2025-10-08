@extends('layout')
@section('title', $title)

@section('content')
<main>
    <section class="flex flex-col items-start justify-start p-4 rounded-lg shadow-md bg-white border border-pink-200">
        <a href="{{ route('supplier.create') }}" class="bg-pink-500 text-white px-4 py-2 mb-4 rounded hover:bg-pink-600">Tambah Suplier</a>
        <div class="w-full overflow-x-auto">
            <table class="min-w-full border border-gray-300 rounded-lg">
                <thead>
                    <tr class="bg-pink-100">
                        <th class="px-4 py-2 border-b border-gray-300 text-left">No</th>
                        <th class="px-4 py-2 border-b border-gray-300 text-left">Nama</th>
                        <th class="px-4 py-2 border-b border-gray-300 text-left">Kontak</th>
                        <th class="px-4 py-2 border-b border-gray-300 text-left">Alamat</th>
                        <th class="px-4 py-2 border-b border-gray-300 text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($suppliers->isEmpty())
                    <tr>
                        <td colspan="5" class="px-4 py-2 border-b border-gray-300 text-center">Tidak ada Suplier</td>
                    </tr>
                    @endif
                    @foreach ($suppliers as $index => $supplier)
                    <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                        <td class="px-4 py-2 border-b border-gray-300">{{ $index + 1 }}</td>
                        <td class="px-4 py-2 border-b border-gray-300">{{ $supplier->name }}</td>
                        <td class="px-4 py-2 border-b border-gray-300">{{ $supplier->contact }}</td>
                        <td class="px-4 py-2 border-b border-gray-300">{{ $supplier->address }}</td>
                        <td class="px-4 py-2 border-b border-gray-300">
                            <a href="{{ route('supplier.edit', $supplier->id) }}" class="text-blue-500 hover:underline">Edit</a>
                            |
                            <form action="{{ route('supplier.destroy', $supplier->id) }}" @submit="loading = true" method="POST" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline" onclick="return confirm('Are you sure you want to delete this supplier?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>
</main>
@endsection
