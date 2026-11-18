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
                    {{ __('Edit Data Pasien') }}
                </h2>
                <p class="text-sm text-gray-500">Perbarui informasi identitas pasien.</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-6 sm:p-8 rounded-xl border border-gray-100 shadow-sm">
            <form method="POST" action="{{ route('petugas.pasien.update', $pasien) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="no_identitas" class="block text-sm font-semibold text-gray-700">Nomor Identitas (NIK / KTP) <span class="text-rose-500">*</span></label>
                    <input type="text" name="no_identitas" id="no_identitas" value="{{ old('no_identitas', $pasien->no_identitas) }}" required maxlength="50"
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 font-mono text-sm">
                    @error('no_identitas')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nama" class="block text-sm font-semibold text-gray-700">Nama Lengkap Pasien <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama" id="nama" value="{{ old('nama', $pasien->nama) }}" required maxlength="255"
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                    @error('nama')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="tgl_lahir" class="block text-sm font-semibold text-gray-700">Tanggal Lahir <span class="text-rose-500">*</span></label>
                        <input type="date" name="tgl_lahir" id="tgl_lahir" value="{{ old('tgl_lahir', $pasien->tgl_lahir->format('Y-m-d')) }}" required
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                        @error('tgl_lahir')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="no_telp" class="block text-sm font-semibold text-gray-700">No. Telepon / WhatsApp</label>
                        <input type="text" name="no_telp" id="no_telp" value="{{ old('no_telp', $pasien->no_telp) }}" maxlength="20"
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                        @error('no_telp')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="alamat" class="block text-sm font-semibold text-gray-700">Alamat Tempat Tinggal</label>
                    <textarea name="alamat" id="alamat" rows="3"
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">{{ old('alamat', $pasien->alamat) }}</textarea>
                    @error('alamat')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-3">
                    <a href="{{ route('petugas.pasien.show', $pasien) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        Perbarui Data Pasien
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

