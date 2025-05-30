<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'kategori_id',
        'penulis',
        'penerbit',
        'tahun_terbit',
        'stok',
        'harga',
        'deskripsi'
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function detailPenjualans()
    {
        return $this->hasMany(DetailPenjualan::class);
    }

    public function detailPembelians()
    {
        return $this->hasMany(DetailPembelian::class);
    }
}