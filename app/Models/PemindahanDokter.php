<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Riwayat pemindahan pasien: dokter asal (tidak hadir) dan dokter tujuan (pengganti).
 */
class PemindahanDokter extends Model
{
    protected $table = 'pemindahan_dokters';

    protected $fillable = [
        'kunjungan_id',
        'dokter_asal_id',
        'dokter_tujuan_id',
        'waktu_pemindahan',
        'alasan',
    ];

    protected $casts = ['waktu_pemindahan' => 'datetime'];

    public function kunjungan(): BelongsTo
    {
        return $this->belongsTo(Kunjungan::class);
    }

    public function dokterAsal(): BelongsTo
    {
        return $this->belongsTo(Dokter::class, 'dokter_asal_id');
    }

    public function dokterTujuan(): BelongsTo
    {
        return $this->belongsTo(Dokter::class, 'dokter_tujuan_id');
    }

    public static function catat_pemindahan(Kunjungan $kunjungan, Dokter $asal, Dokter $tujuan, ?string $alasan = null): self
    {
        return self::create([
            'kunjungan_id' => $kunjungan->id,
            'dokter_asal_id' => $asal->id,
            'dokter_tujuan_id' => $tujuan->id,
            'waktu_pemindahan' => now(),
            'alasan' => $alasan ?? 'Dokter asal tidak hadir',
        ]);
    }

    public static function tampil_pemindahan(Kunjungan $kunjungan)
    {
        return $kunjungan->pemindahanDokters()
            ->with(['dokterAsal.user', 'dokterTujuan.user'])
            ->latest('waktu_pemindahan')
            ->get();
    }
}
