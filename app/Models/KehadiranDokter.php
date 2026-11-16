<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Kehadiran dokter pada tanggal tertentu (kenyataan, berbeda dari jadwal praktik).
 */
class KehadiranDokter extends Model
{
    protected $table = 'kehadiran_dokters';

    protected $fillable = ['dokter_id', 'tanggal', 'status', 'keterangan'];

    protected $casts = ['tanggal' => 'date'];

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class);
    }

    /**
     * Contoh pemakaian:
     * KehadiranDokter::firstOrNew(['dokter_id' => 1, 'tanggal' => '2026-11-20'])
     *     ->ubah_status('Tidak Hadir', 'Sakit');
     */
    public function ubah_status(string $status, ?string $keterangan = null): void
    {
        if (! in_array($status, ['Hadir', 'Tidak Hadir'], true)) {
            throw new \InvalidArgumentException('Status kehadiran harus Hadir atau Tidak Hadir.');
        }

        $this->status = $status;
        $this->keterangan = $keterangan;
        $this->save();
    }

    public function cek_kehadiran(): bool
    {
        return $this->status === 'Hadir';
    }
}
