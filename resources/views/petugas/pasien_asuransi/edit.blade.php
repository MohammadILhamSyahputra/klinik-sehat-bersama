<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('petugas.pasien.show', $pasien) }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Edit Polis Asuransi Pasien') }}
                </h2>
                <p class="text-sm text-gray-500">Pasien: <strong class="text-gray-800">{{ $pasien->nama }}</strong> &bull; NIK: {{ $pasien->no_identitas }}</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-6 sm:p-8 rounded-xl border border-gray-100 shadow-sm">
            <form method="POST" action="{{ route('petugas.pasien.asuransi.update', [$pasien, $asuransi]) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="perusahaan_asuransi_id" class="block text-sm font-semibold text-gray-700">Perusahaan Asuransi Rekanan <span class="text-rose-500">*</span></label>
                    <select name="perusahaan_asuransi_id" id="perusahaan_asuransi_id" required
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                        <option value="">-- Pilih Perusahaan Asuransi --</option>
                        @foreach($perusahaanAsuransis as $pa)
                            <option value="{{ $pa->id }}" {{ old('perusahaan_asuransi_id', $asuransi->perusahaan_asuransi_id) == $pa->id ? 'selected' : '' }}>
                                {{ $pa->nama_asuransi }}
                            </option>
                        @endforeach
                    </select>
                    @error('perusahaan_asuransi_id')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="no_polis" class="block text-sm font-semibold text-gray-700">Nomor Polis / Nomor Kartu Peserta <span class="text-rose-500">*</span></label>
                    <input type="text" name="no_polis" id="no_polis" value="{{ old('no_polis', $asuransi->no_polis) }}" required
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 font-mono text-sm">
                    @error('no_polis')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="prioritas" class="block text-sm font-semibold text-gray-700">Prioritas Asuransi <span class="text-rose-500">*</span></label>
                        <select name="prioritas" id="prioritas" required
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                            <option value="1" {{ old('prioritas', $asuransi->prioritas) == '1' ? 'selected' : '' }}>Prioritas 1 (Utama)</option>
                            <option value="2" {{ old('prioritas', $asuransi->prioritas) == '2' ? 'selected' : '' }}>Prioritas 2 (Sekunder)</option>
                        </select>
                        @error('prioritas')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="masa_berlaku" class="block text-sm font-semibold text-gray-700">Masa Berlaku s/d <span class="text-rose-500">*</span></label>
                        <input type="date" name="masa_berlaku" id="masa_berlaku" value="{{ old('masa_berlaku', $asuransi->masa_berlaku->format('Y-m-d')) }}" required
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                        @error('masa_berlaku')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-3">
                    <a href="{{ route('petugas.pasien.show', $pasien) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        Perbarui Polis
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

