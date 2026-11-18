<x-app-layout>
    {{-- =====================================================================
         STYLE CETAK
         - Tampilan layar (aplikasi) tidak berubah.
         - Saat dicetak / Save as PDF: menu, tombol, dan kartu aplikasi disembunyikan,
           lalu yang tampil hanya blok ".print-only" berformat laporan resmi.
         ===================================================================== --}}
    <style>
        .print-only { display: none; }

        @media print {
            @page {
                size: A4 portrait;
                margin: 16mm 16mm 20mm 16mm;

                @bottom-right {
                    content: "Halaman " counter(page) " dari " counter(pages);
                    font: 9pt "Times New Roman", Times, serif;
                    color: #000;
                }
            }

            html, body, .min-h-screen { background: #fff !important; }
            nav, header, .no-print { display: none !important; }
            main { padding: 0 !important; margin: 0 !important; }
            .print-only { display: block !important; }
        }

        /* ---- Gaya laporan resmi ---- */
        .lap { font-family: "Times New Roman", Times, serif; color: #000; font-size: 11pt; line-height: 1.35; }
        .lap * { box-sizing: border-box; }

        .lap-kop { text-align: center; border-bottom: 3px double #000; padding-bottom: 8px; margin-bottom: 14px; }
        .lap-kop .nama { font-size: 18pt; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; }
        .lap-kop .sub { font-size: 10pt; margin-top: 2px; }

        .lap-judul { text-align: center; margin: 10px 0 14px; }
        .lap-judul .judul { font-size: 13pt; font-weight: 700; text-decoration: underline; text-transform: uppercase; }
        .lap-judul .periode { font-size: 11pt; margin-top: 3px; }

        .lap-bagian { font-size: 11.5pt; font-weight: 700; margin: 16px 0 6px; }

        .lap table { width: 100%; border-collapse: collapse; font-size: 9.5pt; margin-bottom: 6px; }
        .lap th, .lap td { border: 1px solid #000; padding: 4px 6px; vertical-align: top; }
        .lap th {
            background: #e5e5e5 !important;
            text-align: center;
            font-weight: 700;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .lap td.c { text-align: center; }
        .lap td.r { text-align: right; white-space: nowrap; }
        .lap td.kosong { text-align: center; font-style: italic; padding: 10px; }
        .lap tfoot td { font-weight: 700; background: #f2f2f2 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        .lap thead { display: table-header-group; }
        .lap tr { break-inside: avoid; page-break-inside: avoid; }

        .lap-catatan { font-size: 9.5pt; margin: 4px 0 0; }

        .lap-ttd { display: flex; justify-content: space-between; margin-top: 30px; break-inside: avoid; page-break-inside: avoid; }
        .lap-ttd > div { width: 42%; text-align: center; font-size: 10.5pt; }
        .lap-ttd .ruang { height: 64px; }
        .lap-ttd .nama-ttd { font-weight: 700; text-decoration: underline; }

        .lap-cetak { margin-top: 18px; font-size: 8.5pt; color: #444; }
    </style>

    @php
        $periodeLabel = \Carbon\Carbon::parse($bulan . '-01')->locale('id')->translatedFormat('F Y');
    @endphp

    <x-slot name="header">
        <div class="no-print flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
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

    {{-- =====================================================================
         TAMPILAN LAYAR (aplikasi) - disembunyikan saat dicetak
         ===================================================================== --}}
    <div class="no-print py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Kartu Metrik Keuangan Periode Ini -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
                <p class="text-xs font-semibold uppercase text-gray-400">Total Pendapatan Masuk</p>
                <p class="text-2xl font-black text-gray-900 mt-1 font-mono">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
                <p class="text-[11px] text-gray-400 mt-1">Periode: {{ $periodeLabel }}</p>
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
                            <td colspan="5" class="py-3 px-3 text-right">TOTAL PENDAPATAN BULAN {{ strtoupper($periodeLabel) }}:</td>
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

    {{-- =====================================================================
         TAMPILAN CETAK / PDF - hanya muncul saat dicetak
         ===================================================================== --}}
    <div class="print-only lap">

        {{-- Kop laporan --}}
        <div class="lap-kop">
            <div class="nama">Klinik Sehat Bersama</div>
            @if(config('klinik.alamat'))
                <div class="sub">{{ config('klinik.alamat') }}</div>
            @endif
            @if(config('klinik.kontak'))
                <div class="sub">{{ config('klinik.kontak') }}</div>
            @endif
        </div>

        {{-- Judul --}}
        <div class="lap-judul">
            <div class="judul">Laporan Keuangan dan Rekapitulasi Klaim Asuransi</div>
            <div class="periode">Periode: {{ $periodeLabel }}</div>
        </div>

        {{-- A. Ringkasan --}}
        <div class="lap-bagian">A. Ringkasan Penerimaan</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 8%;">No.</th>
                    <th>Keterangan</th>
                    <th style="width: 28%;">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="c">1</td>
                    <td>Penerimaan tunai (pembayaran langsung di kasir)</td>
                    <td class="r">{{ number_format($pendapatanTunai, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="c">2</td>
                    <td>Penerimaan klaim asuransi (disetujui perusahaan penjamin)</td>
                    <td class="r">{{ number_format($pendapatanAsuransi, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="c">3</td>
                    <td>Transfer dan kartu debit/kredit</td>
                    <td class="r">{{ number_format($pendapatanNonTunai, 0, ',', '.') }}</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="r">Total Pendapatan</td>
                    <td class="r">{{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- B. Rincian transaksi --}}
        <div class="lap-bagian">B. Rincian Transaksi Pembayaran</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 14%;">Waktu Bayar</th>
                    <th style="width: 11%;">No. Tagihan</th>
                    <th>Nama Pasien</th>
                    <th style="width: 17%;">Metode Bayar</th>
                    <th style="width: 14%;">Kasir</th>
                    <th style="width: 14%;">Jumlah (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pembayarans as $p)
                    <tr>
                        <td class="c">{{ $loop->iteration }}</td>
                        <td class="c">{{ $p->tgl_bayar->format('d/m/Y H:i') }}</td>
                        <td class="c">INV-{{ str_pad($p->tagihan_id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $p->tagihan->kunjungan->pasien->nama }}</td>
                        <td>
                            {{ $p->metode }}
                            @if($p->klaimAsuransi)
                                ({{ $p->klaimAsuransi->pasienAsuransi->perusahaanAsuransi->nama_asuransi }})
                            @endif
                        </td>
                        <td>{{ $p->kasir->name }}</td>
                        <td class="r">{{ number_format($p->jumlah, 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="kosong">Tidak ada transaksi penerimaan kas pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="r">Total Pendapatan {{ $periodeLabel }}</td>
                    <td class="r">{{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
        <p class="lap-catatan">Jumlah transaksi: {{ $pembayarans->count() }}</p>

        {{-- C. Rekap klaim --}}
        <div class="lap-bagian">C. Rekapitulasi Klaim Asuransi</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">No.</th>
                    <th style="width: 17%;">No. Klaim</th>
                    <th style="width: 12%;">Tgl. Pengajuan</th>
                    <th style="width: 16%;">Mitra Asuransi</th>
                    <th>Pasien / No. Polis</th>
                    <th style="width: 11%;">Status</th>
                    <th style="width: 11%;">No. Tagihan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($rekapKlaim as $k)
                    <tr>
                        <td class="c">{{ $loop->iteration }}</td>
                        <td>{{ $k->no_klaim }}</td>
                        <td class="c">{{ $k->tgl_pengajuan->format('d/m/Y') }}</td>
                        <td>{{ $k->pasienAsuransi->perusahaanAsuransi->nama_asuransi }}</td>
                        <td>
                            {{ $k->tagihan->kunjungan->pasien->nama }}<br>
                            <span style="font-size: 8.5pt;">Polis: {{ $k->pasienAsuransi->no_polis }}</span>
                        </td>
                        <td class="c">{{ $k->status }}</td>
                        <td class="c">INV-{{ str_pad($k->tagihan_id, 5, '0', STR_PAD_LEFT) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="kosong">Belum ada pengajuan klaim asuransi pada periode ini.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <p class="lap-catatan">
            Jumlah berkas klaim: {{ $rekapKlaim->count() }}
            (Disetujui: {{ $rekapKlaim->where('status', 'Disetujui')->count() }},
            Sebagian: {{ $rekapKlaim->where('status', 'Sebagian')->count() }},
            Ditolak: {{ $rekapKlaim->where('status', 'Ditolak')->count() }},
            Diajukan: {{ $rekapKlaim->where('status', 'Diajukan')->count() }})
        </p>

        {{-- Tanda tangan --}}
        <div class="lap-ttd">
            <div>
                Mengetahui,<br>
                Pimpinan Klinik
                <div class="ruang"></div>
                <span class="nama-ttd">( ............................. )</span>
            </div>
            <div>
                Dibuat oleh,<br>
                Petugas Administrasi
                <div class="ruang"></div>
                <span class="nama-ttd">{{ auth()->user()->name }}</span>
            </div>
        </div>

        <div class="lap-cetak">
            Dicetak pada {{ now()->locale('id')->translatedFormat('d F Y, H:i') }} melalui Sistem Informasi Klinik Sehat Bersama.
        </div>
    </div>
</x-app-layout>