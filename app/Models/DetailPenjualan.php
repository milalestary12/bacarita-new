<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    use HasFactory;

    protected $fillable = [
        'penjualan_id',
        'buku_id',
        'jumlah',
        'harga',
        'subtotal'
    ];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function buku()
    {
        return $this->belongsTo(Buku::class);
    }
}