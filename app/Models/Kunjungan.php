<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Kunjungan: pusat model. Status: Terdaftar, Dipanggil, Selesai, Batal.
 */
class Kunjungan extends Model
{
    protected $table = 'kunjungans';

    protected $fillable = [
        'pasien_id',
        'dokter_id',
        'petugas_id',
        'tgl_kunjungan',
        'no_antrian',
        'status',
        'keluhan',
        'hasil_pemeriksaan',
        'catatan_tambahan',
    ];

    protected $casts = ['tgl_kunjungan' => 'date'];

    // ---- relasi ----
    public function pasien(): BelongsTo
    {
        return $this->belongsTo(Pasien::class);
    }

    public function dokter(): BelongsTo
    {
        return $this->belongsTo(Dokter::class);
    }

    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function pemindahanDokters(): HasMany
    {
        return $this->hasMany(PemindahanDokter::class);
    }

    public function kunjunganDiagnoses(): HasMany
    {
        return $this->hasMany(KunjunganDiagnosis::class);
    }

    public function kunjunganTindakans(): HasMany
    {
        return $this->hasMany(KunjunganTindakan::class);
    }

    public function resep(): HasOne
    {
        return $this->hasOne(Resep::class);
    }

    public function tagihan(): HasOne
    {
        return $this->hasOne(Tagihan::class);
    }

    // ---- operasi ----
    /** Data wajib: pasien_id, dokter_id, petugas_id, tgl_kunjungan. */
    public static function simpan_kunjungan(array $data): self
    {
        return DB::transaction(function () use ($data) {
            $data['no_antrian'] = self::nomorAntrianBerikutnya($data['dokter_id'], $data['tgl_kunjungan']);
            $data['status'] = 'Terdaftar';

            return self::create($data);
        });
    }

    /** Pembatalan hanya untuk kunjungan yang belum diperiksa. */
    public function batalkan_kunjungan(): void
    {
        if (! in_array($this->status, ['Terdaftar', 'Dipanggil'], true)) {
            throw ValidationException::withMessages([
                'kunjungan' => 'Kunjungan yang sudah diperiksa atau dibatalkan tidak dapat dibatalkan.',
            ]);
        }

        $this->ubah_status('Batal');
    }

    /** Memindahkan pasien ke dokter pengganti dan mencatat pemindahannya. */
    public function pindah_dokter(Dokter $tujuan, ?string $alasan = null): PemindahanDokter
    {
        if ($this->status !== 'Terdaftar') {
            throw ValidationException::withMessages([
                'kunjungan' => 'Hanya kunjungan berstatus Terdaftar yang dapat dipindahkan.',
            ]);
        }

        if (! $tujuan->hadirPada($this->tgl_kunjungan)) {
            throw ValidationException::withMessages([
                'dokter' => 'Dokter pengganti harus berjadwal dan hadir pada hari tersebut.',
            ]);
        }

        return DB::transaction(function () use ($tujuan, $alasan) {
            $pemindahan = PemindahanDokter::catat_pemindahan($this, $this->dokter, $tujuan, $alasan);

            $this->update([
                'dokter_id' => $tujuan->id,
                'no_antrian' => self::nomorAntrianBerikutnya($tujuan->id, $this->tgl_kunjungan),
            ]);
            $this->unsetRelation('dokter');

            return $pemindahan;
        });
    }

    public function panggil_pasien(): void
    {
        if ($this->status !== 'Terdaftar') {
            throw ValidationException::withMessages([
                'kunjungan' => 'Hanya kunjungan berstatus Terdaftar yang dapat dipanggil.',
            ]);
        }

        $this->ubah_status('Dipanggil');
    }

    public function selesaikan_pemeriksaan(): void
    {
        if ($this->status !== 'Dipanggil') {
            throw ValidationException::withMessages([
                'kunjungan' => 'Pemeriksaan hanya dapat diselesaikan setelah pasien dipanggil.',
            ]);
        }

        $this->ubah_status('Selesai');
    }

    private function ubah_status(string $status): void
    {
        $this->update(['status' => $status]);
    }

    private static function nomorAntrianBerikutnya($dokterId, $tanggal): int
    {
        return (int) self::where('dokter_id', $dokterId)
            ->whereDate('tgl_kunjungan', $tanggal)
            ->lockForUpdate()
            ->max('no_antrian') + 1;
    }
}
