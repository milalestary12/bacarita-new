@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h1 class="text-2xl font-bold mb-6">Dashboard</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        <div class="bg-blue-100 rounded-lg p-4 flex flex-col items-center justify-center">
            <span class="text-3xl font-bold text-blue-700">{{ $totalBuku }}</span>
            <span class="text-blue-700">Total Buku</span>
        </div>
        <div class="bg-green-100 rounded-lg p-4 flex flex-col items-center justify-center">
            <span class="text-3xl font-bold text-green-700">{{ $totalStok }}</span>
            <span class="text-green-700">Total Stok</span>
        </div>
        <div class="bg-red-100 rounded-lg p-4 flex flex-col items-center justify-center">
            <span class="text-3xl font-bold text-red-700">{{ $bukuHabis }}</span>
            <span class="text-red-700">Buku Habis</span>
        </div>
        <div class="bg-purple-100 rounded-lg p-4 flex flex-col items-center justify-center">
            <span class="text-3xl font-bold text-purple-700">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</span>
            <span class="text-purple-700">Penjualan Bulan Ini</span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white border rounded-lg p-4">
            <h2 class="text-lg font-bold mb-4">Penjualan Terbaru</h2>
            @if($penjualanTerbaru->isEmpty())
                <p class="text-gray-500 text-center py-4">Data kosong</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kasir</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($penjualanTerbaru as $penjualan)
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $penjualan->tanggal }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $penjualan->user->name }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">Rp {{ number_format($penjualan->total, 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="bg-white border rounded-lg p-4">
            <h2 class="text-lg font-bold mb-4">Buku Terlaris</h2>
            @if($bukuTerlaris->isEmpty())
                <p class="text-gray-500 text-center py-4">Data kosong</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Buku</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Terjual</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($bukuTerlaris as $item)
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $item->buku->judul }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $item->total_terjual }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="bg-white border rounded-lg p-4">
            <h2 class="text-lg font-bold mb-4">Buku Stok Menipis</h2>
            @if($bukuStokMenipis->isEmpty())
                <p class="text-gray-500 text-center py-4">Data kosong</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul Buku</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Stok</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($bukuStokMenipis as $buku)
                            <tr>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $buku->judul }}</td>
                                <td class="px-4 py-2 whitespace-nowrap">{{ $buku->stok }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection