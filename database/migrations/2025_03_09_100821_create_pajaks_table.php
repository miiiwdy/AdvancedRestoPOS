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
        Schema::create('pajaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('restos_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('outlets_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('nama_pajak')->nullable();
            $table->integer('jumlah_pajak')->nullable();
            $table->boolean('is_active')->default(false)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pajaks');
    }
};
