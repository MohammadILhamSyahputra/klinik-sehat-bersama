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
                    Status Tagihan: {{ $tagihan->status }}
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

        <!-- PENEBUSAN RESEP & PEMOTONGAN STOK FISIK APOTEK (Activity 12 & Sequence 12) -->
        @if($tagihan->kunjungan->resep && $tagihan->kunjungan->resep->resepDetails->isNotEmpty())
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between border-b border-gray-100 pb-3 gap-2">
                    <div>
                        <h3 class="font-bold text-gray-800 text-base flex items-center gap-2">
                            <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                            </svg>
                            Penebusan Resep & Pemotongan Stok Apotek
                        </h3>
                        <p class="text-xs text-gray-400">Activity Diagram 12 &bull; Sequence Diagram 12 (Kondisi Khusus: Obat tidak diserahkan dikeluarkan dari tagihan).</p>
                    </div>
                    <div>
                        <span class="text-xs font-semibold px-2.5 py-1 rounded bg-gray-100 text-gray-700">
                            Status Resep: {{ $tagihan->kunjungan->resep->status_tebus }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($tagihan->kunjungan->resep->resepDetails as $rd)
                        <div class="p-4 rounded-xl border border-gray-100 bg-gray-50 flex flex-col justify-between space-y-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h4 class="font-bold text-gray-800 text-sm">{{ $rd->obat->nama_obat }}</h4>
                                    <div class="text-xs text-gray-500 mt-0.5">Jumlah: <strong>{{ $rd->jumlah }} pcs</strong> &bull; Aturan: {{ $rd->aturan_pakai }}</div>
                                    <div class="text-xs text-teal-700 mt-0.5">Stok Fisik Apotek Saat Ini: <strong>{{ $rd->obat->stok }} pcs</strong></div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[11px] font-bold
                                    @if($rd->status_tebus === 'Sudah') bg-emerald-100 text-emerald-800
                                    @elseif($rd->status_tebus === 'Tidak Diserahkan') bg-rose-100 text-rose-800
                                    @else bg-amber-100 text-amber-800 @endif">
                                    {{ $rd->status_tebus }}
                                </span>
                            </div>

                            @if($tagihan->status !== 'Ditutup' && $rd->status_tebus === 'Belum')
                                <div class="flex items-center gap-2 pt-2 border-t border-gray-200/60">
                                    <!-- Tombol Serahkan Obat (Potong Stok) -->
                                    <form method="POST" action="{{ route('kasir.tagihan.tebus-obat', [$tagihan, $rd]) }}" class="inline">
                                        @csrf
                                        <input type="hidden" name="status_tebus" value="Sudah">
                                        <button type="submit" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded text-xs font-semibold shadow-sm transition">
                                            ✓ Serahkan Obat (Potong Stok)
                                        </button>
                                    </form>

                                    <!-- Tombol Tidak Diserahkan (Keluarkan dari tagihan) -->
                                    <form method="POST" action="{{ route('kasir.tagihan.tebus-obat', [$tagihan, $rd]) }}" class="inline" onsubmit="return confirm('Keluarkan obat ini dari tagihan karena stok habis / tidak diserahkan?')">
                                        @csrf
                                        <input type="hidden" name="status_tebus" value="Tidak Diserahkan">
                                        <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded text-xs font-semibold transition">
                                            ✕ Tidak Diserahkan
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Rincian Biaya -->
            <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-4">
                <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                    <h3 class="font-bold text-gray-800 text-base">Rincian Item Biaya Kunjungan</h3>
                    <span class="text-xs text-gray-400">Activity Diagram 8 &bull; Sequence Diagram 6</span>
                </div>

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

                <!-- Modul Klaim Asuransi (Activity 10 & Sequence 7) -->
                @if($tagihan->kunjungan->pasien->pasienAsuransis->isNotEmpty() && $tagihan->status !== 'Ditutup')
                    <div class="mt-6 pt-5 border-t border-gray-100 bg-blue-50/40 p-5 rounded-xl border border-blue-100 space-y-4">
                        <div class="flex items-center justify-between">
                            <h4 class="font-bold text-sm text-blue-900">Pengajuan Klaim Asuransi (Activity 10 & Sequence 7)</h4>
                            <span class="text-xs text-blue-700">Pasien memiliki {{ $tagihan->kunjungan->pasien->pasienAsuransis->count() }} polis aktif</span>
                        </div>

                        <form method="POST" action="{{ route('kasir.tagihan.ajukan-klaim', $tagihan) }}" class="space-y-3">
                            @csrf
                            <div>
                                <label class="block text-xs font-semibold text-gray-700 mb-1">Pilih Polis Asuransi:</label>
                                <select name="pasien_asuransi_id" required class="w-full rounded-lg border-gray-300 text-xs">
                                    @foreach($tagihan->kunjungan->pasien->pasienAsuransis as $pa)
                                        <option value="{{ $pa->id }}">
                                            {{ $pa->perusahaanAsuransi->nama_asuransi }} (Polis: {{ $pa->no_polis }} &bull; Prioritas {{ $pa->prioritas }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="space-y-2">
                                <label class="block text-xs font-semibold text-gray-700">Tentukan Nominal Ditanggung Asuransi per Item (Rp):</label>
                                @foreach($tagihan->detailTagihans as $item)
                                    <div class="flex items-center justify-between text-xs bg-white p-2 rounded border border-gray-200">
                                        <span class="font-medium text-gray-800">{{ $item->jenis_biaya }} (Biaya: Rp {{ number_format($item->jumlah_biaya, 0, ',', '.') }})</span>
                                        <input type="number" name="porsi[{{ $item->id }}]" value="{{ (int)$item->jumlah_biaya }}" max="{{ (int)$item->jumlah_biaya }}" min="0"
                                            class="w-32 rounded border-gray-300 text-xs font-mono text-right">
                                    </div>
                                @endforeach
                            </div>

                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold rounded-lg shadow-sm transition">
                                Kirim Pengajuan Klaim ke Asuransi
                            </button>
                        </form>
                    </div>
                @endif

                <!-- Riwayat Klaim Asuransi & Simulasi Respon Asuransi -->
                @if($tagihan->klaimAsuransis->isNotEmpty())
                    <div class="pt-4 border-t border-gray-100 space-y-3">
                        <h4 class="font-bold text-xs uppercase text-gray-500">Berkas Klaim Asuransi Terdaftar:</h4>
                        @foreach($tagihan->klaimAsuransis as $klaim)
                            <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 space-y-3 text-xs">
                                <div class="flex justify-between items-center">
                                    <div>
                                        <span class="font-mono font-bold text-blue-700 text-sm">{{ $klaim->no_klaim }}</span> &bull;
                                        <span class="font-semibold text-gray-800">{{ $klaim->pasienAsuransi->perusahaanAsuransi->nama_asuransi }}</span>
                                    </div>
                                    <span class="px-2.5 py-0.5 rounded-full font-bold
                                        @if($klaim->status === 'Disetujui') bg-emerald-100 text-emerald-800
                                        @elseif($klaim->status === 'Ditolak') bg-rose-100 text-rose-800
                                        @else bg-blue-100 text-blue-800 @endif">
                                        Status Klaim: {{ $klaim->status }}
                                    </span>
                                </div>

                                <!-- Simulasi Keputusan Aktor Perusahaan Asuransi jika status masih 'Diajukan' -->
                                @if($klaim->status === 'Diajukan' && $tagihan->status !== 'Ditutup')
                                    <div class="p-3 bg-white rounded-lg border border-blue-200 space-y-2">
                                        <p class="font-bold text-blue-900">Proses Respon Keputusan Asuransi:</p>
                                        <form method="POST" action="{{ route('kasir.tagihan.hasil-klaim', [$tagihan, $klaim]) }}" class="space-y-2">
                                            @csrf
                                            @foreach($klaim->klaimDetails as $kd)
                                                <div class="flex justify-between items-center text-xs">
                                                    <span>{{ $kd->detailTagihan->jenis_biaya }} (Diajukan: Rp {{ number_format($kd->jumlah_diajukan, 0, ',', '.') }})</span>
                                                    <input type="number" name="hasil[{{ $kd->detail_tagihan_id }}]" value="{{ (int)$kd->jumlah_diajukan }}" max="{{ (int)$kd->jumlah_diajukan }}" min="0"
                                                        class="w-28 rounded border-gray-300 text-xs font-mono text-right" placeholder="Disetujui">
                                                </div>
                                            @endforeach
                                            <div>
                                                <input type="text" name="alasan_penolakan" placeholder="Alasan penolakan / sebagian (opsional)"
                                                    class="w-full rounded border-gray-300 text-xs">
                                            </div>
                                            <div class="flex justify-end pt-1">
                                                <button type="submit" class="px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white rounded font-bold text-xs">
                                                    Simpan Keputusan Hasil Klaim Asuransi
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Panel Pembayaran & Penutupan -->
            <div class="space-y-5">
                <!-- Ringkasan Bayar -->
                <div class="bg-teal-50 p-6 rounded-xl border border-teal-200 text-teal-950 space-y-3">
                    <h4 class="font-bold text-sm uppercase tracking-wider text-teal-800">Total Tanggungan Pasien</h4>
                    <div class="text-3xl font-black text-teal-900 font-mono">
                        Rp {{ number_format($sisaTagihan, 0, ',', '.') }}
                    </div>
                    <div class="text-xs text-teal-700 space-y-1 pt-2 border-t border-teal-200">
                        <div class="flex justify-between">
                            <span>Sudah Dibayar (Tunai/Asuransi):</span>
                            <span class="font-mono font-semibold">Rp {{ number_format($sudahDibayar, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Form Kasir Pembayaran Tunai (Activity 9 & Sequence 10) -->
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

                <!-- Tombol Tutup Transaksi (Activity 11 & Sequence 11) -->
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
