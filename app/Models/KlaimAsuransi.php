<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

/**
 * Klaim ke perusahaan asuransi. Satu tagihan dapat punya lebih dari satu klaim
 * (pasien dengan dua asuransi). Hasil dicatat per item (KlaimDetail).
 */
class KlaimAsuransi extends Model
{
    protected $table = 'klaim_asuransis';

    protected $fillable = [
        'tagihan_id',
        'pasien_asuransi_id',
        'no_klaim',
        'tgl_pengajuan',
        'status',
        'alasan_penolakan',
    ];

    protected $casts = ['tgl_pengajuan' => 'date'];

    public function tagihan(): BelongsTo
    {
        return $this->belongsTo(Tagihan::class);
    }

    public function pasienAsuransi(): BelongsTo
    {
        return $this->belongsTo(PasienAsuransi::class);
    }

    public function klaimDetails(): HasMany
    {
        return $this->hasMany(KlaimDetail::class);
    }

    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    /**
     * Mengajukan klaim untuk item yang punya porsi asuransi
     * (panggil DetailTagihan::hitung_porsi() lebih dulu).
     */
    public static function ajukan_klaim(Tagihan $tagihan, PasienAsuransi $polis): self
    {
        return DB::transaction(function () use ($tagihan, $polis) {
            $polis->pilih_asuransi(); // menolak polis yang kedaluwarsa

            $klaim = self::create([
                'tagihan_id' => $tagihan->id,
                'pasien_asuransi_id' => $polis->id,
                'no_klaim' => self::buatNomorKlaim(),
                'tgl_pengajuan' => today(),
                'status' => 'Diajukan',
            ]);

            $items = $tagihan->detailTagihans()->where('ditanggung_asuransi', '>', 0)->get();
            foreach ($items as $detail) {
                $klaim->klaimDetails()->create([
                    'detail_tagihan_id' => $detail->id,
                    'jumlah_diajukan' => $detail->ditanggung_asuransi,
                    'jumlah_disetujui' => 0,
                ]);
            }

            return $klaim;
        });
    }

    /**
     * Mencatat hasil klaim per item: [detail_tagihan_id => jumlah_disetujui].
     * Item yang ditolak sebagian/seluruhnya menjadi Belum Lunas (porsi kembali ke pasien).
     */
    public function catat_hasil_klaim(array $hasil, ?string $alasanPenolakan = null): void
    {
        DB::transaction(function () use ($hasil, $alasanPenolakan) {
            $diajukan = 0.0;
            $disetujui = 0.0;

            foreach ($this->klaimDetails()->get() as $kd) {
                $kd->catat_jumlah_disetujui((float) ($hasil[$kd->detail_tagihan_id] ?? 0));
                $diajukan += (float) $kd->jumlah_diajukan;
                $disetujui += (float) $kd->jumlah_disetujui;
            }

            if ($disetujui <= 0) {
                $status = 'Ditolak';
            } elseif ($disetujui >= $diajukan) {
                $status = 'Disetujui';
            } else {
                $status = 'Sebagian';
            }

            $this->update([
                'status' => $status,
                'alasan_penolakan' => $status === 'Disetujui' ? null : $alasanPenolakan,
            ]);
        });
    }

    private static function buatNomorKlaim(): string
    {
        $urutan = self::whereDate('created_at', today())->count() + 1;

        return 'KLM-' . now()->format('Ymd') . '-' . str_pad((string) $urutan, 4, '0', STR_PAD_LEFT);
    }
}
