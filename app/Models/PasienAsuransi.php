<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

/**
 * Polis asuransi milik pasien. Satu pasien dapat memiliki dua polis
 * (prioritas 1 = utama, 2 = kedua).
 */
class PasienAsuransi extends Model
{
    protected $table = 'pasien_asuransis';

    protected $fillable = [
        'pasien_id',
        'perusahaan_asuransi_id',
        'no_polis',
        'prioritas',
        'masa_berlaku',
    ];

    protected $casts = ['masa_berlaku' => 'date'];

    // ---- relasi ----
    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }

    public function perusahaanAsuransi(): BelongsTo
    {
        return $this->belongsTo(PerusahaanAsuransi::class);
    }

    public function klaimAsuransis(): HasMany
    {
        return $this->hasMany(KlaimAsuransi::class);
    }

    // ---- operasi ----
    /** Memilih polis untuk pembayaran; ditolak jika masa berlaku habis. */
    public function pilih_asuransi(): self
    {
        if (! $this->cek_masa_berlaku()) {
            throw ValidationException::withMessages([
                'polis' => 'Masa berlaku polis asuransi sudah habis.',
            ]);
        }

        return $this;
    }

    private function cek_masa_berlaku(): bool
    {
        return $this->masa_berlaku->greaterThanOrEqualTo(today());
    }
}
