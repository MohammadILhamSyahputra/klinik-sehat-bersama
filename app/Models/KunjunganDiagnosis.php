<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * Penghubung Kunjungan dan Diagnosis (satu kunjungan dapat punya banyak diagnosis).
 */
class KunjunganDiagnosis extends Model
{
    protected $table = 'kunjungan_diagnoses';

    protected $fillable = ['kunjungan_id', 'diagnosis_id', 'jenis'];

    public function kunjungan(): BelongsTo
    {
        return $this->belongsTo(Kunjungan::class);
    }

    public function diagnosis(): BelongsTo
    {
        return $this->belongsTo(Diagnosis::class);
    }

    public function revisiDiagnoses(): HasMany
    {
        return $this->hasMany(RevisiDiagnosis::class);
    }

    public static function tambah_diagnosis(Kunjungan $kunjungan, int $diagnosisId, string $jenis = 'Sekunder'): self
    {
        return self::firstOrCreate(
            ['kunjungan_id' => $kunjungan->id, 'diagnosis_id' => $diagnosisId],
            ['jenis' => $jenis]
        );
    }

    /**
     * Mengubah diagnosis: diagnosis lama dicatat sebagai revisi (tidak ditimpa tanpa jejak).
     * Jika tagihan sudah dibuat, tagihan dihitung ulang.
     */
    public function ubah_diagnosis(Diagnosis $baru, int $dokterId, ?string $alasan = null): void
    {
        DB::transaction(function () use ($baru, $dokterId, $alasan) {
            RevisiDiagnosis::catat_revisi(
                $this,
                $dokterId,
                $this->diagnosis->nama_diagnosis,
                $baru->nama_diagnosis,
                $alasan
            );

            $this->update(['diagnosis_id' => $baru->id]);

            $tagihan = $this->kunjungan->tagihan;
            if ($tagihan) {
                $tagihan->hitung_ulang();
            }
        });
    }
}
