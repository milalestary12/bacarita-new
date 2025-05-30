<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
    Schema::create('detail_penjualans', function (Blueprint $table) {
        $table->id();
        $table->foreignId('penjualan_id')->constrained()->onDelete('cascade');
        $table->foreignId('buku_id')->constrained();
        $table->integer('jumlah');
        $table->decimal('harga', 10, 2);
        $table->decimal('subtotal', 12, 2);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_penjualans');
    }
};
