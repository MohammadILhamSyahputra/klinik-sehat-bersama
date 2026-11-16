<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')->constrained('tagihans')->cascadeOnDelete();
            $table->foreignId('kasir_id')->constrained('users');
            $table->foreignId('klaim_asuransi_id')->nullable()->constrained('klaim_asuransis');
            $table->enum('metode', ['Tunai', 'Asuransi']);
            $table->decimal('jumlah', 12, 2);
            $table->dateTime('tgl_bayar');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
