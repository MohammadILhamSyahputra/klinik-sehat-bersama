<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Riwayat perubahan diagnosis (lama, baru, alasan, waktu). */
class RevisiDiagnosis extends Model
{
    protected $table = 'revisi_diagnoses';

    protected $fillable = [
        'kunjungan_diagnosis_id',
        'dokter_id',
        'waktu_revisi',
        'diagnosis_lama',
        'diagnosis_baru',
        'alasan',
    ];

    protected $casts = ['waktu_revisi' => 'datetime'];

    public function kunjunganDiagnosis(): BelongsTo
    {
        return $this->belongsTo(KunjunganDiagnosis::class);
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class);
    }

    public static function catat_revisi(KunjunganDiagnosis $kd, int $dokterId, string $lama, string $baru, ?string $alasan = null): self
    {
        return self::create([
            'kunjungan_diagnosis_id' => $kd->id,
            'dokter_id' => $dokterId,
            'waktu_revisi' => now(),
            'diagnosis_lama' => $lama,
            'diagnosis_baru' => $baru,
            'alasan' => $alasan,
        ]);
    }

    public static function tampil_riwayat_revisi(KunjunganDiagnosis $kd)
    {
        return $kd->revisiDiagnoses()->with('dokter.user')->latest('waktu_revisi')->get();
    }
}
