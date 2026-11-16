<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Pengguna (superclass di class diagram).
 * PetugasAdministrasi, Dokter, dan Kasir dibedakan lewat kolom "peran".
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    public const PERAN_PETUGAS = 'petugas_administrasi';
    public const PERAN_DOKTER = 'dokter';
    public const PERAN_KASIR = 'kasir';

    protected $fillable = [
        'name',
        'email',
        'password',
        'no_telp',
        'peran',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ---- relasi ----
    public function dokter(): HasOne
    {
        return $this->hasOne(Dokter::class);
    }

    public function kunjunganDidaftarkan(): HasMany
    {
        return $this->hasMany(Kunjungan::class, 'petugas_id');
    }

    public function tagihanDitutup(): HasMany
    {
        return $this->hasMany(Tagihan::class, 'kasir_id');
    }

    public function pembayaranDiterima(): HasMany
    {
        return $this->hasMany(Pembayaran::class, 'kasir_id');
    }

    // ---- operasi (class Pengguna) ----
    public function get_nama(): string
    {
        return $this->name;
    }

    public function adalahPetugasAdministrasi(): bool
    {
        return $this->peran === self::PERAN_PETUGAS;
    }

    public function adalahDokter(): bool
    {
        return $this->peran === self::PERAN_DOKTER;
    }

    public function adalahKasir(): bool
    {
        return $this->peran === self::PERAN_KASIR;
    }
}
