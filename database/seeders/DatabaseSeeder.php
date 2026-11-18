<?php

namespace Database\Seeders;

use App\Models\Diagnosis;
use App\Models\Dokter;
use App\Models\JadwalPraktik;
use App\Models\Obat;
use App\Models\Pasien;
use App\Models\PerusahaanAsuransi;
use App\Models\Tindakan;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Petugas Administrasi
        $petugas = User::updateOrCreate(
            ['email' => 'petugas@klinik.com'],
            [
                'name' => 'Siti Aminah (Petugas Admin)',
                'password' => Hash::make('password'),
                'peran' => User::PERAN_PETUGAS,
                'no_telp' => '081234567890',
            ]
        );

        // 2. Akun Dokter & Profil Dokter
        $userDokterUmum = User::updateOrCreate(
            ['email' => 'dokter@klinik.com'],
            [
                'name' => 'Budi Santoso, Sp.PD',
                'password' => Hash::make('password'),
                'peran' => User::PERAN_DOKTER,
                'no_telp' => '081298765432',
            ]
        );

        $dokterUmum = Dokter::updateOrCreate(
            ['user_id' => $userDokterUmum->id],
            ['spesialisasi' => 'Penyakit Dalam']
        );

        $userDokterAnak = User::updateOrCreate(
            ['email' => 'dokteranak@klinik.com'],
            [
                'name' => 'Rina Wijaya, Sp.A',
                'password' => Hash::make('password'),
                'peran' => User::PERAN_DOKTER,
                'no_telp' => '081277889900',
            ]
        );

        $dokterAnak = Dokter::updateOrCreate(
            ['user_id' => $userDokterAnak->id],
            ['spesialisasi' => 'Spesialis Anak']
        );

        // Jadwal Praktik Dokter untuk setiap hari (Senin s/d Minggu)
        $hariList = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'];
        foreach ($hariList as $hari) {
            JadwalPraktik::firstOrCreate(
                ['dokter_id' => $dokterUmum->id, 'hari' => $hari],
                ['jam_mulai' => '08:00', 'jam_selesai' => '14:00']
            );

            JadwalPraktik::firstOrCreate(
                ['dokter_id' => $dokterAnak->id, 'hari' => $hari],
                ['jam_mulai' => '09:00', 'jam_selesai' => '15:00']
            );
        }

        // 3. Akun Kasir
        $kasir = User::updateOrCreate(
            ['email' => 'kasir@klinik.com'],
            [
                'name' => 'Dewi Sartika (Kasir)',
                'password' => Hash::make('password'),
                'peran' => User::PERAN_KASIR,
                'no_telp' => '081344556677',
            ]
        );

        // 4. Perusahaan Asuransi Rekanan
        $bpjs = PerusahaanAsuransi::firstOrCreate(['nama_asuransi' => 'BPJS Kesehatan']);
        $prudential = PerusahaanAsuransi::firstOrCreate(['nama_asuransi' => 'Prudential Life Assurance']);
        $allianz = PerusahaanAsuransi::firstOrCreate(['nama_asuransi' => 'Allianz Life Indonesia']);

        // 5. Data Pasien Awal
        $pasien1 = Pasien::updateOrCreate(
            ['no_identitas' => '3271012304950001'],
            [
                'nama' => 'Ahmad Fauzi',
                'tgl_lahir' => '1995-04-23',
                'alamat' => 'Jl. Merdeka No. 45, Jakarta Selatan',
                'no_telp' => '085712345678',
            ]
        );

        // Daftarkan Asuransi untuk Ahmad Fauzi
        $pasien1->pasienAsuransis()->firstOrCreate(
            ['perusahaan_asuransi_id' => $bpjs->id],
            [
                'no_polis' => '00012938475',
                'prioritas' => 1,
                'masa_berlaku' => now()->addYears(2),
            ]
        );

        $pasien2 = Pasien::updateOrCreate(
            ['no_identitas' => '3271025508880002'],
            [
                'nama' => 'Siti Nurhaliza',
                'tgl_lahir' => '1988-08-15',
                'alamat' => 'Jl. Sudirman No. 12, Jakarta Pusat',
                'no_telp' => '081987654321',
            ]
        );

        // 6. Master Diagnosis ICD-10
        $diagnoses = [
            ['kode_diagnosis' => 'J00', 'nama_diagnosis' => 'Nasofaringitis Akut (Common Cold)'],
            ['kode_diagnosis' => 'A09', 'nama_diagnosis' => 'Gastroenteritis dan Kolitis Menular'],
            ['kode_diagnosis' => 'I10', 'nama_diagnosis' => 'Hipertensi Esensial (Primer)'],
            ['kode_diagnosis' => 'E11', 'nama_diagnosis' => 'Diabetes Melitus Tipe 2'],
            ['kode_diagnosis' => 'J06.9', 'nama_diagnosis' => 'Infeksi Saluran Pernapasan Atas Akut (ISPA)'],
            ['kode_diagnosis' => 'K29.7', 'nama_diagnosis' => 'Gastritis, Tidak Spesifik'],
        ];
        foreach ($diagnoses as $d) {
            Diagnosis::firstOrCreate(['kode_diagnosis' => $d['kode_diagnosis']], $d);
        }

        // 7. Master Tindakan Medis
        $tindakans = [
            ['kode_tindakan' => 'TND-01', 'nama_tindakan' => 'Pemeriksaan Tanda Vital Lengkap', 'tarif' => 15000],
            ['kode_tindakan' => 'TND-02', 'nama_tindakan' => 'Injeksi / Suntik Obat', 'tarif' => 25000],
            ['kode_tindakan' => 'TND-03', 'nama_tindakan' => 'Nebulisasi / Uap Saluran Napas', 'tarif' => 45000],
            ['kode_tindakan' => 'TND-04', 'nama_tindakan' => 'Perawatan Luka Ringan / Dressing', 'tarif' => 35000],
            ['kode_tindakan' => 'TND-05', 'nama_tindakan' => 'Pemeriksaan Gula Darah Sewaktu (GDS)', 'tarif' => 20000],
        ];
        foreach ($tindakans as $t) {
            Tindakan::firstOrCreate(['kode_tindakan' => $t['kode_tindakan']], $t);
        }

        // 8. Master Obat & Stok
        $obats = [
            ['kode_obat' => 'OBT-001', 'nama_obat' => 'Paracetamol 500mg Strip (10 Tablet)', 'stok' => 150, 'harga' => 10000],
            ['kode_obat' => 'OBT-002', 'nama_obat' => 'Amoxicillin 500mg Strip (10 Kapsul)', 'stok' => 80, 'harga' => 15000],
            ['kode_obat' => 'OBT-003', 'nama_obat' => 'Antasida Doen Strip (10 Tablet Kunyah)', 'stok' => 100, 'harga' => 8000],
            ['kode_obat' => 'OBT-004', 'nama_obat' => 'Cetirizine 10mg Strip (10 Tablet)', 'stok' => 120, 'harga' => 12000],
            ['kode_obat' => 'OBT-005', 'nama_obat' => 'Ambroxol Sirup 60ml', 'stok' => 50, 'harga' => 18000],
            ['kode_obat' => 'OBT-006', 'nama_obat' => 'Vitamin C 500mg Strip (10 Tablet)', 'stok' => 200, 'harga' => 9000],
        ];
        foreach ($obats as $o) {
            Obat::firstOrCreate(['kode_obat' => $o['kode_obat']], $o);
        }
    }
}
