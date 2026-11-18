<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('petugas.pasien.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                        {{ $pasien->nama }}
                    </h2>
                    <p class="text-sm font-mono text-teal-700">NIK: {{ $pasien->no_identitas }}</p>
                </div>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('petugas.pendaftaran.create', ['pasien_id' => $pasien->id]) }}" class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm rounded-lg shadow-sm transition">
                    + Daftarkan Kunjungan
                </a>
                <a href="{{ route('petugas.pasien.edit', $pasien) }}" class="inline-flex items-center px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                    Edit Pasien
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        @if (session('success'))
            <div class="p-4 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-2 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Profil Singkat -->
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-4">
                <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-2 text-base">Informasi Pasien</h3>
                <dl class="space-y-3 text-sm">
                    <div>
                        <dt class="text-xs text-gray-400 uppercase font-semibold">Tanggal Lahir</dt>
                        <dd class="text-gray-800 font-medium">{{ $pasien->tgl_lahir->format('d F Y') }} ({{ $pasien->tgl_lahir->age }} Tahun)</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 uppercase font-semibold">Nomor Telepon</dt>
                        <dd class="text-gray-800 font-medium">{{ $pasien->no_telp ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-400 uppercase font-semibold">Alamat</dt>
                        <dd class="text-gray-800 font-medium">{{ $pasien->alamat ?? '-' }}</dd>
                    </div>
                </dl>

                <!-- Polis Asuransi Pasien -->
                <div class="pt-4 border-t border-gray-100">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="font-bold text-sm text-gray-800">Polis Asuransi</h4>
                        @if($pasien->pasienAsuransis->count() < 2)
                            <a href="{{ route('petugas.pasien.asuransi.create', $pasien) }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">
                                + Tambah Polis
                            </a>
                        @endif
                    </div>
                    <div class="space-y-2">
                        @forelse($pasien->pasienAsuransis as $asuransi)
                            <div class="p-3 rounded-lg border border-gray-100 bg-gray-50 text-xs space-y-1">
                                <div class="flex justify-between font-bold text-gray-800">
                                    <span>{{ $asuransi->perusahaanAsuransi->nama_asuransi }}</span>
                                    <span class="px-1.5 py-0.5 rounded bg-blue-100 text-blue-700 font-normal">Prioritas {{ $asuransi->prioritas }}</span>
                                </div>
                                <div class="text-gray-500 font-mono">No: {{ $asuransi->no_polis }}</div>
                                <div class="text-gray-500">Masa berlaku: {{ $asuransi->masa_berlaku->format('d/m/Y') }}</div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400">Belum ada asuransi terdaftar (Pasien Umum/Tunai).</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Riwayat Kunjungan & Rekam Medis -->
            <div class="md:col-span-2 bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-4">
                <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-2 text-base">Riwayat Kunjungan & Pemeriksaan</h3>

                <div class="space-y-4">
                    @forelse($riwayatKunjungan as $kunjungan)
                        <div class="p-4 rounded-xl border border-gray-100 hover:border-teal-200 transition bg-white space-y-2">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between text-xs text-gray-500 border-b border-gray-50 pb-2">
                                <div class="font-semibold text-gray-700">
                                    {{ $kunjungan->tgl_kunjungan->format('d F Y') }} &bull; Antrian #{{ $kunjungan->no_antrian }}
                                </div>
                                <div class="mt-1 sm:mt-0">
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold
                                        @if($kunjungan->status === 'Selesai') bg-emerald-100 text-emerald-800
                                        @elseif($kunjungan->status === 'Dipanggil') bg-amber-100 text-amber-800
                                        @elseif($kunjungan->status === 'Batal') bg-rose-100 text-rose-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ $kunjungan->status }}
                                    </span>
                                </div>
                            </div>

                            <div class="text-sm">
                                <p><strong class="text-gray-700">Dokter:</strong> dr. {{ $kunjungan->dokter->user->name }} ({{ $kunjungan->dokter->spesialisasi }})</p>
                                @if($kunjungan->keluhan)
                                    <p class="text-gray-600 mt-1"><strong class="text-gray-700">Keluhan:</strong> {{ $kunjungan->keluhan }}</p>
                                @endif
                                @if($kunjungan->hasil_pemeriksaan)
                                    <p class="text-gray-600 mt-1"><strong class="text-gray-700">Hasil Pemeriksaan:</strong> {{ $kunjungan->hasil_pemeriksaan }}</p>
                                @endif
                            </div>

                            @if($kunjungan->kunjunganDiagnoses->isNotEmpty())
                                <div class="pt-2 text-xs flex flex-wrap gap-1">
                                    <span class="text-gray-500 font-semibold mr-1">Diagnosis:</span>
                                    @foreach($kunjungan->kunjunganDiagnoses as $kd)
                                        <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700 font-medium">
                                            {{ $kd->diagnosis->kode_diagnosis }} - {{ $kd->diagnosis->nama_diagnosis }} ({{ $kd->jenis }})
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="py-12 text-center text-gray-400">
                            Belum ada riwayat kunjungan medis untuk pasien ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

