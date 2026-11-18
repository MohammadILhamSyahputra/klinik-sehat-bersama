<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Pendaftaran Antrian Kunjungan') }}
                </h2>
                <p class="text-sm text-gray-500">Monitoring status antrian pasien klinik hari ini.</p>
            </div>
            <div>
                <a href="{{ route('petugas.pendaftaran.create') }}" class="inline-flex items-center px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Daftar Antrian Baru
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

        @if ($errors->any())
            <div class="p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 shadow-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="text-sm font-medium">&bull; {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Filter Tanggal & Status -->
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <form method="GET" action="{{ route('petugas.pendaftaran.index') }}" class="flex flex-col sm:flex-row gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Tanggal Kunjungan</label>
                    <input type="date" name="tanggal" value="{{ $tanggal }}"
                        class="rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-1">Status Kunjungan</label>
                    <select name="status" class="rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                        <option value="">Semua Status</option>
                        <option value="Terdaftar" {{ $status === 'Terdaftar' ? 'selected' : '' }}>Terdaftar (Menunggu)</option>
                        <option value="Dipanggil" {{ $status === 'Dipanggil' ? 'selected' : '' }}>Dipanggil (Diperiksa)</option>
                        <option value="Selesai" {{ $status === 'Selesai' ? 'selected' : '' }}>Selesai</option>
                        <option value="Batal" {{ $status === 'Batal' ? 'selected' : '' }}>Batal</option>
                    </select>
                </div>
                <div class="flex items-end gap-2">
                    <button type="submit" class="px-5 py-2.5 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition shadow-sm">
                        Filter
                    </button>
                    <a href="{{ route('petugas.pendaftaran.index') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                        Hari Ini
                    </a>
                </div>
            </form>
        </div>

        <!-- Tabel Antrian -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="py-3.5 px-4 text-center">No Antrian</th>
                            <th class="py-3.5 px-4">Nama Pasien & NIK</th>
                            <th class="py-3.5 px-4">Dokter Tujuan</th>
                            <th class="py-3.5 px-4">Keluhan Awal</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4 text-center">Aksi Petugas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($kunjungans as $kunjungan)
                            <tr class="hover:bg-teal-50/20 transition">
                                <td class="py-3 px-4 text-center">
                                    <span class="inline-flex items-center justify-center w-9 h-9 rounded-full bg-teal-100 text-teal-800 font-bold text-sm">
                                        #{{ $kunjungan->no_antrian }}
                                    </span>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-gray-900">{{ $kunjungan->pasien->nama }}</div>
                                    <div class="text-xs text-gray-500 font-mono">{{ $kunjungan->pasien->no_identitas }} &bull; {{ $kunjungan->pasien->no_telp ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-medium text-gray-800">dr. {{ $kunjungan->dokter->user->name }}</div>
                                    <div class="text-xs text-teal-600">{{ $kunjungan->dokter->spesialisasi }}</div>
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
                                    <a href="{{ route('petugas.pendaftaran.show', $kunjungan) }}" class="inline-flex items-center px-2.5 py-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-md text-xs font-medium transition">
                                        Detail
                                    </a>

                                    @if($kunjungan->status === 'Terdaftar')
                                        <a href="{{ route('petugas.pendaftaran.pindah-dokter.form', $kunjungan) }}" class="inline-flex items-center px-2.5 py-1.5 bg-amber-50 text-amber-700 hover:bg-amber-100 rounded-md text-xs font-medium transition" title="Pindah ke dokter pengganti">
                                            Pindah Dokter
                                        </a>

                                        <form method="POST" action="{{ route('petugas.pendaftaran.batal', $kunjungan) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan kunjungan ini?')">
                                            @csrf
                                            <button type="submit" class="inline-flex items-center px-2.5 py-1.5 bg-rose-50 text-rose-700 hover:bg-rose-100 rounded-md text-xs font-medium transition">
                                                Batal
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-400">
                                    Belum ada pendaftaran antrian pada tanggal ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($kunjungans->hasPages())
                <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $kunjungans->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

