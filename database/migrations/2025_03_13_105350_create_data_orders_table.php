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
        Schema::create('data_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restos_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('outlets_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('order_id')->nullable();
            $table->string('nama_kasir')->nullable();
            $table->string('order_type')->nullable();
            $table->string('tables')->nullable();
            $table->integer('guest')->nullable();
            $table->integer('total_harga_beli')->nullable();
            $table->integer('total_harga_jual')->nullable();
            $table->integer('total_harga_before_rounding')->nullable();
            $table->integer('total_harga_after_all')->nullable();
            $table->integer('rounding')->nullable();
            $table->integer('amount_paid')->nullable();
            $table->integer('change')->nullable();
            $table->integer('keuntungan')->nullable();
            $table->integer('total_pajak')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_orders');
    }
};
