<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPembelian extends Model
{
    use HasFactory;

    protected $fillable = [
        'pembelian_id',
        'buku_id',
        'jumlah',
        'harga',
        'subtotal'
    ];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }
}