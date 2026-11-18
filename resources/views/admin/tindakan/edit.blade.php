<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.tindakan.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Edit Tarif Tindakan Medis') }}
                </h2>
                <p class="text-sm text-gray-500">{{ $tindakan->nama_tindakan }} ({{ $tindakan->kode_tindakan }})</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-6 sm:p-8 rounded-xl border border-gray-100 shadow-sm">
            <form method="POST" action="{{ route('admin.tindakan.update', $tindakan) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="kode_tindakan" class="block text-sm font-semibold text-gray-700">Kode Tindakan <span class="text-rose-500">*</span></label>
                    <input type="text" name="kode_tindakan" id="kode_tindakan" value="{{ old('kode_tindakan', $tindakan->kode_tindakan) }}" required maxlength="50"
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 font-mono text-sm uppercase">
                    @error('kode_tindakan')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nama_tindakan" class="block text-sm font-semibold text-gray-700">Nama Tindakan Medis <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_tindakan" id="nama_tindakan" value="{{ old('nama_tindakan', $tindakan->nama_tindakan) }}" required maxlength="255"
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                    @error('nama_tindakan')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tarif" class="block text-sm font-semibold text-gray-700">Tarif Tindakan (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="tarif" id="tarif" value="{{ old('tarif', (int)$tindakan->tarif) }}" required min="0"
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm font-mono font-bold">
                    @error('tarif')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.tindakan.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        Perbarui Tindakan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

