<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Satu baris biaya (pendaftaran, pemeriksaan, tindakan, atau obat) dengan
 * porsi asuransi, porsi pasien, dan status pembayaran sendiri.
 */
class DetailTagihan extends Model
{
    protected $table = 'detail_tagihans';

    protected $fillable = [
        'tagihan_id',
        'jenis_biaya',
        'kunjungan_tindakan_id',
        'resep_detail_id',
        'jumlah_biaya',
        'ditanggung_asuransi',
        'ditanggung_pasien',
        'status_pembayaran',
    ];

    protected $casts = [
        'jumlah_biaya' => 'decimal:2',
        'ditanggung_asuransi' => 'decimal:2',
        'ditanggung_pasien' => 'decimal:2',
    ];

    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(Tagihan::class);
    }

    public function kunjunganTindakan(): BelongsTo
    {
        return $this->belongsTo(KunjunganTindakan::class);
    }

    public function resepDetail(): BelongsTo
    {
        return $this->belongsTo(ResepDetail::class);
    }

    public function klaimDetails(): HasMany
    {
        return $this->hasMany(KlaimDetail::class);
    }

    /** Membagi biaya: porsi asuransi dan sisanya ditanggung pasien. */
    public function hitung_porsi(float $ditanggungAsuransi): void
    {
        $biaya = (float) $this->jumlah_biaya;
        $asuransi = min(max($ditanggungAsuransi, 0), $biaya);

        $this->update([
            'ditanggung_asuransi' => $asuransi,
            'ditanggung_pasien' => $biaya - $asuransi,
        ]);
    }

    public function ubah_status_pembayaran(string $status): void
    {
        if (! in_array($status, ['Belum Ditentukan', 'Lunas', 'Belum Lunas'], true)) {
            throw new \InvalidArgumentException('Status pembayaran tidak valid.');
        }

        $this->update(['status_pembayaran' => $status]);
    }
}
