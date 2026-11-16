<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Resep bergantung pada Kunjungan (komposisi 1 ke 0..1).
 * status_tebus adalah atribut turunan (tidak disimpan di tabel).
 */
class Resep extends Model
{
    protected $table = 'reseps';

    protected $fillable = ['kunjungan_id', 'tanggal_resep'];

    protected $casts = ['tanggal_resep' => 'date'];

    public function kunjungan(): BelongsTo
    {
        return $this->belongsTo(Kunjungan::class);
    }

    public function resepDetails(): HasMany
    {
        return $this->hasMany(ResepDetail::class);
    }

    /** Atribut turunan: $resep->status_tebus */
    protected function statusTebus(): Attribute
    {
        return Attribute::get(fn () => $this->hitung_status_tebus());
    }

    public static function buat_resep(Kunjungan $kunjungan): self
    {
        return self::firstOrCreate(
            ['kunjungan_id' => $kunjungan->id],
            ['tanggal_resep' => today()]
        );
    }

    public function simpan_resep(): self
    {
        $this->save();

        return $this;
    }

    private function hitung_status_tebus(): string
    {
        $total = $this->resepDetails()->count();
        $sudah = $this->resepDetails()->where('status_tebus', 'Sudah')->count();

        if ($total === 0 || $sudah === 0) {
            return 'Belum';
        }

        return $sudah === $total ? 'Sudah' : 'Sebagian';
    }
}
