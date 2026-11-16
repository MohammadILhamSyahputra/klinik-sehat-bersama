<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('resep_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('resep_id')->constrained('reseps')->cascadeOnDelete();
            $table->foreignId('obat_id')->constrained('obats');
            $table->unsignedInteger('jumlah');
            $table->string('aturan_pakai', 255);
            $table->boolean('tersedia')->default(false);
            $table->enum('status_tebus', ['Belum', 'Sudah', 'Tidak Diserahkan'])->default('Belum');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resep_details');
    }
};
