<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Uang yang benar-benar dibayar (tunai atau dari klaim asuransi). */
class Pembayaran extends Model
{
    protected $table = 'pembayarans';

    protected $fillable = ['tagihan_id', 'kasir_id', 'klaim_asuransi_id', 'metode', 'jumlah', 'tgl_bayar'];

    protected $casts = [
        'jumlah' => 'decimal:2',
        'tgl_bayar' => 'datetime',
    ];

    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(Tagihan::class);
    }

    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function klaimAsuransi(): BelongsTo
    {
        return $this->belongsTo(KlaimAsuransi::class);
    }

    /**
     * Mencatat pembayaran. Untuk tunai, isi $uangBayar agar kembalian dihitung.
     * Mengembalikan ['pembayaran' => Pembayaran, 'kembalian' => float].
     */
    public static function catat_pembayaran(
        Tagihan $tagihan,
        int $kasirId,
        string $metode,
        float $jumlah,
        ?int $klaimAsuransiId = null,
        ?float $uangBayar = null
    ): array {
        return DB::transaction(function () use ($tagihan, $kasirId, $metode, $jumlah, $klaimAsuransiId, $uangBayar) {
            $pembayaran = self::create([
                'tagihan_id' => $tagihan->id,
                'kasir_id' => $kasirId,
                'klaim_asuransi_id' => $klaimAsuransiId,
                'metode' => $metode,
                'jumlah' => $jumlah,
                'tgl_bayar' => now(),
            ]);

            $kembalian = ($metode === 'Tunai' && $uangBayar !== null)
                ? $pembayaran->hitung_kembalian($uangBayar)
                : 0.0;

            return ['pembayaran' => $pembayaran, 'kembalian' => $kembalian];
        });
    }

    private function hitung_kembalian(float $uangBayar): float
    {
        if ($uangBayar < (float) $this->jumlah) {
            throw ValidationException::withMessages(['uang_bayar' => 'Uang bayar kurang.']);
        }

        return $uangBayar - (float) $this->jumlah;
    }
}
