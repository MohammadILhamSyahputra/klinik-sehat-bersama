<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use App\Models\JadwalPraktik;
use App\Models\KehadiranDokter;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DokterController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $dokters = Dokter::with(['user', 'jadwalPraktiks'])
            ->when($search, function ($query, $search) {
                $query->where('spesialisasi', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.dokter.index', compact('dokters', 'search'));
    }

    public function create(): View
    {
        return view('admin.dokter.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'spesialisasi' => ['required', 'string', 'max:100'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'no_telp' => $validated['no_telp'] ?? null,
            'peran' => User::PERAN_DOKTER,
        ]);

        $user->dokter()->create([
            'spesialisasi' => $validated['spesialisasi'],
        ]);

        return redirect()
            ->route('admin.dokter.index')
            ->with('success', 'Dokter berhasil ditambahkan.');
    }

    public function show(Dokter $dokter): View
    {
        $dokter->load(['user', 'jadwalPraktiks', 'kehadiranDokters' => fn ($q) => $q->latest('tanggal')->limit(10)]);

        return view('admin.dokter.show', compact('dokter'));
    }

    public function edit(Dokter $dokter): View
    {
        $dokter->load('user');

        return view('admin.dokter.edit', compact('dokter'));
    }

    public function update(Request $request, Dokter $dokter): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($dokter->user_id)],
            'no_telp' => ['nullable', 'string', 'max:20'],
            'spesialisasi' => ['required', 'string', 'max:100'],
            'password' => ['nullable', 'string', 'min:8'],
        ]);

        $userData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'no_telp' => $validated['no_telp'] ?? null,
        ];

        if (! empty($validated['password'])) {
            $userData['password'] = $validated['password'];
        }

        $dokter->user->update($userData);
        $dokter->update(['spesialisasi' => $validated['spesialisasi']]);

        return redirect()
            ->route('admin.dokter.show', $dokter)
            ->with('success', 'Data dokter berhasil diperbarui.');
    }

    public function storeJadwal(Request $request, Dokter $dokter): RedirectResponse
    {
        $validated = $request->validate([
            'hari' => ['required', 'in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu'],
            'jam_mulai' => ['required', 'date_format:H:i'],
            'jam_selesai' => ['required', 'date_format:H:i', 'after:jam_mulai'],
        ]);

        $dokter->jadwalPraktiks()->create($validated);

        return back()->with('success', 'Jadwal praktik dokter berhasil ditambahkan.');
    }

    public function destroyJadwal(Dokter $dokter, JadwalPraktik $jadwal): RedirectResponse
    {
        $jadwal->delete();

        return back()->with('success', 'Jadwal praktik dokter berhasil dihapus.');
    }

    public function setKehadiran(Request $request, Dokter $dokter): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal' => ['required', 'date'],
            'status' => ['required', 'in:Hadir,Tidak Hadir'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ]);

        $kehadiran = KehadiranDokter::firstOrNew([
            'dokter_id' => $dokter->id,
            'tanggal' => $validated['tanggal'],
        ]);

        $kehadiran->ubah_status($validated['status'], $validated['keterangan'] ?? null);

        return back()->with('success', 'Status kehadiran dokter berhasil dicatat.');
    }
}

