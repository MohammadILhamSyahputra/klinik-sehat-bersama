<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Antrian Pemeriksaan Pasien') }}
                </h2>
                <p class="text-sm text-gray-500">dr. {{ $dokter->user->name }} ({{ $dokter->spesialisasi }})</p>
            </div>
            <div>
                <form method="GET" action="{{ route('dokter.pemeriksaan.antrian') }}" class="flex items-center gap-2">
                    <input type="date" name="tanggal" value="{{ $tanggal }}"
                        class="rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                    <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition shadow-sm">
                        Ganti Tanggal
                    </button>
                </form>
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

        @if ($errors->any())
            <div class="p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 shadow-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="text-sm font-medium">&bull; {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="py-3.5 px-4 text-center">No Antrian</th>
                            <th class="py-3.5 px-4">Nama Pasien</th>
                            <th class="py-3.5 px-4">Usia / Tgl Lahir</th>
                            <th class="py-3.5 px-4">Keluhan Awal</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4 text-center">Tindakan Dokter</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($antrian as $kunjungan)
                            <tr class="hover:bg-teal-50/20 transition">
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-teal-100 text-teal-800 font-bold text-sm">
                                        #{{ $kunjungan->no_antrian }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-gray-900">{{ $kunjungan->pasien->nama }}</div>
                                    <div class="text-xs text-gray-500 font-mono">{{ $kunjungan->pasien->no_identitas }}</div>
                                </td>
                                <td class="py-3 px-4 text-gray-600">
                                    {{ $kunjungan->pasien->tgl_lahir->age }} Tahun
                                    <span class="text-xs text-gray-400">({{ $kunjungan->pasien->tgl_lahir->format('d/m/Y') }})</span>
                                </td>
                                <td class="py-3 px-4 text-gray-600 max-w-xs truncate" title="{{ $kunjungan->keluhan }}">
                                    {{ $kunjungan->keluhan ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                        @if($kunjungan->status === 'Selesai') bg-emerald-100 text-emerald-800
                                        @elseif($kunjungan->status === 'Dipanggil') bg-amber-100 text-amber-800
                                        @elseif($kunjungan->status === 'Batal') bg-rose-100 text-rose-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        {{ $kunjungan->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center space-x-1 whitespace-nowrap">
                                    @if($kunjungan->status === 'Terdaftar')
                                        <form method="POST" action="{{ route('dokter.pemeriksaan.panggil', $kunjungan) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-md text-xs font-semibold shadow-sm transition">
                                                Panggil Pasien &rarr;
                                            </button>
                                        </form>
                                    @elseif($kunjungan->status === 'Dipanggil')
                                        <a href="{{ route('dokter.pemeriksaan.periksa', $kunjungan) }}" class="inline-flex items-center px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white rounded-md text-xs font-semibold shadow-sm transition">
                                            Lanjutkan Periksa &rarr;
                                        </a>
                                    @elseif($kunjungan->status === 'Selesai')
                                        <a href="{{ route('dokter.pemeriksaan.periksa', $kunjungan) }}" class="inline-flex items-center px-2.5 py-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-md text-xs font-medium transition">
                                            Lihat Rekam Medis
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-400">
                                    Tidak ada antrian pasien pada tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

