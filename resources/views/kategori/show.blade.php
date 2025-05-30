@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">Detail Kategori</h1>
        <a href="{{ route('kategori.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">Kembali</a>
    </div>

    <div class="border-t border-gray-200 py-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-gray-500">ID</p>
                <p class="text-lg">{{ $kategori->id }}</p>
            </div>
            <div>
                <p class="text-gray-500">Nama Kategori</p>
                <p class="text-lg">{{ $kategori->nama_kategori }}</p>
            </div>
        </div>
    </div>

    <div class="flex gap-2 mt-4">
        <a href="{{ route('kategori.edit', $kategori->id) }}" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Edit</a>
        <form action="{{ route('kategori.destroy', $kategori->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus kategori ini?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">Hapus</button>
        </form>
    </div>
</div>
@endsection