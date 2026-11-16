<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Dokter: subclass Pengguna (lewat user_id) sekaligus entity.
 */
class Dokter extends Model
{
    protected $table = 'dokters';

    protected $fillable = ['user_id', 'spesialisasi'];

    // ---- relasi ----
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function jadwalPraktiks(): HasMany
    {
        return $this->hasMany(JadwalPraktik::class);
    }

    public function kehadiranDokters(): HasMany
    {
        return $this->hasMany(KehadiranDokter::class);
    }

    public function kunjungans(): HasMany
    {
        return $this->hasMany(Kunjungan::class);
    }

    public function pemindahanAsal(): HasMany
    {
        return $this->hasMany(PemindahanDokter::class, 'dokter_asal_id');
    }

    public function pemindahanTujuan(): HasMany
    {
        return $this->hasMany(PemindahanDokter::class, 'dokter_tujuan_id');
    }

    public function revisiDiagnoses(): HasMany
    {
        return $this->hasMany(RevisiDiagnosis::class);
    }

    // ---- operasi ----
    /** Apakah dokter punya jadwal praktik pada hari dari $tanggal. */
    public function berjadwalPada($tanggal): bool
    {
        return $this->jadwalPraktiks()
            ->where('hari', JadwalPraktik::namaHari($tanggal))
            ->exists();
    }

    /**
     * Jadwal praktik tidak selalu berarti hadir: dokter dianggap hadir
     * jika berjadwal dan tidak ditandai "Tidak Hadir" pada tanggal itu.
     */
    public function hadirPada($tanggal): bool
    {
        $tgl = Carbon::parse($tanggal)->toDateString();

        return $this->berjadwalPada($tgl)
            && ! $this->kehadiranDokters()
                ->whereDate('tanggal', $tgl)
                ->where('status', 'Tidak Hadir')
                ->exists();
    }

    public function lihat_riwayat_pasien(Pasien $pasien)
    {
        return $pasien->tampil_riwayat();
    }
}
