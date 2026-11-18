<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Kelola Dokter, Jadwal & Presensi') }}
                </h2>
                <p class="text-sm text-gray-500">Master data dokter, jadwal praktik mingguan, dan pencatatan kehadiran harian.</p>
            </div>
            <div>
                <a href="{{ route('admin.dokter.create') }}" class="inline-flex items-center px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Dokter Baru
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

        @if (session('error'))
            <div class="p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 flex items-center shadow-sm">
                <svg class="w-5 h-5 mr-2 text-rose-600" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Card Navigasi Master Data -->
        <div class="flex flex-wrap gap-2 pb-2 border-b border-gray-200 text-sm">
            <a href="{{ route('admin.dokter.index') }}" class="px-4 py-2 font-bold text-teal-700 bg-teal-50 border-b-2 border-teal-600 rounded-t-lg">
                Dokter & Jadwal
            </a>
            <a href="{{ route('admin.obat.index') }}" class="px-4 py-2 font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-t-lg transition">
                Obat & Stok Apotek
            </a>
            <a href="{{ route('admin.tindakan.index') }}" class="px-4 py-2 font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-t-lg transition">
                Tindakan Medis
            </a>
            <a href="{{ route('admin.diagnosis.index') }}" class="px-4 py-2 font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-t-lg transition">
                Diagnosis ICD-10
            </a>
            <a href="{{ route('admin.asuransi.index') }}" class="px-4 py-2 font-medium text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-t-lg transition">
                Mitra Asuransi
            </a>
        </div>

        <!-- Tabel Daftar Dokter -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="py-3.5 px-4">Nama Dokter & Gelar</th>
                            <th class="py-3.5 px-4">Spesialisasi</th>
                            <th class="py-3.5 px-4">Kontak (Email / No Telp)</th>
                            <th class="py-3.5 px-4">Jadwal Praktik Aktif</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($dokters as $dokter)
                            <tr class="hover:bg-teal-50/20 transition">
                                <td class="py-3 px-4">
                                    <div class="font-bold text-gray-900">dr. {{ $dokter->user->name }}</div>
                                    <div class="text-xs text-gray-400">ID Dokter: #{{ $dokter->id }}</div>
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                                        {{ $dokter->spesialisasi }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-xs text-gray-600">
                                    <div>{{ $dokter->user->email }}</div>
                                    <div class="text-gray-400">{{ $dokter->user->no_telp ?? '-' }}</div>
                                </td>
                                <td class="py-3 px-4 text-xs">
                                    <div class="flex flex-wrap gap-1 max-w-xs">
                                        @forelse($dokter->jadwalPraktiks as $jp)
                                            <span class="px-1.5 py-0.5 rounded bg-gray-100 text-gray-700 font-mono">
                                                {{ $jp->hari }} ({{ substr($jp->jam_mulai, 0, 5) }}-{{ substr($jp->jam_selesai, 0, 5) }})
                                            </span>
                                        @empty
                                            <span class="text-gray-400 italic">Belum diatur</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-center space-x-1 whitespace-nowrap">
                                    <a href="{{ route('admin.dokter.show', $dokter) }}" class="inline-flex items-center px-2.5 py-1.5 bg-teal-50 text-teal-700 hover:bg-teal-100 rounded-md text-xs font-semibold transition" title="Kelola Jadwal & Presensi">
                                        Jadwal & Kehadiran &rarr;
                                    </a>
                                    <a href="{{ route('admin.dokter.edit', $dokter) }}" class="inline-flex items-center px-2.5 py-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-md text-xs font-medium transition">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-400">
                                    Belum ada data dokter terdaftar.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($dokters->hasPages())
                <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $dokters->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

