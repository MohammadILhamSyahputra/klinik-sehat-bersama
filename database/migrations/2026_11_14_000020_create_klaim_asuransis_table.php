<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('klaim_asuransis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')->constrained('tagihans');
            $table->foreignId('pasien_asuransi_id')->constrained('pasien_asuransis');
            $table->string('no_klaim', 50)->unique();
            $table->date('tgl_pengajuan');
            $table->enum('status', ['Diajukan', 'Disetujui', 'Sebagian', 'Ditolak'])->default('Diajukan');
            $table->text('alasan_penolakan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('klaim_asuransis');
    }
};
