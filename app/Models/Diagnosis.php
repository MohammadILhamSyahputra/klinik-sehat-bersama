<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/** Master diagnosis. */
class Diagnosis extends Model
{
    protected $table = 'diagnoses';

    protected $fillable = ['kode_diagnosis', 'nama_diagnosis'];

    public function kunjunganDiagnoses(): HasMany
    {
        return $this->hasMany(KunjunganDiagnosis::class);
    }

    public static function tampil_diagnosis()
    {
        return self::orderBy('nama_diagnosis')->get();
    }

    public static function cari_diagnosis(string $kata)
    {
        return self::where('nama_diagnosis', 'like', "%{$kata}%")
            ->orWhere('kode_diagnosis', 'like', "%{$kata}%")
            ->get();
    }
}
