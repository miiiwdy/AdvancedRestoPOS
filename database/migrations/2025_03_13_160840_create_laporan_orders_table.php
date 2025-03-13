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
        Schema::create('laporan_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restos_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('outlets_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('order_id');
            $table->string('nama_kasir');
            $table->string('order_type');
            $table->string('tables')->nullable();
            $table->integer('guest');
            $table->decimal('total_harga_beli', 10,2);
            $table->decimal('total_harga_jual', 10,2);
            $table->decimal('total_harga_before_rounding', 10,2);
            $table->decimal('total_harga_after_all', 10,2);
            $table->integer('rounding')->nullable();
            $table->decimal('amount_paid', 10,2)->nullable();
            $table->decimal('change',10,2)->nullable();
            $table->decimal('keuntungan',10,2);
            $table->decimal('total_pajak',10,2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_orders');
    }
};
