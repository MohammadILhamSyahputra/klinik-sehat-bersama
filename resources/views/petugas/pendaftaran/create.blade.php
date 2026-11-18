<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('petugas.pendaftaran.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Daftar Kunjungan Pasien Baru') }}
                </h2>
                <p class="text-sm text-gray-500">Pilih pasien, tentukan tanggal kunjungan, dan pilih dokter yang bertugas.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        @if ($errors->any())
            <div class="mb-5 p-4 rounded-lg bg-rose-50 border border-rose-200 text-rose-800 shadow-sm space-y-1">
                @foreach ($errors->all() as $error)
                    <p class="text-sm font-medium">&bull; {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <div class="bg-white p-6 sm:p-8 rounded-xl border border-gray-100 shadow-sm">
            <form method="POST" action="{{ route('petugas.pendaftaran.store') }}" class="space-y-6">
                @csrf

                <!-- Pilih Pasien -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label for="pasien_id" class="block text-sm font-semibold text-gray-700">Pilih Pasien <span class="text-rose-500">*</span></label>
                        <a href="{{ route('petugas.pasien.create') }}" class="text-xs font-semibold text-teal-600 hover:text-teal-700">
                            + Pasien Belum Terdaftar? Buat Baru
                        </a>
                    </div>

                    @if($selectedPasien)
                        <div class="p-4 rounded-lg bg-teal-50/60 border border-teal-200 flex items-center justify-between">
                            <div>
                                <input type="hidden" name="pasien_id" value="{{ $selectedPasien->id }}">
                                <p class="font-bold text-gray-900">{{ $selectedPasien->nama }}</p>
                                <p class="text-xs font-mono text-teal-800">NIK: {{ $selectedPasien->no_identitas }} &bull; Lahir: {{ $selectedPasien->tgl_lahir->format('d/m/Y') }}</p>
                            </div>
                            <a href="{{ route('petugas.pendaftaran.create') }}" class="text-xs text-rose-600 hover:underline">
                                Ganti Pasien
                            </a>
                        </div>
                    @else
                        <select name="pasien_id" id="pasien_id" required
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                            <option value="">-- Pilih Pasien Terdaftar --</option>
                            @foreach(\App\Models\Pasien::latest()->limit(50)->get() as $p)
                                <option value="{{ $p->id }}" {{ old('pasien_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nama }} (NIK: {{ $p->no_identitas }})
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-400">Menampilkan 50 pasien terbaru. Gunakan menu Data Pasien untuk mencari.</p>
                    @endif
                </div>

                <!-- Tanggal Kunjungan -->
                <div>
                    <label for="tgl_kunjungan" class="block text-sm font-semibold text-gray-700">Tanggal Kunjungan <span class="text-rose-500">*</span></label>
                    <input type="date" name="tgl_kunjungan" id="tgl_kunjungan" value="{{ old('tgl_kunjungan', $tanggal) }}" required
                        onchange="window.location.href = '{{ route('petugas.pendaftaran.create') }}?tanggal=' + this.value + ({{ $selectedPasien ? "'&pasien_id=" . $selectedPasien->id . "'" : "''" }})"
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                    <p class="mt-1 text-xs text-gray-500">Mengubah tanggal akan memuat ulang daftar dokter yang bertugas di tanggal tersebut.</p>
                </div>

                <!-- Dokter Bertugas -->
                <div>
                    <label for="dokter_id" class="block text-sm font-semibold text-gray-700">Dokter Tujuan (Sedang Bertugas) <span class="text-rose-500">*</span></label>
                    <select name="dokter_id" id="dokter_id" required
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                        <option value="">-- Pilih Dokter Bertugas --</option>
                        @forelse($dokters as $dok)
                            <option value="{{ $dok->id }}" {{ old('dokter_id') == $dok->id ? 'selected' : '' }}>
                                dr. {{ $dok->user->name }} &mdash; {{ $dok->spesialisasi }}
                            </option>
                        @empty
                            <option value="" disabled>Tidak ada dokter yang berjadwal & hadir pada tanggal ini</option>
                        @endforelse
                    </select>
                    @if($dokters->isEmpty())
                        <p class="mt-1.5 text-xs text-amber-600 font-medium">
                            ⚠️ Tidak ada dokter yang memiliki jadwal aktif atau dokter ditandai "Tidak Hadir" di tanggal ini.
                        </p>
                    @endif
                </div>

                <!-- Keluhan Awal -->
                <div>
                    <label for="keluhan" class="block text-sm font-semibold text-gray-700">Keluhan Pasien (Awal)</label>
                    <textarea name="keluhan" id="keluhan" rows="3"
                        placeholder="Contoh: Demam tinggi sejak 2 hari yang lalu, pusing, dan batuk kering..."
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">{{ old('keluhan') }}</textarea>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-3">
                    <a href="{{ route('petugas.pendaftaran.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </a>
                    <button type="submit" @if($dokters->isEmpty()) disabled @endif
                        class="px-5 py-2.5 bg-teal-600 hover:bg-teal-700 disabled:bg-gray-400 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        Terbitkan Nomor Antrian
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

