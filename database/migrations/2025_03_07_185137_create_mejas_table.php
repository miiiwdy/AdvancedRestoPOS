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
        Schema::create('mejas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_mejas_id')->nullable()->constrained('kategori_mejas')->onDelete('set null');
            $table->string('nomor_meja')->nullable();
            $table->string('guest')->nullable();
            $table->time('time_used')->nullable();
            $table->boolean('is_available')->nullable()->default('true');
            $table->boolean('is_served')->nullable()->default('false');
            $table->boolean('is_reserved')->nullable()->default('false');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mejas');
    }
};
