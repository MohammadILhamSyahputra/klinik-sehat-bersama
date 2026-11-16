<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Isi resep. Obat yang stoknya habis tetap boleh disimpan (tersedia = false).
 */
class ResepDetail extends Model
{
    protected $table = 'resep_details';

    protected $fillable = ['resep_id', 'obat_id', 'jumlah', 'aturan_pakai', 'tersedia', 'status_tebus'];

    protected $casts = ['tersedia' => 'boolean'];

    public function resep(): BelongsTo
    {
        return $this->belongsTo(Resep::class);
    }

    public function obat(): BelongsTo
    {
        return $this->belongsTo(Obat::class);
    }

    public function detailTagihan(): HasOne
    {
        return $this->hasOne(DetailTagihan::class);
    }

    /** Menambah obat ke resep, lalu mengecek stoknya (resep tetap tersimpan walau stok habis). */
    public static function tambah_obat(Resep $resep, Obat $obat, int $jumlah, string $aturanPakai): self
    {
        $detail = self::create([
            'resep_id' => $resep->id,
            'obat_id' => $obat->id,
            'jumlah' => $jumlah,
            'aturan_pakai' => $aturanPakai,
            'tersedia' => false,
            'status_tebus' => 'Belum',
        ]);

        $detail->cek_stok();

        return $detail;
    }

    /** Mengembalikan false jika stok habis; pemanggil menampilkan peringatan ke dokter. */
    public function cek_stok(): bool
    {
        $ada = $this->obat->cek_stok($this->jumlah);

        if ($ada) {
            $this->tandai_tersedia();
        } else {
            $this->update(['tersedia' => false]);
        }

        return $ada;
    }

    public function ubah_status_tebus(string $status): void
    {
        if (! in_array($status, ['Belum', 'Sudah', 'Tidak Diserahkan'], true)) {
            throw new \InvalidArgumentException('Status tebus tidak valid.');
        }

        $this->update(['status_tebus' => $status]);
    }

    private function tandai_tersedia(): void
    {
        $this->update(['tersedia' => true]);
    }
}
