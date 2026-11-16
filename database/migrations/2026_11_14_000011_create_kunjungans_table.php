<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kunjungans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pasien_id')->constrained('pasiens');
            $table->foreignId('dokter_id')->constrained('dokters');
            $table->foreignId('petugas_id')->constrained('users');
            $table->date('tgl_kunjungan');
            $table->unsignedInteger('no_antrian');
            $table->enum('status', ['Terdaftar', 'Dipanggil', 'Selesai', 'Batal'])->default('Terdaftar');
            $table->text('keluhan')->nullable();
            $table->text('hasil_pemeriksaan')->nullable();
            $table->text('catatan_tambahan')->nullable();

            $table->unique(['dokter_id', 'tgl_kunjungan', 'no_antrian']);
            $table->index(['pasien_id', 'tgl_kunjungan']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kunjungans');
    }
};
