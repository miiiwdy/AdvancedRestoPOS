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
        Schema::create('kategoris', function (Blueprint $table) {
            $table->id();
            $table->string('icon')->nullable(); //icon berbentuk i class, dapat diambil dari https://fontawesome.com/icons/
            $table->string('warna_teks_kategori')->nullable(); //untuk warna teks kategori di data produk
            $table->string('warna_background_kategori')->nullable(); //untuk wanra background kategori di data produk
            $table->string('nama_kategori')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategoris');
    }
};
