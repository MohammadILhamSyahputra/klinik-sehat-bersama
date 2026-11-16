<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * PerusahaanAsuransi: aktor eksternal sekaligus entity (bukan subclass Pengguna).
 */
class PerusahaanAsuransi extends Model
{
    protected $table = 'perusahaan_asuransis';

    protected $fillable = ['nama_asuransi'];

    public function pasienAsuransis(): HasMany
    {
        return $this->hasMany(PasienAsuransi::class);
    }

    public static function tampil_asuransi()
    {
        return self::orderBy('nama_asuransi')->get();
    }

    /** Perusahaan asuransi menyampaikan hasil klaim: [detail_tagihan_id => jumlah_disetujui]. */
    public function proses_klaim(KlaimAsuransi $klaim, array $hasil, ?string $alasanPenolakan = null): void
    {
        $klaim->catat_hasil_klaim($hasil, $alasanPenolakan);
    }
}
