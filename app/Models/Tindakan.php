<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Master tindakan beserta tarifnya. */
class Tindakan extends Model
{
    protected $table = 'tindakans';

    protected $fillable = ['kode_tindakan', 'nama_tindakan', 'tarif'];

    protected $casts = ['tarif' => 'decimal:2'];

    public function kunjunganTindakans(): HasMany
    {
        return $this->hasMany(KunjunganTindakan::class);
    }

    public static function tampil_tindakan()
    {
        return self::orderBy('nama_tindakan')->get();
    }

    public static function cari_tindakan(string $kata)
    {
        return self::where('nama_tindakan', 'like', "%{$kata}%")
            ->orWhere('kode_tindakan', 'like', "%{$kata}%")
            ->get();
    }
}
