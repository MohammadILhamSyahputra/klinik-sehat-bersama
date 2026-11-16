<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tagihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kunjungan_id')->unique()->constrained('kunjungans');
            $table->foreignId('kasir_id')->nullable()->constrained('users'); // kasir yang menutup transaksi
            $table->enum('status', ['Terbuka', 'Ditutup'])->default('Terbuka');
            $table->date('tgl_tutup')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tagihans');
    }
};
