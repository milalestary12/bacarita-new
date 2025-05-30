<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBuku = Buku::count();
        $totalStok = Buku::sum('stok');
        $bukuHabis = Buku::where('stok', 0)->count();
        
        $totalPenjualan = Penjualan::whereMonth('tanggal', Carbon::now()->month)
                        ->whereYear('tanggal', Carbon::now()->year)
                        ->sum('total');
        
        $penjualanTerbaru = Penjualan::with('user', 'detailPenjualans.buku')
                        ->orderBy('tanggal', 'desc')
                        ->limit(5)
                        ->get();
                        
        $bukuTerlaris = DetailPenjualan::select('buku_id', DB::raw('SUM(jumlah) as total_terjual'))
                        ->with('buku')
                        ->groupBy('buku_id')
                        ->orderBy('total_terjual', 'desc')
                        ->limit(5)
                        ->get();
                        
        $bukuStokMenipis = Buku::where('stok', '>', 0)
                        ->where('stok', '<=', 5)
                        ->orderBy('stok', 'asc')
                        ->limit(5)
                        ->get();
        
        return view('dashboard.index', compact('totalBuku', 'totalStok', 'bukuHabis', 'totalPenjualan', 'penjualanTerbaru', 'bukuTerlaris', 'bukuStokMenipis'));
    }
}