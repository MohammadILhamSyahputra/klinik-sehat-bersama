<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pengaturan Biaya Klinik Sehat Bersama
    |--------------------------------------------------------------------------
    |
    | Pengaturan biaya standar pendaftaran dan pemeriksaan awal dokter.
    | Digunakan saat pembuatan tagihan kunjungan pasien.
    |
    */

    'biaya_pendaftaran' => env('KLINIK_BIAYA_PENDAFTARAN', 20000),
    'biaya_pemeriksaan' => env('KLINIK_BIAYA_PEMERIKSAAN', 50000),
];

