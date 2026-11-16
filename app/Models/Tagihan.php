<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Tagihan satu kunjungan. total_biaya adalah atribut turunan.
 * Biaya pendaftaran dan pemeriksaan diambil dari config/klinik.php.
 */
class Tagihan extends Model
{
    protected $table = 'tagihans';

    protected $fillable = ['kunjungan_id', 'kasir_id', 'status', 'tgl_tutup'];

    protected $casts = ['tgl_tutup' => 'date'];

    // ---- relasi ----
    public function kunjungan(): BelongsTo
    {
        return $this->belongsTo(Kunjungan::class);
    }

    public function kasir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    public function detailTagihans(): HasMany
    {
        return $this->hasMany(DetailTagihan::class);
    }

    public function pembayarans(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function klaimAsuransis(): HasMany
    {
        return $this->hasMany(KlaimAsuransi::class);
    }

    /** Atribut turunan: $tagihan->total_biaya */
    protected function totalBiaya(): Attribute
    {
        return Attribute::get(fn () => (float) $this->detailTagihans()->sum('jumlah_biaya'));
    }

    // ---- operasi ----
    /** Dipanggil setelah pemeriksaan selesai. */
    public static function hitung_tagihan(Kunjungan $kunjungan): self
    {
        return DB::transaction(function () use ($kunjungan) {
            $tagihan = self::firstOrCreate(
                ['kunjungan_id' => $kunjungan->id],
                ['status' => 'Terbuka']
            );

            $tagihan->sinkronkanDetail();

            return $tagihan;
        });
    }

    /**
     * Menghitung ulang (mis. diagnosis berubah atau obat tidak diserahkan).
     * Jika tagihan sudah ditutup dan nilainya berubah, tagihan dibuka kembali
     * sebagai penyesuaian.
     */
    public function hitung_ulang(): void
    {
        DB::transaction(function () {
            $sebelum = (float) $this->detailTagihans()->sum('jumlah_biaya');

            $this->sinkronkanDetail();

            $sesudah = (float) $this->detailTagihans()->sum('jumlah_biaya');

            if ($this->status === 'Ditutup' && $sebelum !== $sesudah) {
                $this->update(['status' => 'Terbuka', 'tgl_tutup' => null]);
            }
        });
    }

    /** Kasir hanya dapat menutup transaksi jika semua biaya sudah punya status yang jelas. */
    public function tutup_transaksi(int $kasirId): void
    {
        if (! $this->validasi_status()) {
            throw ValidationException::withMessages([
                'tagihan' => 'Transaksi belum dapat ditutup: masih ada biaya yang status pembayarannya belum jelas.',
            ]);
        }

        $this->update([
            'status' => 'Ditutup',
            'tgl_tutup' => today(),
            'kasir_id' => $kasirId,
        ]);
    }

    private function validasi_status(): bool
    {
        if (! $this->detailTagihans()->exists()) {
            return false;
        }

        return ! $this->detailTagihans()
            ->where('status_pembayaran', 'Belum Ditentukan')
            ->exists();
    }

    private function sinkronkanDetail(): void
    {
        $kunjungan = $this->kunjungan()->with(['kunjunganTindakans', 'resep'])->first();

        $this->pastikanDetail('Pendaftaran', null, null, (float) config('klinik.biaya_pendaftaran'));
        $this->pastikanDetail('Pemeriksaan', null, null, (float) config('klinik.biaya_pemeriksaan'));

        foreach ($kunjungan->kunjunganTindakans as $kt) {
            $this->pastikanDetail('Tindakan', $kt->id, null, $kt->hitung_biaya_tindakan());
        }

        if ($kunjungan->resep) {
            foreach ($kunjungan->resep->resepDetails()->with('obat')->get() as $rd) {
                if ($rd->status_tebus === 'Tidak Diserahkan') {
                    // obat yang tidak diserahkan tidak ditagihkan
                    $this->detailTagihans()->where('resep_detail_id', $rd->id)->delete();
                    continue;
                }

                $this->pastikanDetail('Obat', null, $rd->id, (float) $rd->obat->harga * $rd->jumlah);
            }
        }
    }

    private function pastikanDetail(string $jenis, ?int $kunjunganTindakanId, ?int $resepDetailId, float $biaya): void
    {
        $detail = $this->detailTagihans()
            ->where('jenis_biaya', $jenis)
            ->where('kunjungan_tindakan_id', $kunjunganTindakanId)
            ->where('resep_detail_id', $resepDetailId)
            ->first();

        if (! $detail) {
            $this->detailTagihans()->create([
                'jenis_biaya' => $jenis,
                'kunjungan_tindakan_id' => $kunjunganTindakanId,
                'resep_detail_id' => $resepDetailId,
                'jumlah_biaya' => $biaya,
                'ditanggung_asuransi' => 0,
                'ditanggung_pasien' => $biaya,
                'status_pembayaran' => 'Belum Ditentukan',
            ]);

            return;
        }

        $detail->update([
            'jumlah_biaya' => $biaya,
            'ditanggung_pasien' => max(0, $biaya - (float) $detail->ditanggung_asuransi),
        ]);
    }
}
