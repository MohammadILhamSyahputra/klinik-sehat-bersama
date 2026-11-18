<?php

namespace App\Http\Controllers;

use App\Models\Dokter;
use App\Models\Kunjungan;
use App\Models\Pasien;
use App\Models\Tagihan;
use Illuminate\Http\Request;
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
        ];

        // Data khusus dokter jika login sebagai dokter
        $antrianDokter = null;
        if ($user->peran === 'dokter' && $user->dokter) {
            $antrianDokter = Kunjungan::with('pasien')
                ->where('dokter_id', $user->dokter->id)
                ->whereDate('tgl_kunjungan', $today)
                ->orderBy('no_antrian')
                ->get();
        }

        return view('dashboard', compact('stats', 'antrianDokter'));
    }
}

