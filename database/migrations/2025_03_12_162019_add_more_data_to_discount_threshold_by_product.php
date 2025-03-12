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
        Schema::table('diskon_threshold_by_products', function (Blueprint $table) {
            $table->foreignId('kategori_diskons_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('deskripsi_diskon')->nullable();
            $table->integer('stok_diskon')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diskon_threshold_by_products', function (Blueprint $table) {
            //
        });
    }
};
