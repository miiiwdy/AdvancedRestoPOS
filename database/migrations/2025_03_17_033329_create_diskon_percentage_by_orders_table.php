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
        Schema::create('diskon_percentage_by_orders', function (Blueprint $table) {
            $table->bigIncrements('dp_id');
            $table->foreignId('restos_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('outlets_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('kategori_diskons_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('nama_diskon');
            $table->string('deskripsi_diskon');
            $table->integer('minimum_order_amount');
            $table->foreignId('percentage_value');
            $table->boolean('is_active')->default(true);
            $table->integer('stok_diskon');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('diskon_percentage_by_orders');
    }
};
