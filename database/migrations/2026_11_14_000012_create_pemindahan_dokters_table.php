<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pemindahan_dokters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kunjungan_id')->constrained('kunjungans')->cascadeOnDelete();
            $table->foreignId('dokter_asal_id')->constrained('dokters');
            $table->foreignId('dokter_tujuan_id')->constrained('dokters');
            $table->dateTime('waktu_pemindahan');
            $table->text('alasan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pemindahan_dokters');
    }
};
