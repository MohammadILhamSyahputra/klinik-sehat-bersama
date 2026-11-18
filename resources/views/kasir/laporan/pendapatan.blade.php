<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Laporan Keuangan & Rekap Klaim Asuransi') }}
                </h2>
                <p class="text-sm text-gray-500">Analitik perolehan kas masuk dan monitoring klaim per periode bulan.</p>
            </div>
            <div class="flex items-center gap-2">
                <form method="GET" action="{{ route('kasir.laporan.pendapatan') }}" class="flex items-center gap-2">
                    <input type="month" name="bulan" value="{{ $bulan }}"
                        class="rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                    <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        Tampilkan Periode
                    </button>
                </form>
                <button onclick="window.print()" class="px-3.5 py-2 bg-teal-50 hover:bg-teal-100 text-teal-700 text-sm font-semibold rounded-lg shadow-sm transition inline-flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Laporan
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Kartu Metrik Keuangan Periode Ini -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold uppercase text-gray-400">Total Pendapatan Masuk</p>
                <p class="text-2xl font-black text-gray-900 mt-1 font-mono">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
                <p class="text-[11px] text-gray-400 mt-1">Periode: {{ date('F Y', strtotime($bulan . '-01')) }}</p>
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold uppercase text-emerald-600">Penerimaan Tunai (Cash)</p>
                <p class="text-2xl font-black text-emerald-700 mt-1 font-mono">Rp {{ number_format($pendapatanTunai, 0, ',', '.') }}</p>
                <p class="text-[11px] text-gray-400 mt-1">Pembayaran langsung di kasir</p>
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold uppercase text-blue-600">Penerimaan Klaim Asuransi</p>
                <p class="text-2xl font-black text-blue-700 mt-1 font-mono">Rp {{ number_format($pendapatanAsuransi, 0, ',', '.') }}</p>
                <p class="text-[11px] text-gray-400 mt-1">Klaim disetujui perusahaan penjamin</p>
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold uppercase text-purple-600">Transfer & Kartu Debit/Kredit</p>
                <p class="text-2xl font-black text-purple-700 mt-1 font-mono">Rp {{ number_format($pendapatanNonTunai, 0, ',', '.') }}</p>
                <p class="text-[11px] text-gray-400 mt-1">Non-tunai bank / EDC</p>
            </div>
        </div>

        <!-- 1. TABEL DETAIL TRANSAKSI PEMBAYARAN -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden space-y-4 p-6">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div>
                    <h3 class="font-bold text-gray-800 text-base">Riwayat Transaksi Pembayaran Masuk</h3>
                    <p class="text-xs text-gray-400">Penerimaan uang kasir per invoice tagihan pasien.</p>
                </div>
                <span class="text-xs font-semibold text-gray-500">{{ $pembayarans->count() }} Transaksi</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-200 text-gray-600 font-semibold uppercase">
                            <th class="py-3 px-3">Waktu Bayar</th>
                            <th class="py-3 px-3">No. Tagihan</th>
                            <th class="py-3 px-3">Nama Pasien</th>
                            <th class="py-3 px-3">Metode Bayar</th>
                            <th class="py-3 px-3">Kasir Petugas</th>
                            <th class="py-3 px-3 text-right">Jumlah Dibayar (Rp)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($pembayarans as $p)
                            <tr class="hover:bg-teal-50/20 transition">
                                <td class="py-2.5 px-3 font-mono text-gray-500">
                                    {{ $p->tgl_bayar->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-2.5 px-3 font-mono font-bold text-teal-700">
                                    INV-{{ str_pad($p->tagihan_id, 5, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="py-2.5 px-3 font-semibold text-gray-900">
                                    {{ $p->tagihan->kunjungan->pasien->nama }}
                                </td>
                                <td class="py-2.5 px-3">
                                    <span class="px-2 py-0.5 rounded font-semibold text-[11px]
                                        @if($p->metode === 'Tunai') bg-emerald-100 text-emerald-800
                                        @elseif($p->metode === 'Asuransi') bg-blue-100 text-blue-800
                                        @else bg-purple-100 text-purple-800 @endif">
                                        {{ $p->metode }}
                                        @if($p->klaimAsuransi)
                                            ({{ $p->klaimAsuransi->pasienAsuransi->perusahaanAsuransi->nama_asuransi }})
                                        @endif
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-gray-500">
                                    {{ $p->kasir->name }}
                                </td>
                                <td class="py-2.5 px-3 text-right font-mono font-bold text-gray-900">
                                    Rp {{ number_format($p->jumlah, 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-400">
                                    Tidak ada transaksi penerimaan kas pada periode bulan ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 border-gray-200 font-bold text-sm text-gray-900 bg-gray-50/50">
                            <td colspan="5" class="py-3 px-3 text-right">TOTAL PENDAPATAN BULAN {{ strtoupper(date('F Y', strtotime($bulan . '-01'))) }}:</td>
                            <td class="py-3 px-3 text-right font-mono font-black text-teal-800">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- 2. TABEL REKAPITULASI KLAIM ASURANSI -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden space-y-4 p-6">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <div>
                    <h3 class="font-bold text-gray-800 text-base">Monitoring Rekap Klaim Asuransi</h3>
                    <p class="text-xs text-gray-400">Status berkas klaim yang diajukan ke perusahaan mitra asuransi.</p>
                </div>
                <span class="text-xs font-semibold text-gray-500">{{ $rekapKlaim->count() }} Berkas Klaim</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-200 text-gray-600 font-semibold uppercase">
                            <th class="py-3 px-3">No. Klaim</th>
                            <th class="py-3 px-3">Tanggal Pengajuan</th>
                            <th class="py-3 px-3">Mitra Asuransi</th>
                            <th class="py-3 px-3">Nama Pasien & Polis</th>
                            <th class="py-3 px-3 text-center">Status Keputusan</th>
                            <th class="py-3 px-3 text-center">Detail Tagihan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse($rekapKlaim as $k)
                            <tr class="hover:bg-blue-50/20 transition">
                                <td class="py-2.5 px-3 font-mono font-bold text-blue-700">
                                    {{ $k->no_klaim }}
                                </td>
                                <td class="py-2.5 px-3 text-gray-500">
                                    {{ $k->tgl_pengajuan->format('d/m/Y') }}
                                </td>
                                <td class="py-2.5 px-3 font-semibold text-gray-900">
                                    {{ $k->pasienAsuransi->perusahaanAsuransi->nama_asuransi }}
                                </td>
                                <td class="py-2.5 px-3">
                                    <div class="font-medium text-gray-800">{{ $k->tagihan->kunjungan->pasien->nama }}</div>
                                    <div class="text-[11px] text-gray-400 font-mono">Polis: {{ $k->pasienAsuransi->no_polis }}</div>
                                </td>
                                <td class="py-2.5 px-3 text-center">
                                    <span class="px-2.5 py-0.5 rounded-full font-bold text-[11px]
                                        @if($k->status === 'Disetujui') bg-emerald-100 text-emerald-800
                                        @elseif($k->status === 'Ditolak') bg-rose-100 text-rose-800
                                        @elseif($k->status === 'Sebagian') bg-amber-100 text-amber-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ $k->status }}
                                    </span>
                                </td>
                                <td class="py-2.5 px-3 text-center">
                                    <a href="{{ route('kasir.tagihan.show', $k->tagihan_id) }}" class="text-teal-600 hover:underline font-semibold">
                                        INV-{{ str_pad($k->tagihan_id, 5, '0', STR_PAD_LEFT) }} &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-400">
                                    Belum ada pengajuan klaim asuransi pada periode ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

