<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Penjualan;
use App\Models\DetailPenjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PDF;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search ?? '';
        $tanggal_mulai = $request->tanggal_mulai ?? '';
        $tanggal_akhir = $request->tanggal_akhir ?? '';
        
        $penjualans = Penjualan::with('user')
            ->when($search, function($query) use($search) {
                return $query->whereHas('user', function($q) use($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->when($tanggal_mulai && $tanggal_akhir, function($query) use($tanggal_mulai, $tanggal_akhir) {
                return $query->whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir]);
            })
            ->orderBy('tanggal', 'desc')
            ->paginate(10);
        
        return view('penjualan.index', compact('penjualans', 'search', 'tanggal_mulai', 'tanggal_akhir'));
    }

    public function create()
    {
        $bukus = Buku::where('stok', '>', 0)->get();
        return view('penjualan.create', compact('bukus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'buku_id' => 'required|array',
            'buku_id.*' => 'exists:bukus,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1',
        ]);

        DB::beginTransaction();
        try {
            $total = 0;
            // Cek stok dan hitung total
            for ($i = 0; $i < count($request->buku_id); $i++) {
                $buku = Buku::findOrFail($request->buku_id[$i]);
                if ($buku->stok < $request->jumlah[$i]) {
                    return redirect()->back()->with('error', "Stok buku '{$buku->judul}' tidak mencukupi. Stok tersedia: {$buku->stok}")->withInput();
                }
                $total += $request->jumlah[$i] * $buku->harga;
            }

            $penjualan = Penjualan::create([
                'user_id' => Auth::id(),
                'tanggal' => $request->tanggal,
                'total' => $total
            ]);

            for ($i = 0; $i < count($request->buku_id); $i++) {
                $buku = Buku::findOrFail($request->buku_id[$i]);
                DetailPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'buku_id' => $request->buku_id[$i],
                    'jumlah' => $request->jumlah[$i],
                    'harga' => $buku->harga,
                    'subtotal' => $request->jumlah[$i] * $buku->harga
                ]);

                // Kurangi stok buku
                $buku->stok -= $request->jumlah[$i];
                $buku->save();
            }

            DB::commit();
            return redirect()->route('penjualan.show', $penjualan->id)->with('success', 'Penjualan berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Penjualan $penjualan)
    {
        $penjualan->load('user', 'detailPenjualans.buku');
        return view('penjualan.show', compact('penjualan'));
    }

    public function exportPdf(Request $request)
    {
        $tanggal_mulai = $request->tanggal_mulai ?? '';
        $tanggal_akhir = $request->tanggal_akhir ?? '';
        
        $penjualans = Penjualan::with('user', 'detailPenjualans.buku')
            ->when($tanggal_mulai && $tanggal_akhir, function($query) use($tanggal_mulai, $tanggal_akhir) {
                return $query->whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir]);
            })
            ->orderBy('tanggal', 'desc')
            ->get();
        
        $pdf = PDF::loadView('penjualan.pdf', compact('penjualans', 'tanggal_mulai', 'tanggal_akhir'));
        return $pdf->download('laporan-penjualan.pdf');
    }
}