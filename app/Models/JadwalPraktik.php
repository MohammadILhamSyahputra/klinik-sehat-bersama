<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Jadwal praktik = rencana rutin dokter (bukan kepastian hadir).
 */
class JadwalPraktik extends Model
{
    protected $table = 'jadwal_praktiks';

    protected $fillable = ['dokter_id', 'hari', 'jam_mulai', 'jam_selesai'];

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class);
    }

    public static function namaHari($tanggal): string
    {
        $hari = [1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu', 7 => 'Minggu'];

        return $hari[Carbon::parse($tanggal)->dayOfWeekIso];
    }

    public static function tampil_jadwal()
    {
        return self::with('dokter.user')
            ->orderBy('hari')
            ->orderBy('jam_mulai')
            ->get();
    }

    /** Dokter yang berjadwal dan hadir pada tanggal tertentu (calon dokter pengganti). */
    public static function cari_dokter_berjadwal($tanggal)
    {
        $tgl = Carbon::parse($tanggal)->toDateString();

        return Dokter::with('user')
            ->whereHas('jadwalPraktiks', fn ($q) => $q->where('hari', self::namaHari($tgl)))
            ->whereDoesntHave('kehadiranDokters', fn ($q) => $q
                ->whereDate('tanggal', $tgl)
                ->where('status', 'Tidak Hadir'))
            ->get();
    }
}
