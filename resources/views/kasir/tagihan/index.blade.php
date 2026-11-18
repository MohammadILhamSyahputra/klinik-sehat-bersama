<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Kasir & Billing Tagihan') }}
                </h2>
                <p class="text-sm text-gray-500">Daftar tagihan pasien pasca pemeriksaan dokter.</p>
            </div>
            <div>
                <form method="GET" action="{{ route('kasir.tagihan.index') }}" class="flex items-center gap-2">
                    <select name="status" class="rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                        <option value="">Semua Status</option>
                        <option value="Terbuka" {{ $status === 'Terbuka' ? 'selected' : '' }}>Terbuka (Belum Ditutup)</option>
                        <option value="Ditutup" {{ $status === 'Ditutup' ? 'selected' : '' }}>Ditutup (Selesai)</option>
                    </select>
                    <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition shadow-sm">
                        Filter
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

        <div class="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/80 border-b border-gray-200 text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            <th class="py-3.5 px-4">No. Tagihan</th>
                            <th class="py-3.5 px-4">Nama Pasien & NIK</th>
                            <th class="py-3.5 px-4">Dokter Pemeriksa</th>
                            <th class="py-3.5 px-4">Tanggal Kunjungan</th>
                            <th class="py-3.5 px-4 text-center">Status</th>
                            <th class="py-3.5 px-4 text-center">Aksi Kasir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($tagihans as $tagihan)
                            <tr class="hover:bg-teal-50/20 transition">
                                <td class="py-3 px-4 font-mono font-bold text-teal-700">
                                    INV-{{ str_pad($tagihan->id, 5, '0', STR_PAD_LEFT) }}
                                </td>
                                <td class="py-3 px-4">
                                    <div class="font-semibold text-gray-900">{{ $tagihan->kunjungan->pasien->nama }}</div>
                                    <div class="text-xs text-gray-500 font-mono">{{ $tagihan->kunjungan->pasien->no_identitas }}</div>
                                </td>
                                <td class="py-3 px-4 text-gray-700">
                                    dr. {{ $tagihan->kunjungan->dokter->user->name }}
                                </td>
                                <td class="py-3 px-4 text-gray-600">
                                    {{ $tagihan->kunjungan->tgl_kunjungan->format('d/m/Y') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-semibold
                                        @if($tagihan->status === 'Ditutup') bg-emerald-100 text-emerald-800
                                        @else bg-amber-100 text-amber-800 @endif">
                                        {{ $tagihan->status }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-center space-x-1 whitespace-nowrap">
                                    <a href="{{ route('kasir.tagihan.show', $tagihan) }}" class="inline-flex items-center px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white rounded-md text-xs font-semibold shadow-sm transition">
                                        Kelola & Pembayaran &rarr;
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-gray-400">
                                    Belum ada data tagihan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($tagihans->hasPages())
                <div class="p-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $tagihans->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>

