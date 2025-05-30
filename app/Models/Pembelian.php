<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembelian extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_id',
        'tanggal',
        'status',
        'total'
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function detailPembelians()
    {
        return $this->hasMany(DetailPembelian::class);
    }
}