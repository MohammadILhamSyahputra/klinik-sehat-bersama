<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('revisi_diagnoses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kunjungan_diagnosis_id')->constrained('kunjungan_diagnoses')->cascadeOnDelete();
            $table->foreignId('dokter_id')->constrained('dokters');
            $table->dateTime('waktu_revisi');
            $table->string('diagnosis_lama', 200);
            $table->string('diagnosis_baru', 200);
            $table->text('alasan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('revisi_diagnoses');
    }
};
