<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('petugas.pendaftaran.show', $kunjungan) }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Pemindahan Pasien ke Dokter Pengganti') }}
                </h2>
                <p class="text-sm text-gray-500">Pasien: {{ $kunjungan->pasien->nama }} (No Antrian Saat Ini: #{{ $kunjungan->no_antrian }})</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        @if ($errors->any())
            <div class="mb-5 p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 shadow-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="text-sm font-medium">&bull; {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="bg-white p-6 sm:p-8 rounded-xl border border-gray-100 shadow-sm space-y-5">
            <div class="p-4 rounded-lg bg-amber-50 border border-amber-200 text-xs text-amber-900">
                <p class="font-bold mb-1">Dokter Asal Saat Ini:</p>
                <p>dr. {{ $kunjungan->dokter->user->name }} ({{ $kunjungan->dokter->spesialisasi }})</p>
                <p class="mt-2 text-amber-800">Pemindahan dokter akan otomatis memberikan nomor antrian baru pada antrian dokter tujuan untuk tanggal <strong>{{ $kunjungan->tgl_kunjungan->format('d/m/Y') }}</strong>.</p>
            </div>

            <form method="POST" action="{{ route('petugas.pendaftaran.pindah-dokter', $kunjungan) }}" class="space-y-5">
                @csrf

                <div>
                    <label for="dokter_tujuan_id" class="block text-sm font-semibold text-gray-700">Pilih Dokter Pengganti (Hadir Hari Ini) <span class="text-rose-500">*</span></label>
                    <select name="dokter_tujuan_id" id="dokter_tujuan_id" required
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                        <option value="">-- Pilih Dokter Pengganti --</option>
                        @forelse($doktersTersedia as $d)
                            <option value="{{ $d->id }}" {{ old('dokter_tujuan_id') == $d->id ? 'selected' : '' }}>
                                dr. {{ $d->user->name }} &mdash; {{ $d->spesialisasi }}
                            </option>
                        @empty
                            <option value="" disabled>Tidak ada dokter pengganti lain yang berjadwal & hadir hari ini</option>
                        @endforelse
                    </select>
                </div>

                <div>
                    <label for="alasan" class="block text-sm font-semibold text-gray-700">Alasan Pemindahan</label>
                    <input type="text" name="alasan" id="alasan" value="{{ old('alasan', 'Dokter asal berhalangan hadir / izin mendadak') }}"
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-3">
                    <a href="{{ route('petugas.pendaftaran.show', $kunjungan) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </a>
                    <button type="submit" @if($doktersTersedia->isEmpty()) disabled @endif
                        class="px-5 py-2 bg-amber-600 hover:bg-amber-700 disabled:bg-gray-400 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        Konfirmasi Pindah Dokter
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

