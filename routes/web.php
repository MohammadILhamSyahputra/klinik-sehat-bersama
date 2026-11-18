<?php

use App\Http\Controllers\Admin\DiagnosisController;
use App\Http\Controllers\Admin\DokterController;
use App\Http\Controllers\Admin\ObatController;
use App\Http\Controllers\Admin\PerusahaanAsuransiController;
use App\Http\Controllers\Admin\TindakanController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Dokter\PemeriksaanController;
use App\Http\Controllers\Kasir\TagihanController;
use App\Http\Controllers\Petugas\PasienAsuransiController;
use App\Http\Controllers\Petugas\PasienController;
use App\Http\Controllers\Petugas\PendaftaranController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // ==========================================
    // AREA PETUGAS ADMINISTRASI
    // ==========================================
    Route::middleware('role:petugas_administrasi')->prefix('petugas')->name('petugas.')->group(function () {
        // Pasien CRUD
        Route::resource('pasien', PasienController::class);

        // Pasien Asuransi
        Route::get('pasien/{pasien}/asuransi/create', [PasienAsuransiController::class, 'create'])->name('pasien.asuransi.create');
        Route::post('pasien/{pasien}/asuransi', [PasienAsuransiController::class, 'store'])->name('pasien.asuransi.store');
        Route::get('pasien/{pasien}/asuransi/{asuransi}/edit', [PasienAsuransiController::class, 'edit'])->name('pasien.asuransi.edit');
        Route::put('pasien/{pasien}/asuransi/{asuransi}', [PasienAsuransiController::class, 'update'])->name('pasien.asuransi.update');
        Route::delete('pasien/{pasien}/asuransi/{asuransi}', [PasienAsuransiController::class, 'destroy'])->name('pasien.asuransi.destroy');

        // Pendaftaran Kunjungan & Antrian
        Route::get('pendaftaran', [PendaftaranController::class, 'index'])->name('pendaftaran.index');
        Route::get('pendaftaran/create', [PendaftaranController::class, 'create'])->name('pendaftaran.create');
        Route::post('pendaftaran', [PendaftaranController::class, 'store'])->name('pendaftaran.store');
        Route::get('pendaftaran/{kunjungan}', [PendaftaranController::class, 'show'])->name('pendaftaran.show');
        Route::post('pendaftaran/{kunjungan}/batal', [PendaftaranController::class, 'batal'])->name('pendaftaran.batal');
        Route::get('pendaftaran/{kunjungan}/pindah-dokter', [PendaftaranController::class, 'pemindahanForm'])->name('pendaftaran.pindah-dokter.form');
        Route::post('pendaftaran/{kunjungan}/pindah-dokter', [PendaftaranController::class, 'pindahDokter'])->name('pendaftaran.pindah-dokter');
    });

    // ==========================================
    // AREA DOKTER
    // ==========================================
    Route::middleware('role:dokter')->prefix('dokter')->name('dokter.')->group(function () {
        Route::get('antrian', [PemeriksaanController::class, 'antrian'])->name('pemeriksaan.antrian');
        Route::post('kunjungan/{kunjungan}/panggil', [PemeriksaanController::class, 'panggil'])->name('pemeriksaan.panggil');
        Route::get('kunjungan/{kunjungan}/periksa', [PemeriksaanController::class, 'periksa'])->name('pemeriksaan.periksa');
        Route::post('kunjungan/{kunjungan}/catatan', [PemeriksaanController::class, 'simpanCatatan'])->name('pemeriksaan.catatan');
        Route::post('kunjungan/{kunjungan}/diagnosis', [PemeriksaanController::class, 'tambahDiagnosis'])->name('pemeriksaan.diagnosis.store');
        Route::put('kunjungan/{kunjungan}/diagnosis/{kunjunganDiagnosis}', [PemeriksaanController::class, 'ubahDiagnosis'])->name('pemeriksaan.diagnosis.update');
        Route::delete('kunjungan/{kunjungan}/diagnosis/{kunjunganDiagnosis}', [PemeriksaanController::class, 'hapusDiagnosis'])->name('pemeriksaan.diagnosis.destroy');
        Route::post('kunjungan/{kunjungan}/tindakan', [PemeriksaanController::class, 'tambahTindakan'])->name('pemeriksaan.tindakan.store');
        Route::delete('kunjungan/{kunjungan}/tindakan/{tindakanId}', [PemeriksaanController::class, 'hapusTindakan'])->name('pemeriksaan.tindakan.destroy');
        Route::post('kunjungan/{kunjungan}/obat', [PemeriksaanController::class, 'tambahObat'])->name('pemeriksaan.obat.store');
        Route::delete('kunjungan/{kunjungan}/obat/{resepDetail}', [PemeriksaanController::class, 'hapusObat'])->name('pemeriksaan.obat.destroy');
        Route::post('kunjungan/{kunjungan}/selesai', [PemeriksaanController::class, 'selesaiPemeriksaan'])->name('pemeriksaan.selesai');
    });

    // ==========================================
    // AREA KASIR
    // ==========================================
    Route::middleware('role:kasir')->prefix('kasir')->name('kasir.')->group(function () {
        Route::get('tagihan', [TagihanController::class, 'index'])->name('tagihan.index');
        Route::get('tagihan/{tagihan}', [TagihanController::class, 'show'])->name('tagihan.show');
        Route::post('tagihan/{tagihan}/tebus-obat/{resepDetail}', [TagihanController::class, 'tebusObat'])->name('tagihan.tebus-obat');
        Route::post('tagihan/{tagihan}/ajukan-klaim', [TagihanController::class, 'ajukanKlaim'])->name('tagihan.ajukan-klaim');
        Route::post('tagihan/{tagihan}/hasil-klaim/{klaim}', [TagihanController::class, 'prosesHasilKlaim'])->name('tagihan.hasil-klaim');
        Route::post('tagihan/{tagihan}/bayar', [TagihanController::class, 'bayar'])->name('tagihan.bayar');
        Route::post('tagihan/{tagihan}/tutup', [TagihanController::class, 'tutup'])->name('tagihan.tutup');
    });

    // ==========================================
    // AREA MASTER DATA (PETUGAS / ADMIN)
    // ==========================================
    Route::middleware('role:petugas_administrasi')->prefix('admin')->name('admin.')->group(function () {
        // Dokter & Jadwal & Kehadiran
        Route::resource('dokter', DokterController::class);
        Route::post('dokter/{dokter}/jadwal', [DokterController::class, 'storeJadwal'])->name('dokter.jadwal.store');
        Route::delete('dokter/{dokter}/jadwal/{jadwal}', [DokterController::class, 'destroyJadwal'])->name('dokter.jadwal.destroy');
        Route::post('dokter/{dokter}/kehadiran', [DokterController::class, 'setKehadiran'])->name('dokter.kehadiran');

        // Master Data Lainnya
        Route::resource('obat', ObatController::class)->except(['show']);
        Route::resource('tindakan', TindakanController::class)->except(['show']);
        Route::resource('diagnosis', DiagnosisController::class)->except(['show']);
        Route::resource('asuransi', PerusahaanAsuransiController::class)->except(['show']);
    });
});

require __DIR__.'/auth.php';
