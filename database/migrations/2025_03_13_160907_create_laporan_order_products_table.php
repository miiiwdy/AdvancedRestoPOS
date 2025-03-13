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
        Schema::create('laporan_order_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restos_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('outlets_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('order_id')->nullable();
            $table->string('nama_kasir');
            $table->foreignId('id_product')->constrained('products')->onDelete('cascade');
            $table->string('nama_product');
            $table->string('kode_product');
            $table->string('foto_product')->nullable();
            $table->foreignId('kategori_id')->constrained('kategoris')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('harga_beli_per_item', 10, 2);
            $table->decimal('total_harga_beli', 10, 2);
            $table->decimal('harga_jual_per_item', 10, 2);
            $table->decimal('total_harga_jual_before', 10, 2);
            $table->decimal('total_harga_jual_after', 10, 2);
            $table->decimal('total_pajak', 10, 2);
            $table->decimal('total_after_rounding', 10, 2);
            $table->string('order_type');
            $table->text('note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_order_products');
    }
};
