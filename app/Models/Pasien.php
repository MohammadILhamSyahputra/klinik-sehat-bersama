<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Pasien: entity (bukan aktor, bukan subclass Pengguna).
 */
class Pasien extends Model
{
    protected $table = 'pasiens';

    protected $fillable = [
        'no_identitas',
        'nama',
        'tgl_lahir',
        'alamat',
        'no_telp',
    ];

    protected $casts = ['tgl_lahir' => 'date'];

    // ---- relasi ----
    public function kunjungans(): HasMany
    {
        return $this->hasMany(Kunjungan::class);
    }

    public function pasienAsuransis(): HasMany
    {
        return $this->hasMany(PasienAsuransi::class);
    }

    // ---- operasi ----
    /** Mencari pasien lama berdasarkan nomor identitas (null = pasien baru). */
    public static function cari_by_identitas(string $noIdentitas): ?self
    {
        return self::where('no_identitas', $noIdentitas)->first();
    }

    public static function simpan_pasien(array $data): self
    {
        return self::create($data);
    }

    public function tampil_riwayat()
    {
        return $this->kunjungans()
            ->with(['dokter.user', 'kunjunganDiagnoses.diagnosis'])
            ->orderByDesc('tgl_kunjungan')
            ->get();
    }
}
