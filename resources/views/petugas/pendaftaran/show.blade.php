<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('petugas.pendaftaran.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                        Detail Pendaftaran Antrian
                    </h2>
                    <p class="text-sm text-gray-500">Tanggal: {{ $kunjungan->tgl_kunjungan->format('d F Y') }}</p>
                </div>
            </div>
            <div class="flex space-x-2">
                <button onclick="window.print()" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg shadow-sm transition inline-flex items-center">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Karcis Antrian
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        @if (session('success'))
            <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-2 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Kartu Nomor Antrian Pasien -->
            <div class="bg-gradient-to-br from-teal-500 to-teal-700 text-white p-6 rounded-2xl shadow-md text-center flex flex-col justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-teal-100">Karcis Antrian Pasien</span>
                    <div class="my-6">
                        <span class="text-6xl font-black tracking-tight">#{{ $kunjungan->no_antrian }}</span>
                    </div>
                    <div class="text-sm font-medium text-teal-100">
                        {{ $kunjungan->tgl_kunjungan->format('d/m/Y') }}
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-teal-400/40 text-xs text-teal-100">
                    Klinik Sehat Bersama
                </div>
            </div>

            <!-- Detail Pasien & Kunjungan -->
            <div class="md:col-span-2 bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h3 class="font-bold text-gray-800 text-lg">Informasi Kunjungan</h3>
                    <span class="px-3 py-1 rounded-full text-xs font-bold
                        @if($kunjungan->status === 'Selesai') bg-emerald-100 text-emerald-800
                        @elseif($kunjungan->status === 'Dipanggil') bg-amber-100 text-amber-800
                        @elseif($kunjungan->status === 'Batal') bg-rose-100 text-rose-800
                        @else bg-blue-100 text-blue-800 @endif">
                        {{ $kunjungan->status }}
                    </span>
                </div>

                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <dt class="text-xs text-gray-400 font-semibold uppercase">Nama Pasien</dt>
                        <dd class="font-bold text-gray-900 mt-0.5">{{ $kunjungan->pasien->nama }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 font-semibold uppercase">NIK Pasien</dt>
                        <dd class="font-mono text-gray-800 mt-0.5">{{ $kunjungan->pasien->no_identitas }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 font-semibold uppercase">Dokter Pemeriksa</dt>
                        <dd class="font-medium text-gray-800 mt-0.5">dr. {{ $kunjungan->dokter->user->name }} ({{ $kunjungan->dokter->spesialisasi }})</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 font-semibold uppercase">Petugas Pendaftaran</dt>
                        <dd class="font-medium text-gray-800 mt-0.5">{{ $kunjungan->petugas->name }}</dd>
                    </div>
                    <div class="sm:col-span-2">
                        <dt class="text-xs text-gray-400 font-semibold uppercase">Keluhan Awal</dt>
                        <dd class="text-gray-700 mt-0.5 bg-gray-50 p-3 rounded-lg border border-gray-100">{{ $kunjungan->keluhan ?? 'Tidak ada catatan keluhan awal' }}</dd>
                    </div>
                </dl>

                @if($kunjungan->pemindahanDokters->isNotEmpty())
                    <div class="pt-4 border-t border-gray-100">
                        <h4 class="text-xs font-bold uppercase text-amber-800 mb-2">Riwayat Pemindahan Dokter Pengganti:</h4>
                        <div class="space-y-2">
                            @foreach($kunjungan->pemindahanDokters as $pindah)
                                <div class="p-3 bg-amber-50 rounded-lg text-xs text-amber-900 border border-amber-200">
                                    Dipindahkan dari <strong>dr. {{ $pindah->dokterAsal->user->name }}</strong> ke <strong>dr. {{ $pindah->dokterTujuan->user->name }}</strong> pada {{ $pindah->waktu_pemindahan->format('d/m/Y H:i') }}.
                                    <div class="text-amber-700 mt-0.5">Alasan: {{ $pindah->alasan }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

