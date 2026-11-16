<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Penghubung Kunjungan dan Tindakan. Tarif dibekukan saat tindakan dilakukan
 * sehingga perubahan tarif di masa depan tidak mengubah riwayat.
 */
class KunjunganTindakan extends Model
{
    protected $table = 'kunjungan_tindakans';

    protected $fillable = ['kunjungan_id', 'tindakan_id', 'jumlah', 'tarif_saat_itu'];

    protected $casts = ['tarif_saat_itu' => 'decimal:2'];

    public function kunjungan(): BelongsTo
    {
        return $this->belongsTo(Kunjungan::class);
    }

    public function tindakan(): BelongsTo
    {
        return $this->belongsTo(Tindakan::class);
    }

    public function detailTagihan(): HasOne
    {
        return $this->hasOne(DetailTagihan::class);
    }

    public static function tambah_tindakan(Kunjungan $kunjungan, Tindakan $tindakan, int $jumlah = 1): self
    {
        return self::create([
            'kunjungan_id' => $kunjungan->id,
            'tindakan_id' => $tindakan->id,
            'jumlah' => $jumlah,
            'tarif_saat_itu' => $tindakan->tarif,
        ]);
    }

    public function hitung_biaya_tindakan(): float
    {
        return (float) $this->tarif_saat_itu * $this->jumlah;
    }
}
