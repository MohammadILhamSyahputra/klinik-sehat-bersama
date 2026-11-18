<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Banner Selamat Datang -->
        <div class="p-6 rounded-2xl bg-gradient-to-r from-teal-600 to-emerald-600 text-white shadow-md flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h3 class="text-xl font-bold">Selamat Datang, {{ Auth::user()->name }}! 👋</h3>
                <p class="text-teal-100 text-sm mt-1">
                    Anda masuk sebagai <span class="font-semibold uppercase tracking-wide text-white">{{ str_replace('_', ' ', Auth::user()->peran) }}</span> di Sistem Klinik Sehat Bersama.
                </p>
            </div>
            @if(Auth::user()->peran === 'petugas_administrasi')
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('petugas.pendaftaran.create') }}" class="px-4 py-2 bg-white text-teal-800 hover:bg-teal-50 font-semibold rounded-lg text-sm shadow-sm transition">
                        + Daftar Kunjungan Pasien
                    </a>
                    <a href="{{ route('petugas.pasien.create') }}" class="px-4 py-2 bg-teal-700/60 hover:bg-teal-700 text-white font-medium rounded-lg text-sm transition">
                        + Pasien Baru
                    </a>
                </div>
            @endif
        </div>

        <!-- Kartu Statistik -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center space-x-4">
                <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase text-gray-400">Total Pasien</p>
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
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold uppercase text-gray-400">Pemeriksaan Selesai</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-0.5">{{ $stats['kunjungan_selesai'] }}</p>
                </div>
            </div>
        </div>

        @if(Auth::user()->peran === 'petugas_administrasi')
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <h4 class="font-bold text-gray-800 text-base">Akses Cepat Petugas Administrasi</h4>
                    <a href="{{ route('petugas.pendaftaran.index') }}" class="text-xs text-teal-600 font-semibold hover:underline">
                        Lihat Semua Antrian &rarr;
                    </a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <a href="{{ route('petugas.pasien.index') }}" class="p-4 rounded-xl border border-gray-100 hover:border-teal-300 hover:bg-teal-50/20 transition block">
                        <div class="font-bold text-gray-800 mb-1">Cari & Kelola Pasien</div>
                        <p class="text-xs text-gray-500">Pencarian NIK, pendaftaran pasien baru, dan mutasi identitas.</p>
                    </a>
                    <a href="{{ route('petugas.pendaftaran.create') }}" class="p-4 rounded-xl border border-gray-100 hover:border-teal-300 hover:bg-teal-50/20 transition block">
                        <div class="font-bold text-gray-800 mb-1">Daftarkan Kunjungan Baru</div>
                        <p class="text-xs text-gray-500">Menerbitkan nomor antrian kunjungan dokter bertugas hari ini.</p>
                    </a>
                    <a href="{{ route('admin.dokter.index') }}" class="p-4 rounded-xl border border-gray-100 hover:border-teal-300 hover:bg-teal-50/20 transition block">
                        <div class="font-bold text-gray-800 mb-1">Jadwal & Kehadiran Dokter</div>
                        <p class="text-xs text-gray-500">Atur jadwal praktik mingguan dan presensi kehadiran dokter.</p>
                    </a>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
