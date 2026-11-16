<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/** Hasil klaim per item biaya (jumlah diajukan dan jumlah disetujui). */
class KlaimDetail extends Model
{
    protected $table = 'klaim_details';

    protected $fillable = ['klaim_asuransi_id', 'detail_tagihan_id', 'jumlah_diajukan', 'jumlah_disetujui'];

    protected $casts = [
        'jumlah_diajukan' => 'decimal:2',
        'jumlah_disetujui' => 'decimal:2',
    ];

    public function klaimAsuransi(): BelongsTo
    {
        return $this->belongsTo(KlaimAsuransi::class);
    }

    public function detailTagihan(): BelongsTo
    {
        return $this->belongsTo(DetailTagihan::class);
    }

    /**
     * Mencatat jumlah yang disetujui dan memperbarui porsi serta status item tagihan:
     * jika porsi pasien nol -> Lunas, selain itu Belum Lunas (sampai dibayar tunai).
     */
    public function catat_jumlah_disetujui(float $jumlah): void
    {
        $disetujui = min(max($jumlah, 0), (float) $this->jumlah_diajukan);
        $this->update(['jumlah_disetujui' => $disetujui]);

        $detail = $this->detailTagihan;
        $detail->hitung_porsi($disetujui);
        $detail->ubah_status_pembayaran(
            (float) $detail->ditanggung_pasien <= 0 ? 'Lunas' : 'Belum Lunas'
        );
    }
}
