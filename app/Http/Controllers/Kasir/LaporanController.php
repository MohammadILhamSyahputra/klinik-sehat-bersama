<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\KlaimAsuransi;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function pendapatan(Request $request): View
    {
        $bulan = $request->input('bulan', now()->format('Y-m'));
        $startDate = $bulan . '-01';
        $endDate = date('Y-m-t', strtotime($startDate));

        $pembayarans = Pembayaran::with(['tagihan.kunjungan.pasien', 'kasir', 'klaimAsuransi.pasienAsuransi.perusahaanAsuransi'])
            ->whereDate('tgl_bayar', '>=', $startDate)
            ->whereDate('tgl_bayar', '<=', $endDate)
            ->latest('tgl_bayar')
            ->get();

        $totalPendapatan = (float) $pembayarans->sum('jumlah');
        $pendapatanTunai = (float) $pembayarans->where('metode', 'Tunai')->sum('jumlah');
        $pendapatanAsuransi = (float) $pembayarans->where('metode', 'Asuransi')->sum('jumlah');
        $pendapatanNonTunai = (float) $pembayarans->whereNotIn('metode', ['Tunai', 'Asuransi'])->sum('jumlah');

        // Ringkasan per metode
        $ringkasanMetode = $pembayarans->groupBy('metode')->map(function ($items, $metode) {
            return [
                'metode' => $metode,
                'total' => (float) $items->sum('jumlah'),
                'transaksi' => $items->count(),
            ];
        });

        // Rekap klaim asuransi pada periode ini
        $rekapKlaim = KlaimAsuransi::with(['pasienAsuransi.perusahaanAsuransi', 'tagihan.kunjungan.pasien'])
            ->whereDate('tgl_pengajuan', '>=', $startDate)
            ->whereDate('tgl_pengajuan', '<=', $endDate)
            ->latest()
            ->get();

        return view('kasir.laporan.pendapatan', compact(
            'pembayarans',
            'bulan',
            'totalPendapatan',
            'pendapatanTunai',
            'pendapatanAsuransi',
            'pendapatanNonTunai',
            'ringkasanMetode',
            'rekapKlaim'
        ));
    }
}

