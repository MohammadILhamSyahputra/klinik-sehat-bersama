<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kunjungan_tindakans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kunjungan_id')->constrained('kunjungans')->cascadeOnDelete();
            $table->foreignId('tindakan_id')->constrained('tindakans');
            $table->unsignedInteger('jumlah')->default(1);
            $table->decimal('tarif_saat_itu', 12, 2); // tarif dibekukan saat tindakan dilakukan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kunjungan_tindakans');
    }
};
