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
    Schema::create('pembelians', function (Blueprint $table) {
        $table->id();
        $table->foreignId('supplier_id')->constrained();
        $table->date('tanggal');
        $table->enum('status', ['pending', 'selesai'])->default('pending');
        $table->decimal('total', 12, 2);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembelians');
    }
};
