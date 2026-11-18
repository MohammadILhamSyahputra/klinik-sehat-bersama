<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.obat.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    {{ __('Edit Data Obat') }}
                </h2>
                <p class="text-sm text-gray-500">{{ $obat->nama_obat }} ({{ $obat->kode_obat }})</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-6 sm:p-8 rounded-xl border border-gray-100 shadow-sm">
            <form method="POST" action="{{ route('admin.obat.update', $obat) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label for="kode_obat" class="block text-sm font-semibold text-gray-700">Kode Obat <span class="text-rose-500">*</span></label>
                    <input type="text" name="kode_obat" id="kode_obat" value="{{ old('kode_obat', $obat->kode_obat) }}" required maxlength="50"
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 font-mono text-sm uppercase">
                    @error('kode_obat')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="nama_obat" class="block text-sm font-semibold text-gray-700">Nama Obat & Sediaan <span class="text-rose-500">*</span></label>
                    <input type="text" name="nama_obat" id="nama_obat" value="{{ old('nama_obat', $obat->nama_obat) }}" required maxlength="255"
                        class="mt-1 block w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                    @error('nama_obat')
                        <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="stok" class="block text-sm font-semibold text-gray-700">Stok Fisik Apotek <span class="text-rose-500">*</span></label>
                        <input type="number" name="stok" id="stok" value="{{ old('stok', $obat->stok) }}" required min="0"
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm font-mono font-bold">
                        @error('stok')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="harga" class="block text-sm font-semibold text-gray-700">Harga Satuan (Rp) <span class="text-rose-500">*</span></label>
                        <input type="number" name="harga" id="harga" value="{{ old('harga', (int)$obat->harga) }}" required min="0"
                            class="mt-1 block w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm font-mono">
                        @error('harga')
                            <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex items-center justify-end space-x-3">
                    <a href="{{ route('admin.obat.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                        Perbarui Obat
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>

