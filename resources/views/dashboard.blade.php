<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Dashboard Interaktif Klinik') }}
                </h2>
                <p class="text-sm text-gray-500">Ringkasan aktivitas operasional, analitik antrian, dan performa keuangan.</p>
            </div>
            <div class="flex items-center gap-2">
                @if(Auth::user()->peran === 'kasir' || Auth::user()->peran === 'petugas_administrasi')
                    <a href="{{ route('kasir.laporan.pendapatan') }}" class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-xs rounded-lg shadow-sm transition">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                        Laporan Keuangan & Analitik &rarr;
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Banner Selamat Datang -->
        <div class="p-6 rounded-2xl bg-gradient-to-r from-teal-600 via-teal-700 to-emerald-700 text-white shadow-md flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold">Halo, {{ Auth::user()->name }}! 👋</h3>
                <p class="text-teal-100 text-sm mt-1">
                    Anda bertugas sebagai <span class="font-bold uppercase tracking-wider text-white underline">{{ str_replace('_', ' ', Auth::user()->peran) }}</span> di Sistem Klinik Sehat Bersama.
                </p>
            </div>
            <div class="flex flex-wrap gap-2 text-xs">
                @if(Auth::user()->peran === 'petugas_administrasi')
                    <a href="{{ route('petugas.pendaftaran.create') }}" class="px-3.5 py-2 bg-white text-teal-800 font-bold rounded-lg shadow-sm hover:bg-teal-50 transition">
                        + Daftar Kunjungan Baru
                    </a>
                    <a href="{{ route('petugas.pasien.create') }}" class="px-3.5 py-2 bg-teal-800/80 text-white font-medium rounded-lg hover:bg-teal-800 transition">
                        + Pasien Baru
                    </a>
                @elseif(Auth::user()->peran === 'dokter')
                    <a href="{{ route('dokter.pemeriksaan.antrian') }}" class="px-3.5 py-2 bg-white text-teal-800 font-bold rounded-lg shadow-sm hover:bg-teal-50 transition">
                        Buka Antrian Pasien Saya &rarr;
                    </a>
                @elseif(Auth::user()->peran === 'kasir')
                    <a href="{{ route('kasir.tagihan.index') }}" class="px-3.5 py-2 bg-white text-teal-800 font-bold rounded-lg shadow-sm hover:bg-teal-50 transition">
                        Buka Kasir Tagihan &rarr;
                    </a>
                @endif
            </div>
        </div>

        <!-- Kartu Metrik Utama -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase text-gray-400">Total Pasien Terdaftar</p>
                    <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ number_format($stats['total_pasien']) }}</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-teal-100 text-teal-600 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase text-gray-400">Kunjungan Hari Ini</p>
                    <p class="text-2xl font-bold text-gray-900 mt-0.5">{{ $stats['kunjungan_hari_ini'] }}</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase text-gray-400">Antrian Menunggu</p>
                    <p class="text-2xl font-bold text-amber-600 mt-0.5">{{ $stats['kunjungan_menunggu'] }}</p>
                </div>
            </div>

            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase text-gray-400">Penerimaan Kas Hari Ini</p>
                    <p class="text-xl font-bold text-emerald-700 mt-0.5 font-mono">Rp {{ number_format($stats['pendapatan_hari_ini'], 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom Kiri: Tren Kunjungan Pasien -->
            <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <div>
                        <h4 class="font-bold text-gray-800 text-base">Tren Kunjungan Pasien (7 Hari Terakhir)</h4>
                        <p class="text-xs text-gray-400">Volume pasien harian di klinik.</p>
                    </div>
                </div>

                <div class="grid grid-cols-7 gap-2 pt-4 items-end h-48">
                    @for ($i = 6; $i >= 0; $i--)
                        @php
                            $tgl = now()->subDays($i)->toDateString();
                            $total = $trenKunjungan[$tgl] ?? 0;
                            $tinggi = min(100, max(15, $total * 20));
                        @endphp
                        <div class="flex flex-col items-center h-full justify-end group">
                            <span class="text-xs font-bold text-teal-700 opacity-0 group-hover:opacity-100 transition mb-1">{{ $total }}</span>
                            <div class="w-full bg-teal-100 rounded-t-lg transition hover:bg-teal-500 relative flex items-center justify-center" style="height: {{ $tinggi }}%">
                                <span class="text-[11px] font-bold text-teal-900 group-hover:text-white">{{ $total }}</span>
                            </div>
                            <span class="text-[10px] text-gray-500 mt-2 font-mono whitespace-nowrap">{{ date('d/m', strtotime($tgl)) }}</span>
                        </div>
                    @endfor
                </div>
            </div>

            <!-- Kolom Kanan: Akses Cepat & Status Operasional -->
            <div class="space-y-6">
                <!-- Tagihan Terbuka Kasir -->
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-3">
                    <div class="flex items-center justify-between border-b border-gray-100 pb-2">
                        <h4 class="font-bold text-gray-800 text-sm">Tagihan Kasir Terbuka</h4>
                        <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-xs font-bold">{{ $stats['tagihan_terbuka'] }} Tagihan</span>
                    </div>

                    <div class="space-y-2 text-xs">
                        @forelse($tagihanTerbuka as $tt)
                            <a href="{{ route('kasir.tagihan.show', $tt) }}" class="p-2.5 rounded-lg border border-gray-100 hover:border-teal-300 hover:bg-teal-50/20 transition flex justify-between items-center block">
                                <div>
                                    <div class="font-bold text-gray-800">{{ $tt->kunjungan->pasien->nama }}</div>
                                    <div class="text-[11px] text-gray-400 font-mono">INV-{{ str_pad($tt->id, 5, '0', STR_PAD_LEFT) }} &bull; dr. {{ $tt->kunjungan->dokter->user->name }}</div>
                                </div>
                                <span class="text-teal-600 font-semibold hover:underline">Bayar &rarr;</span>
                            </a>
                        @empty
                            <p class="text-gray-400 text-center py-4">Semua tagihan lunas dan transaksi ditutup.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Tautan Pintas Berdasarkan Peran -->
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-3">
                    <h4 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-2">Pintasan Cepat</h4>
                    <div class="grid grid-cols-1 gap-2 text-xs">
                        @if(Auth::user()->peran === 'petugas_administrasi')
                            <a href="{{ route('petugas.pasien.index') }}" class="p-2 rounded-lg bg-gray-50 hover:bg-teal-50 text-gray-700 hover:text-teal-800 font-semibold transition">
                                &rarr; Kelola Data Pasien & NIK
                            </a>
                            <a href="{{ route('admin.dokter.index') }}" class="p-2 rounded-lg bg-gray-50 hover:bg-teal-50 text-gray-700 hover:text-teal-800 font-semibold transition">
                                &rarr; Atur Jadwal & Presensi Dokter
                            </a>
                            <a href="{{ route('admin.obat.index') }}" class="p-2 rounded-lg bg-gray-50 hover:bg-teal-50 text-gray-700 hover:text-teal-800 font-semibold transition">
                                &rarr; Master Obat & Stok Apotek
                            </a>
                        @elseif(Auth::user()->peran === 'dokter')
                            <a href="{{ route('dokter.pemeriksaan.antrian') }}" class="p-2 rounded-lg bg-gray-50 hover:bg-teal-50 text-gray-700 hover:text-teal-800 font-semibold transition">
                                &rarr; Ruang Antrian Pemeriksaan Pasien
                            </a>
                        @elseif(Auth::user()->peran === 'kasir')
                            <a href="{{ route('kasir.tagihan.index') }}" class="p-2 rounded-lg bg-gray-50 hover:bg-teal-50 text-gray-700 hover:text-teal-800 font-semibold transition">
                                &rarr; Pembayaran & Kasir Billing
                            </a>
                            <a href="{{ route('kasir.laporan.pendapatan') }}" class="p-2 rounded-lg bg-gray-50 hover:bg-teal-50 text-gray-700 hover:text-teal-800 font-semibold transition">
                                &rarr; Laporan Rekap Pendapatan Bulanan
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
