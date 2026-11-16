<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('klaim_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('klaim_asuransi_id')->constrained('klaim_asuransis')->cascadeOnDelete();
            $table->foreignId('detail_tagihan_id')->constrained('detail_tagihans');
            $table->decimal('jumlah_diajukan', 12, 2);
            $table->decimal('jumlah_disetujui', 12, 2)->default(0);

            $table->unique(['klaim_asuransi_id', 'detail_tagihan_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('klaim_details');
    }
};
