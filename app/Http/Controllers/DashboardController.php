<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use App\Models\KlaimAsuransi;
use App\Models\Kunjungan;
use App\Models\Pasien;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();
        $today = today()->toDateString();

        $stats = [
            'total_pasien' => Pasien::count(),
            'kunjungan_hari_ini' => Kunjungan::whereDate('tgl_kunjungan', $today)->count(),
            'kunjungan_menunggu' => Kunjungan::whereDate('tgl_kunjungan', $today)->where('status', 'Terdaftar')->count(),
            'kunjungan_selesai' => Kunjungan::whereDate('tgl_kunjungan', $today)->where('status', 'Selesai')->count(),
            'tagihan_terbuka' => Tagihan::where('status', 'Terbuka')->count(),
            'pendapatan_hari_ini' => (float) Pembayaran::whereDate('tgl_bayar', $today)->sum('jumlah'),
            'total_klaim_bulan_ini' => KlaimAsuransi::whereMonth('tgl_pengajuan', now()->month)->count(),
        ];

        // Tren Kunjungan 7 Hari Terakhir
        $trenKunjungan = Kunjungan::select(DB::raw('DATE(tgl_kunjungan) as tanggal'), DB::raw('count(*) as total'))
            ->where('tgl_kunjungan', '>=', now()->subDays(6)->toDateString())
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->pluck('total', 'tanggal');

        // Antrian Dokter Khusus Dokter yang Sedang Login
        $antrianDokter = null;
        if ($user->peran === 'dokter' && $user->dokter) {
            $antrianDokter = Kunjungan::with('pasien')
                ->where('dokter_id', $user->dokter->id)
                ->whereDate('tgl_kunjungan', $today)
                ->orderBy('no_antrian')
                ->get();
        }

        // Kunjungan Terbaru untuk Petugas
        $kunjunganTerbaru = Kunjungan::with(['pasien', 'dokter.user'])
            ->whereDate('tgl_kunjungan', $today)
            ->latest()
            ->limit(5)
            ->get();

        // Tagihan Terbuka untuk Kasir
        $tagihanTerbuka = Tagihan::with(['kunjungan.pasien', 'kunjungan.dokter.user'])
            ->where('status', 'Terbuka')
            ->latest()
            ->limit(5)
            ->get();

        return view('dashboard', compact(
            'stats',
            'trenKunjungan',
            'antrianDokter',
            'kunjunganTerbaru',
            'tagihanTerbuka'
        ));
    }
}
