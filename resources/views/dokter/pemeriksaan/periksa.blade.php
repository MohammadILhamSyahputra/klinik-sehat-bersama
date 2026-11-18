<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center space-x-3">
                <a href="{{ route('dokter.pemeriksaan.antrian') }}" class="text-gray-400 hover:text-gray-600 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                        Lembar Pemeriksaan Pasien (No Antrian #{{ $kunjungan->no_antrian }})
                    </h2>
                    <p class="text-sm text-gray-500">Pasien: <strong class="text-gray-900">{{ $kunjungan->pasien->nama }}</strong> ({{ $kunjungan->pasien->tgl_lahir->age }} Thn) &bull; dr. {{ $dokter->user->name }}</p>
                </div>
            </div>
            <div>
                <span class="px-3 py-1 rounded-full text-xs font-bold
                    @if($kunjungan->status === 'Selesai') bg-emerald-100 text-emerald-800
                    @elseif($kunjungan->status === 'Dipanggil') bg-amber-100 text-amber-800
                    @else bg-blue-100 text-blue-800 @endif">
                    Status: {{ $kunjungan->status }}
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

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Kolom Kiri: Riwayat Pasien -->
            <div class="space-y-6">
                <!-- Info Pasien -->
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm space-y-3">
                    <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-2 text-sm uppercase tracking-wide">Data Identitas Pasien</h3>
                    <div class="text-xs space-y-2 text-gray-600">
                        <div><strong class="text-gray-800">Nama:</strong> {{ $kunjungan->pasien->nama }}</div>
                        <div><strong class="text-gray-800">NIK:</strong> {{ $kunjungan->pasien->no_identitas }}</div>
                        <div><strong class="text-gray-800">Tanggal Lahir:</strong> {{ $kunjungan->pasien->tgl_lahir->format('d/m/Y') }} ({{ $kunjungan->pasien->tgl_lahir->age }} thn)</div>
                        <div><strong class="text-gray-800">Keluhan Awal:</strong> {{ $kunjungan->keluhan ?? '-' }}</div>
                    </div>
                </div>

                <!-- Riwayat Medis Sebelumnya -->
                <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm space-y-3">
                    <h3 class="font-bold text-gray-800 border-b border-gray-100 pb-2 text-sm uppercase tracking-wide">Riwayat Medis Terdahulu</h3>
                    <div class="space-y-3 max-h-96 overflow-y-auto text-xs">
                        @forelse($riwayatPasien->where('id', '!=', $kunjungan->id) as $rp)
                            <div class="p-3 rounded-lg bg-gray-50 border border-gray-100 space-y-1">
                                <div class="flex justify-between font-bold text-gray-700">
                                    <span>{{ $rp->tgl_kunjungan->format('d/m/Y') }}</span>
                                    <span>dr. {{ $rp->dokter->user->name }}</span>
                                </div>
                                <div class="text-gray-600"><strong>Hasil:</strong> {{ $rp->hasil_pemeriksaan ?? '-' }}</div>
                                <div class="text-teal-700">
                                    <strong>Diag:</strong>
                                    {{ $rp->kunjunganDiagnoses->pluck('diagnosis.nama_diagnosis')->join(', ') ?: '-' }}
                                </div>
                            </div>
                        @empty
                            <p class="text-gray-400">Belum ada catatan riwayat kunjungan lampau.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Form Rekam Medis (Catatan, Diagnosis, Tindakan, Obat) -->
            <div class="lg:col-span-2 space-y-6">
                <!-- 1. Catatan Hasil Pemeriksaan Dokter -->
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-4">
                    <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-2">1. Hasil Pemeriksaan Fisik & Anamnesis</h3>
                    <form method="POST" action="{{ route('dokter.pemeriksaan.catatan', $kunjungan) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Hasil Temuan Klinis / Pemeriksaan <span class="text-rose-500">*</span></label>
                            <textarea name="hasil_pemeriksaan" rows="3" required placeholder="Hasil observasi fisik, tensi, suhu tubuh, dan diagnosa klinis awal..."
                                class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">{{ old('hasil_pemeriksaan', $kunjungan->hasil_pemeriksaan) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 uppercase mb-1">Catatan Tambahan / Edukasi Pasien</label>
                            <input type="text" name="catatan_tambahan" value="{{ old('catatan_tambahan', $kunjungan->catatan_tambahan) }}"
                                placeholder="Edukasi diet, kontrol ulang dalam 3 hari, dsb..."
                                class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-sm">
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                                Simpan / Perbarui Catatan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- 2. Diagnosis (ICD) & Revisi Diagnosis (Activity 7 & Sequence 9) -->
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-4">
                    <div class="flex justify-between items-center border-b border-gray-100 pb-2">
                        <h3 class="font-bold text-gray-800 text-base">2. Diagnosis Medis & Revisi Diagnosis</h3>
                        <span class="text-xs text-gray-400">Activity Diagram 7 &bull; Sequence Diagram 9</span>
                    </div>

                    @if($kunjungan->status !== 'Selesai')
                        <form method="POST" action="{{ route('dokter.pemeriksaan.diagnosis.store', $kunjungan) }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 bg-gray-50 p-4 rounded-lg border border-gray-100">
                            @csrf
                            <div class="sm:col-span-2">
                                <select name="diagnosis_id" required class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-xs">
                                    <option value="">-- Tambah Diagnosis (ICD-10) --</option>
                                    @foreach($masterDiagnoses as $diag)
                                        <option value="{{ $diag->id }}">{{ $diag->kode_diagnosis }} - {{ $diag->nama_diagnosis }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <select name="jenis" required class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-xs">
                                    <option value="Primer">Primer (Utama)</option>
                                    <option value="Sekunder">Sekunder</option>
                                </select>
                            </div>
                            <div>
                                <button type="submit" class="w-full py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold rounded-lg transition">
                                    + Tambah
                                </button>
                            </div>
                        </form>
                    @endif

                    <div class="space-y-3">
                        @forelse($kunjungan->kunjunganDiagnoses as $kd)
                            <div x-data="{ openRevisi: false }" class="p-3 rounded-lg border border-gray-100 bg-white space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <div>
                                        <span class="font-mono font-bold text-teal-700">{{ $kd->diagnosis->kode_diagnosis }}</span> &mdash;
                                        <span class="font-semibold text-gray-800">{{ $kd->diagnosis->nama_diagnosis }}</span>
                                        <span class="ml-2 px-1.5 py-0.5 rounded bg-blue-100 text-blue-700 font-medium">{{ $kd->jenis }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <button @click="openRevisi = !openRevisi" type="button" class="text-amber-600 hover:text-amber-700 font-semibold underline text-xs">
                                            Revisi Diagnosis
                                        </button>
                                        @if($kunjungan->status !== 'Selesai')
                                            <form method="POST" action="{{ route('dokter.pemeriksaan.diagnosis.destroy', [$kunjungan, $kd]) }}" onsubmit="return confirm('Hapus diagnosis ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-600 hover:underline">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>

                                <!-- Form Revisi Diagnosis (Modal/Dropdown) -->
                                <div x-show="openRevisi" class="p-3 bg-amber-50/60 rounded-lg border border-amber-200 mt-2 space-y-2 text-xs" style="display: none;">
                                    <p class="font-bold text-amber-900">Ubah Diagnosis (Audit Trail Revisi):</p>
                                    <form method="POST" action="{{ route('dokter.pemeriksaan.diagnosis.update', [$kunjungan, $kd]) }}" class="space-y-2">
                                        @csrf
                                        @method('PUT')
                                        <div>
                                            <label class="block text-gray-600 mb-1">Pilih Diagnosis Baru:</label>
                                            <select name="diagnosis_baru_id" required class="w-full rounded border-gray-300 text-xs">
                                                <option value="">-- Pilih Diagnosis Baru --</option>
                                                @foreach($masterDiagnoses as $diag)
                                                    @if($diag->id !== $kd->diagnosis_id)
                                                        <option value="{{ $diag->id }}">{{ $diag->kode_diagnosis }} - {{ $diag->nama_diagnosis }}</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-gray-600 mb-1">Alasan Perubahan Diagnosis <span class="text-rose-500">*</span>:</label>
                                            <input type="text" name="alasan" required placeholder="Contoh: Hasil lab darah menunjukkan tifoid bukan common cold"
                                                class="w-full rounded border-gray-300 text-xs">
                                        </div>
                                        <div class="flex justify-end gap-2 pt-1">
                                            <button type="button" @click="openRevisi = false" class="px-2.5 py-1 bg-gray-200 text-gray-700 rounded text-xs">Batal</button>
                                            <button type="submit" class="px-3 py-1 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded text-xs">Simpan Revisi Diagnosis</button>
                                        </div>
                                    </form>
                                </div>

                                <!-- Riwayat Jejak Revisi Diagnosis Terlampir -->
                                @if($kd->revisiDiagnoses->isNotEmpty())
                                    <div class="pt-2 border-t border-gray-100 text-[11px] text-gray-500 space-y-1">
                                        <span class="font-bold text-gray-700">Audit Trail Revisi:</span>
                                        @foreach($kd->revisiDiagnoses as $rev)
                                            <div class="p-1.5 bg-gray-50 rounded">
                                                Diagnosis diubah dari <strong>"{{ $rev->diagnosis_lama }}"</strong> menjadi <strong>"{{ $rev->diagnosis_baru }}"</strong> oleh dr. {{ $rev->dokter->user->name }} pada {{ $rev->waktu_revisi->format('d/m/Y H:i') }}.
                                                <div class="text-gray-400 italic">Alasan: {{ $rev->alasan }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @empty
                            <p class="text-xs text-gray-400">Belum ada diagnosis yang dimasukkan.</p>
                        @endforelse
                    </div>
                </div>

                <!-- 3. Tindakan Medis -->
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-4">
                    <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-2">3. Tindakan Medis</h3>

                    @if($kunjungan->status !== 'Selesai')
                        <form method="POST" action="{{ route('dokter.pemeriksaan.tindakan.store', $kunjungan) }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 bg-gray-50 p-4 rounded-lg border border-gray-100">
                            @csrf
                            <div class="sm:col-span-2">
                                <select name="tindakan_id" required class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-xs">
                                    <option value="">-- Pilih Tindakan --</option>
                                    @foreach($masterTindakans as $tnd)
                                        <option value="{{ $tnd->id }}">{{ $tnd->nama_tindakan }} (Rp {{ number_format($tnd->tarif, 0, ',', '.') }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <input type="number" name="jumlah" value="1" min="1" required placeholder="Jumlah"
                                    class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-xs">
                            </div>
                            <div>
                                <button type="submit" class="w-full py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold rounded-lg transition">
                                    + Tindakan
                                </button>
                            </div>
                        </form>
                    @endif

                    <div class="space-y-2">
                        @forelse($kunjungan->kunjunganTindakans as $kt)
                            <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100 bg-white text-xs">
                                <div>
                                    <span class="font-semibold text-gray-800">{{ $kt->tindakan->nama_tindakan }}</span>
                                    <span class="text-gray-500">({{ $kt->jumlah }}x @ Rp {{ number_format($kt->tarif_saat_itu, 0, ',', '.') }})</span>
                                </div>
                                <div class="flex items-center space-x-3">
                                    <span class="font-bold text-gray-900">Rp {{ number_format($kt->hitung_biaya_tindakan(), 0, ',', '.') }}</span>
                                    @if($kunjungan->status !== 'Selesai')
                                        <form method="POST" action="{{ route('dokter.pemeriksaan.tindakan.destroy', [$kunjungan, $kt->id]) }}" onsubmit="return confirm('Hapus tindakan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:underline">Hapus</button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400">Belum ada tindakan medis yang ditambahkan.</p>
                        @endforelse
                    </div>
                </div>

                <!-- 4. Resep Obat (Sesuai Activity 6 & Sequence 5) -->
                <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-4">
                    <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-2">4. Resep Obat</h3>

                    @if($kunjungan->status !== 'Selesai')
                        <form method="POST" action="{{ route('dokter.pemeriksaan.obat.store', $kunjungan) }}" class="space-y-3 bg-gray-50 p-4 rounded-lg border border-gray-100">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="sm:col-span-2">
                                    <select name="obat_id" required class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-xs">
                                        <option value="">-- Pilih Obat --</option>
                                        @foreach($masterObats as $obt)
                                            <option value="{{ $obt->id }}">
                                                {{ $obt->nama_obat }} (Stok: {{ $obt->stok }} &bull; Rp {{ number_format($obt->harga, 0, ',', '.') }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <input type="number" name="jumlah" value="1" min="1" required placeholder="Jumlah (strip/btl)"
                                        class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-xs">
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <input type="text" name="aturan_pakai" required placeholder="Aturan pakai, misal: 3x1 tablet sesudah makan"
                                    class="flex-1 rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-xs">
                                <button type="submit" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold rounded-lg transition whitespace-nowrap">
                                    + Tambah Resep
                                </button>
                            </div>
                        </form>
                    @endif

                    <div class="space-y-2">
                        @if($kunjungan->resep && $kunjungan->resep->resepDetails->isNotEmpty())
                            @foreach($kunjungan->resep->resepDetails as $rd)
                                <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100 bg-white text-xs">
                                    <div>
                                        <div class="font-semibold text-gray-800">{{ $rd->obat->nama_obat }} ({{ $rd->jumlah }} pcs)</div>
                                        <div class="text-gray-500">Aturan: {{ $rd->aturan_pakai }}</div>
                                        @if(!$rd->tersedia)
                                            <span class="inline-block px-1.5 py-0.5 rounded bg-rose-100 text-rose-700 font-bold text-[10px]">Stok Apotek Tidak Mencukupi!</span>
                                        @endif
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <span class="font-bold text-gray-900">Rp {{ number_format($rd->obat->harga * $rd->jumlah, 0, ',', '.') }}</span>
                                        @if($kunjungan->status !== 'Selesai')
                                            <form method="POST" action="{{ route('dokter.pemeriksaan.obat.destroy', [$kunjungan, $rd]) }}" onsubmit="return confirm('Hapus obat ini dari resep?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-600 hover:underline">Hapus</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-xs text-gray-400">Belum ada resep obat yang dibuat.</p>
                        @endif
                    </div>
                </div>

                <!-- Tombol Selesaikan Pemeriksaan -->
                @if($kunjungan->status !== 'Selesai')
                    <div class="pt-4 border-t border-gray-100 flex items-center justify-between bg-teal-50/50 p-5 rounded-xl border border-teal-100">
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm">Selesaikan Pemeriksaan?</h4>
                            <p class="text-xs text-gray-500">Pemeriksaan akan ditandai selesai dan tagihan biaya akan otomatis dibuat untuk Kasir.</p>
                        </div>
                        <form method="POST" action="{{ route('dokter.pemeriksaan.selesai', $kunjungan) }}" onsubmit="return confirm('Yakin ingin menyelesaikan pemeriksaan untuk pasien ini?')">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-bold rounded-lg shadow-sm transition">
                                Selesaikan Pemeriksaan &rarr;
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
