<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run()
    {
        $kategoris = [
            'Fiksi',
            'Non-Fiksi',
            'Sejarah',
            'Sains',
            'Teknologi',
            'Bisnis',
            'Pendidikan',
            'Anak-anak',
            'Novel',
            'Komik',
        ];

        foreach ($kategoris as $kategori) {
            Kategori::create(['nama_kategori' => $kategori]);
        }
    }
}