<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Data Pasien') }}
                </h2>
                <p class="text-sm text-gray-500">Kelola informasi pasien terdaftar dan rekam identitas.</p>
            </div>
            <div>
                <a href="{{ route('petugas.pasien.create') }}" class="inline-flex items-center px-4 py-2.5 bg-teal-600 hover:bg-teal-700 text-white font-semibold text-sm rounded-lg shadow-sm transition">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Pasien Baru
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

        <!-- Card Pencarian -->
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <form method="GET" action="{{ route('petugas.pasien.index') }}" class="flex flex-col sm:flex-row gap-3">
                <div class="relative flex-1">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama pasien, No Identitas / NIK, atau nomor telepon..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="px-5 py-2.5 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition shadow-sm">
                        Cari
                    </button>
                    @if($search)
                        <a href="{{ route('petugas.pasien.index') }}" class="px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Tabel Daftar Pasien -->
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="py-3.5 px-4">No Identitas (NIK)</th>
                            <th class="py-3.5 px-4">Nama Pasien</th>
                            <th class="py-3.5 px-4">Tanggal Lahir</th>
                            <th class="py-3.5 px-4">No Telepon</th>
                            <th class="py-3.5 px-4">Alamat</th>
                            <th class="py-3.5 px-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($pasiens as $pasien)
                            <tr class="hover:bg-teal-50/30 transition">
                                <td class="py-3 px-4 font-mono font-medium text-teal-700">
                                    {{ $pasien->no_identitas }}
                                </td>
                                <td class="py-3 px-4 font-semibold text-gray-900">
                                    {{ $pasien->nama }}
                                </td>
                                <td class="py-3 px-4 text-gray-600">
                                    {{ $pasien->tgl_lahir->format('d/m/Y') }}
                                    <span class="text-xs text-gray-400">({{ $pasien->tgl_lahir->age }} thn)</span>
                                </td>
                                <td class="py-3 px-4 text-gray-600">
                                    {{ $pasien->no_telp ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-gray-600 max-w-xs truncate" title="{{ $pasien->alamat }}">
                                    {{ $pasien->alamat ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-center space-x-1 whitespace-nowrap">
                                    <a href="{{ route('petugas.pendaftaran.create', ['pasien_id' => $pasien->id]) }}" class="inline-flex items-center px-2.5 py-1.5 bg-teal-50 text-teal-700 hover:bg-teal-100 rounded-md text-xs font-semibold transition" title="Daftarkan ke Kunjungan Hari Ini">
                                        + Daftar Kunjungan
                                    </a>
                                    <a href="{{ route('petugas.pasien.show', $pasien) }}" class="inline-flex items-center px-2.5 py-1.5 bg-gray-100 text-gray-700 hover:bg-gray-200 rounded-md text-xs font-medium transition">
                                        Detail
                                    </a>
                                    <a href="{{ route('petugas.pasien.edit', $pasien) }}" class="inline-flex items-center px-2.5 py-1.5 bg-amber-50 text-amber-700 hover:bg-amber-100 rounded-md text-xs font-medium transition">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-400">
                                    Tidak ada data pasien yang ditemukan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($pasiens->hasPages())
                <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $pasiens->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

