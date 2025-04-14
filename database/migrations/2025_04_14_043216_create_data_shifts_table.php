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
        Schema::create('data_shifts', function (Blueprint $table) {
            $table->id();
            $table->integer('no_shift');
            $table->string('nama_shift');
            $table->foreignId('restos_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('outlets_id')->nullable()->constrained()->onDelete('cascade');
            $table->time('shift_start');
            $table->time('shift_end');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_shifts');
    }
};
