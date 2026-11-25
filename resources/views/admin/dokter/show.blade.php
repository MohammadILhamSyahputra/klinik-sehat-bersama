<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center space-x-3">
            <a href="{{ route('admin.dokter.index') }}" class="text-gray-400 hover:text-gray-600 transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">
                    dr. {{ $dokter->user->name }}
                </h2>
                <p class="text-sm text-teal-700 font-semibold">{{ $dokter->spesialisasi }} &bull; {{ $dokter->user->email }}</p>
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

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- 1. KELOLA JADWAL PRAKTIK MINGGUAN -->
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-5">
                <h3 class="font-bold text-gray-800 text-base border-b border-gray-100 pb-2">
                    Jadwal Praktik Rutin Mingguan
                </h3>

                <form method="POST" action="{{ route('admin.dokter.jadwal.store', $dokter) }}" class="space-y-3 bg-gray-50 p-4 rounded-lg border border-gray-100">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Hari</label>
                            <select name="hari" required class="w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-xs">
                                <option value="Senin">Senin</option>
                                <option value="Selasa">Selasa</option>
                                <option value="Rabu">Rabu</option>
                                <option value="Kamis">Kamis</option>
                                <option value="Jumat">Jumat</option>
                                <option value="Sabtu">Sabtu</option>
                                <option value="Minggu">Minggu</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Jam Mulai</label>
                            <input type="text" name="jam_mulai" required value="08:00" placeholder="HH:MM"
                                class="jam-24 w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-600 mb-1">Jam Selesai</label>
                            <input type="text" name="jam_selesai" required value="14:00" placeholder="HH:MM"
                                class="jam-24 w-full rounded-lg border-gray-300 focus:border-teal-500 focus:ring-teal-500 text-xs">
                        </div>
                    </div>
                    <button type="submit" class="w-full py-2 bg-teal-600 hover:bg-teal-700 text-white text-xs font-semibold rounded-lg shadow-sm transition">
                        + Tambah Hari Praktik
                    </button>
                </form>

                <div class="space-y-2">
                    @forelse($dokter->jadwalPraktiks as $jadwal)
                        <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100 bg-white text-xs">
                            <div>
                                <span class="font-bold text-gray-800 text-sm">{{ $jadwal->hari }}</span> &bull;
                                <span class="font-mono text-gray-600">{{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }} WIB</span>
                            </div>
                            <form method="POST" action="{{ route('admin.dokter.jadwal.destroy', [$dokter, $jadwal]) }}" onsubmit="return confirm('Hapus jadwal hari {{ $jadwal->hari }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-rose-600 hover:underline">Hapus</button>
                            </form>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400">Belum ada jadwal praktik mingguan.</p>
                    @endforelse
                </div>
            </div>

            <!-- 2. KELOLA PRESENSI & KEHADIRAN HARIAN DOKTER (Sesuai Activity 3) -->
            <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm space-y-5">
                <div class="border-b border-gray-100 pb-2">
                    <h3 class="font-bold text-gray-800 text-base">
                        Kelola Kehadiran / Presensi Dokter (Harian)
                    </h3>
                    <p class="text-xs text-gray-400 mt-0.5">Ubah status kehadiran dokter pada tanggal tertentu (Sesuai Activity Diagram 3).</p>
                </div>

                <form method="POST" action="{{ route('admin.dokter.kehadiran', $dokter) }}" class="space-y-3 bg-amber-50/50 p-4 rounded-lg border border-amber-200">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Tanggal</label>
                            <input type="date" name="tanggal" required value="{{ today()->toDateString() }}"
                                class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-xs">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Status Kehadiran</label>
                            <select name="status" required class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-xs font-bold">
                                <option value="Hadir">Hadir (Sesuai Jadwal)</option>
                                <option value="Tidak Hadir">Tidak Hadir (Izin / Sakit / Cuti)</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Keterangan / Alasan (Jika Tidak Hadir)</label>
                        <input type="text" name="keterangan" placeholder="Contoh: Mengikuti seminar kedokteran / Sakit"
                            class="w-full rounded-lg border-gray-300 focus:border-amber-500 focus:ring-amber-500 text-xs">
                    </div>
                    <button type="submit" class="w-full py-2 bg-amber-600 hover:bg-amber-700 text-white text-xs font-bold rounded-lg shadow-sm transition">
                        Simpan Status Presensi Tanggal Tersebut
                    </button>
                </form>

                <div>
                    <h4 class="font-bold text-xs uppercase text-gray-500 mb-2">Riwayat Presensi Terbaru:</h4>
                    <div class="space-y-2">
                        @forelse($dokter->kehadiranDokters as $kh)
                            <div class="flex items-center justify-between p-3 rounded-lg border border-gray-100 bg-white text-xs">
                                <div>
                                    <span class="font-semibold text-gray-800">{{ $kh->tanggal->format('d/m/Y') }}</span>
                                    @if($kh->keterangan)
                                        <span class="text-gray-500">({{ $kh->keterangan }})</span>
                                    @endif
                                </div>
                                <span class="px-2 py-0.5 rounded-full font-bold text-[11px]
                                    {{ $kh->status === 'Hadir' ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $kh->status }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400">Belum ada riwayat pengecualian kehadiran.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr@4.6.13/dist/flatpickr.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr('.jam-24', {
            enableTime: true,
            noCalendar: true,
            dateFormat: 'H:i',   // nilai yang dikirim ke server, contoh 08:00 atau 19:30
            time_24hr: true,     // pemilih jam 24 jam, tanpa AM/PM
            minuteIncrement: 5,
            allowInput: true
        });
    });
</script>
</x-app-layout>

