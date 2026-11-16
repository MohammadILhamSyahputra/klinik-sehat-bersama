<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pasien_asuransis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasiens');
            $table->foreignId('perusahaan_asuransi_id')->constrained('perusahaan_asuransis');
            $table->string('no_polis', 50)->unique();
            $table->unsignedTinyInteger('prioritas')->default(1); // 1 = utama, 2 = kedua
            $table->date('masa_berlaku');

            $table->unique(['pasien_id', 'prioritas']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pasien_asuransis');
    }
};
