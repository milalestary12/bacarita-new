<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Pembelian;
use App\Models\DetailPembelian;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search ?? '';
        $tanggal_mulai = $request->tanggal_mulai ?? '';
        $tanggal_akhir = $request->tanggal_akhir ?? '';
        
        $pembelians = Pembelian::with('supplier')
            ->when($search, function($query) use($search) {
                return $query->whereHas('supplier', function($q) use($search) {
                    $q->where('nama', 'like', "%{$search}%");
                });
            })
            ->when($tanggal_mulai && $tanggal_akhir, function($query) use($tanggal_mulai, $tanggal_akhir) {
                return $query->whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir]);
            })
            ->orderBy('tanggal', 'desc')
            ->paginate(10);
        
        return view('pembelian.index', compact('pembelians', 'search', 'tanggal_mulai', 'tanggal_akhir'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $bukus = Buku::all();
        return view('pembelian.create', compact('suppliers', 'bukus'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'tanggal' => 'required|date',
            'buku_id' => 'required|array',
            'buku_id.*' => 'exists:bukus,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1',
            'harga' => 'required|array',
            'harga.*' => 'numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            $total = 0;
            for ($i = 0; $i < count($request->buku_id); $i++) {
                $total += $request->jumlah[$i] * $request->harga[$i];
            }

            $pembelian = Pembelian::create([
                'supplier_id' => $request->supplier_id,
                'tanggal' => $request->tanggal,
                'status' => 'pending',
                'total' => $total
            ]);

            for ($i = 0; $i < count($request->buku_id); $i++) {
                DetailPembelian::create([
                    'pembelian_id' => $pembelian->id,
                    'buku_id' => $request->buku_id[$i],
                    'jumlah' => $request->jumlah[$i],
                    'harga' => $request->harga[$i],
                    'subtotal' => $request->jumlah[$i] * $request->harga[$i]
                ]);
            }

            DB::commit();
            return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function show(Pembelian $pembelian)
    {
        $pembelian->load('supplier', 'detailPembelians.buku');
        return view('pembelian.show', compact('pembelian'));
    }

    public function edit(Pembelian $pembelian)
    {
        if ($pembelian->status === 'selesai') {
            return redirect()->route('pembelian.index')->with('error', 'Pembelian yang sudah selesai tidak dapat diedit.');
        }
        
        $pembelian->load('detailPembelians.buku');
        $suppliers = Supplier::all();
        $bukus = Buku::all();
        return view('pembelian.edit', compact('pembelian', 'suppliers', 'bukus'));
    }

    public function update(Request $request, Pembelian $pembelian)
    {
        if ($pembelian->status === 'selesai') {
            return redirect()->route('pembelian.index')->with('error', 'Pembelian yang sudah selesai tidak dapat diubah.');
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'tanggal' => 'required|date',
            'buku_id' => 'required|array',
            'buku_id.*' => 'exists:bukus,id',
            'jumlah' => 'required|array',
            'jumlah.*' => 'integer|min:1',
            'harga' => 'required|array',
            'harga.*' => 'numeric|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Hapus detail pembelian lama
            DetailPembelian::where('pembelian_id', $pembelian->id)->delete();

            $total = 0;
            for ($i = 0; $i < count($request->buku_id); $i++) {
                $total += $request->jumlah[$i] * $request->harga[$i];
            }

            $pembelian->update([
                'supplier_id' => $request->supplier_id,
                'tanggal' => $request->tanggal,
                'total' => $total
            ]);

            for ($i = 0; $i < count($request->buku_id); $i++) {
                DetailPembelian::create([
                    'pembelian_id' => $pembelian->id,
                    'buku_id' => $request->buku_id[$i],
                    'jumlah' => $request->jumlah[$i],
                    'harga' => $request->harga[$i],
                    'subtotal' => $request->jumlah[$i] * $request->harga[$i]
                ]);
            }

            DB::commit();
            return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Pembelian $pembelian)
    {
        if ($pembelian->status === 'selesai') {
            return redirect()->route('pembelian.index')->with('error', 'Pembelian yang sudah selesai tidak dapat dihapus.');
        }

        DB::beginTransaction();
        try {
            DetailPembelian::where('pembelian_id', $pembelian->id)->delete();
            $pembelian->delete();
            
            DB::commit();
            return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pembelian.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function konfirmasi(Pembelian $pembelian)
    {
        if ($pembelian->status === 'selesai') {
            return redirect()->route('pembelian.index')->with('error', 'Pembelian sudah dikonfirmasi sebelumnya.');
        }

        DB::beginTransaction();
        try {
            // Update status pembelian
            $pembelian->update(['status' => 'selesai']);

            // Tambahkan stok buku
            foreach ($pembelian->detailPembelians as $detail) {
                $buku = Buku::find($detail->buku_id);
                $buku->stok += $detail->jumlah;
                $buku->save();
            }
            
            DB::commit();
            return redirect()->route('pembelian.index')->with('success', 'Pembelian berhasil dikonfirmasi dan stok buku telah ditambahkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('pembelian.index')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function exportPdf(Request $request)
    {
        $tanggal_mulai = $request->tanggal_mulai ?? '';
        $tanggal_akhir = $request->tanggal_akhir ?? '';
        
        $pembelians = Pembelian::with('supplier', 'detailPembelians.buku')
            ->when($tanggal_mulai && $tanggal_akhir, function($query) use($tanggal_mulai, $tanggal_akhir) {
                return $query->whereBetween('tanggal', [$tanggal_mulai, $tanggal_akhir]);
            })
            ->orderBy('tanggal', 'desc')
            ->get();
        
        $pdf = PDF::loadView('pembelian.pdf', compact('pembelians', 'tanggal_mulai', 'tanggal_akhir'));
        return $pdf->download('laporan-pembelian.pdf');
    }
}