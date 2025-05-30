@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Daftar Buku</h1>
        @if(Auth::user()->role === 'admin')
        <a href="{{ route('buku.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Tambah Buku</a>
        @endif
    </div>

    <div class="mb-4">
        <form action="{{ route('buku.index') }}" method="GET" class="flex">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari judul, penulis, penerbit..." class="border rounded-l px-4 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded-r hover:bg-blue-700">Cari</button>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penulis</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penerbit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($bukus as $buku)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $buku->id }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $buku->judul }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $buku->kategori->nama_kategori }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $buku->penulis }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">{{ $buku->penerbit }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="@if($buku->stok == 0) text-red-600 font-bold @elseif($buku->stok <= 5) text-yellow-600 font-bold @else text-green-600 @endif">
                            {{ $buku->stok }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">Rp {{ number_format($buku->harga, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap flex gap-2">
                        <a href="{{ route('buku.show', $buku->id) }}" class="text-blue-600 hover:text-blue-900">Detail</a>
                        @if(Auth::user()->role === 'admin')
                        <a href="{{ route('buku.edit', $buku->id) }}" class="text-indigo-600 hover:text-indigo-900">Edit</a>
                        <form action="{{ route('buku.destroy', $buku->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus buku ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900">Hapus</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center">Data kosong</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $bukus->links() }}
    </div>
</div>
@endsection