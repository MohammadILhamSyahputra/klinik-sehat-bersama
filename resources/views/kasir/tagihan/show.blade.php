<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <a href="{{ route('kasir.tagihan.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                        Rincian Tagihan INV-{{ str_pad($tagihan->id, 5, '0', STR_PAD_LEFT) }}
                    </h2>
                    <p class="text-sm text-gray-500">Pasien: <strong class="text-gray-900">{{ $tagihan->kunjungan->pasien->nama }}</strong> &bull; dr. {{ $tagihan->kunjungan->dokter->user->name }}</p>
                </div>
            </div>
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-bold
                    @if($tagihan->status === 'Ditutup') bg-emerald-100 text-emerald-800
                    @else bg-amber-100 text-amber-800 @endif">
                    Tagihan: {{ $tagihan->status }}
                </span>
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

        <!-- Banner Penyerahan Obat Apotek (Jika Ada Resep) -->
        @if($tagihan->kunjungan->resep && $tagihan->kunjungan->resep->resepDetails->isNotEmpty() && $tagihan->status !== 'Ditutup')
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm space-y-3">
                <h3 class="font-bold text-gray-800 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                    Apotek & Penyerahan Obat Resep Pasien
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
                    @foreach($tagihan->kunjungan->resep->resepDetails as $rd)
                        <div class="p-3 rounded-lg border border-gray-100 bg-gray-50 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-gray-800">{{ $rd->obat->nama_obat }}</span> ({{ $rd->jumlah }} pcs)
                                <div class="text-gray-500">Status: <strong class="{{ $rd->status_tebus === 'Sudah' ? 'text-emerald-700' : 'text-amber-700' }}">{{ $rd->status_tebus }}</strong></div>
                            </div>
                            @if($rd->status_tebus !== 'Sudah')
                                <form method="POST" action="{{ route('kasir.tagihan.tebus-obat', [$tagihan, $rd]) }}" class="flex gap-1">
                                    @csrf
                                    <input type="hidden" name="status_tebus" value="Sudah">
                                    <button type="submit" class="px-2.5 py-1 bg-teal-600 hover:bg-teal-700 text-white rounded font-medium">
                                        Serahkan Obat
                                    </button>
                                </form>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Rincian Biaya -->
            <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-4">
                <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-2">Rincian Item Biaya Kunjungan</h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="border-b border-gray-200 text-gray-500 font-semibold uppercase">
                                <th class="py-2.5">Jenis Biaya / Item</th>
                                <th class="py-2.5 text-right">Biaya (Rp)</th>
                                <th class="py-2.5 text-right">Tanggungan Asuransi</th>
                                <th class="py-2.5 text-right">Tanggungan Pasien</th>
                                <th class="py-2.5 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-gray-700">
                            @foreach($tagihan->detailTagihans as $item)
                                <tr>
                                    <td class="py-3 font-medium text-gray-900">
                                        {{ $item->jenis_biaya }}
                                        @if($item->kunjunganTindakan)
                                            <span class="text-gray-500 font-normal">({{ $item->kunjunganTindakan->tindakan->nama_tindakan }})</span>
                                        @elseif($item->resepDetail)
                                            <span class="text-gray-500 font-normal">({{ $item->resepDetail->obat->nama_obat }})</span>
                                        @endif
                                    </td>
                                    <td class="py-3 text-right font-mono font-medium">
                                        {{ number_format($item->jumlah_biaya, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 text-right font-mono text-teal-700">
                                        {{ number_format($item->ditanggung_asuransi, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 text-right font-mono font-bold text-gray-900">
                                        {{ number_format($item->ditanggung_pasien, 0, ',', '.') }}
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-semibold
                                            @if($item->status_pembayaran === 'Lunas') bg-emerald-100 text-emerald-800
                                            @elseif($item->status_pembayaran === 'Belum Lunas') bg-amber-100 text-amber-800
                                            @else bg-gray-100 text-gray-600 @endif">
                                            {{ $item->status_pembayaran }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-gray-200 font-bold text-sm text-gray-900">
                                <td class="pt-3">TOTAL BIAYA</td>
                                <td class="pt-3 text-right font-mono">Rp {{ number_format($totalBiaya, 0, ',', '.') }}</td>
                                <td class="pt-3 text-right font-mono text-teal-700">Rp {{ number_format($totalAsuransi, 0, ',', '.') }}</td>
                                <td class="pt-3 text-right font-mono text-teal-900">Rp {{ number_format($totalPasien, 0, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Riwayat Pembayaran Tercatat -->
                @if($tagihan->pembayarans->isNotEmpty())
                    <div class="pt-4 border-t border-gray-100">
                        <h4 class="font-bold text-xs uppercase text-gray-500 mb-2">Riwayat Pembayaran Masuk:</h4>
                        <div class="space-y-1.5 text-xs">
                            @foreach($tagihan->pembayarans as $p)
                                <div class="flex justify-between items-center p-2 rounded bg-gray-50 border border-gray-100">
                                    <div>
                                        <span class="font-semibold text-gray-800">{{ $p->metode }}</span> &bull;
                                        <span class="text-gray-500">{{ $p->tgl_bayar->format('d/m/Y H:i') }} (Kasir: {{ $p->kasir->name }})</span>
                                    </div>
                                    <span class="font-mono font-bold text-emerald-700">Rp {{ number_format($p->jumlah, 0, ',', '.') }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Panel Pembayaran & Penutupan -->
            <div class="space-y-5">
                <!-- Ringkasan Bayar -->
                <div class="bg-teal-50 p-6 rounded-xl border border-teal-200 text-teal-950 space-y-3">
                    <h4 class="font-bold text-sm uppercase tracking-wider text-teal-800">Total Yang Harus Dibayar</h4>
                    <div class="text-3xl font-black text-teal-900 font-mono">
                        Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-teal-700 space-y-1 pt-2 border-t border-teal-200">
                        <div class="flex justify-between">
                            <span>Sudah Dibayar:</span>
                            <span class="font-mono font-semibold">Rp {{ number_format($sudahDibayar, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Form Kasir Pembayaran Tunai -->
                @if($tagihan->status !== 'Ditutup' && $sisaTagihan > 0)
                    <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm space-y-4">
                        <h4 class="font-bold text-gray-800 text-sm border-b border-gray-100 pb-2">Catat Pembayaran Pasien</h4>
                        <form method="POST" action="{{ route('kasir.tagihan.bayar', $tagihan) }}" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Metode Bayar</label>
                                <select name="metode" required class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-xs">
                                    <option value="Tunai">Tunai (Cash)</option>
                                    <option value="Transfer">Transfer Bank</option>
                                    <option value="Kartu Debit">Kartu Debit</option>
                                    <option value="Kartu Kredit">Kartu Kredit</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Nominal Yang Dibayar (Rp)</label>
                                <input type="number" name="jumlah" value="{{ $sisaTagihan }}" max="{{ $sisaTagihan }}" required
                                    class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-xs font-mono">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-gray-600 mb-1">Uang Diterima dari Pasien (Untuk Tunai)</label>
                                <input type="number" name="uang_bayar" placeholder="Contoh: 100000"
                                    class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-xs font-mono">
                                <p class="text-[11px] text-gray-400 mt-0.5">Sistem akan menghitung kembalian otomatis.</p>
                            </div>
                            <button type="submit" class="w-full py-2.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-bold rounded-lg shadow-sm transition">
                                Simpan Pembayaran
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Tombol Tutup Transaksi -->
                @if($tagihan->status !== 'Ditutup')
                    <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm space-y-3">
                        <h4 class="font-bold text-gray-800 text-sm">Tutup Transaksi Tagihan</h4>
                        <p class="text-xs text-gray-500">Pastikan seluruh item telah memiliki status lunas sebelum menutup transaksi.</p>
                        <form method="POST" action="{{ route('kasir.tagihan.tutup', $tagihan) }}">
                            @csrf
                            <button type="submit" class="w-full py-2.5 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-lg shadow-sm transition">
                                Tutup Transaksi Selesai &rarr;
                            </button>
                        </form>
                    </div>
                @else
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs text-center font-bold">
                        ✓ Transaksi Ini Telah Ditutup & Selesai
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>

