<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\ValidationException;

/** Master obat dan stoknya. */
class Obat extends Model
{
    protected $table = 'obats';

    protected $fillable = ['kode_obat', 'nama_obat', 'stok', 'harga'];

    protected $casts = ['harga' => 'decimal:2'];

    public function resepDetails(): HasMany
    {
        return $this->hasMany(ResepDetail::class);
    }

    public static function tampil_obat()
    {
        return self::orderBy('nama_obat')->get();
    }

    public function cek_stok(int $jumlah = 1): bool
    {
        return $this->stok >= $jumlah;
    }

    public function kurangi_stok(int $jumlah): void
    {
        $berhasil = self::whereKey($this->id)
            ->where('stok', '>=', $jumlah)
            ->decrement('stok', $jumlah);

        if (! $berhasil) {
            throw ValidationException::withMessages([
                'stok' => "Stok {$this->nama_obat} tidak mencukupi.",
            ]);
        }

        $this->refresh();
    }
}
