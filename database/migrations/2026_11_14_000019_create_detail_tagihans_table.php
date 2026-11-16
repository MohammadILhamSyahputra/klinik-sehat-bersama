<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_tagihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')->constrained('tagihans')->cascadeOnDelete();
            $table->enum('jenis_biaya', ['Pendaftaran', 'Pemeriksaan', 'Tindakan', 'Obat']);
            $table->foreignId('kunjungan_tindakan_id')->nullable()->constrained('kunjungan_tindakans')->nullOnDelete();
            $table->foreignId('resep_detail_id')->nullable()->constrained('resep_details')->nullOnDelete();
            $table->decimal('jumlah_biaya', 12, 2);
            $table->decimal('ditanggung_asuransi', 12, 2)->default(0);
            $table->decimal('ditanggung_pasien', 12, 2)->default(0);
            $table->enum('status_pembayaran', ['Belum Ditentukan', 'Lunas', 'Belum Lunas'])->default('Belum Ditentukan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_tagihans');
    }
};
