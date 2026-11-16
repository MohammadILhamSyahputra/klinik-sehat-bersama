<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kehadiran_dokters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dokter_id')->constrained('dokters');
            $table->date('tanggal');
            $table->enum('status', ['Hadir', 'Tidak Hadir'])->default('Hadir');
            $table->text('keterangan')->nullable();

            $table->unique(['dokter_id', 'tanggal']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kehadiran_dokters');
    }
};
