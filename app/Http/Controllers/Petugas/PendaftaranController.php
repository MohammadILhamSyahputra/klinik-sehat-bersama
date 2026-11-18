<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use App\Models\JadwalPraktik;
use App\Models\Kunjungan;
use App\Models\Pasien;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PendaftaranController extends Controller
{
    public function index(Request $request): View
    {
        $tanggal = $request->input('tanggal', today()->toDateString());
        $status = $request->input('status');

        $kunjungans = Kunjungan::with(['pasien', 'dokter.user', 'petugas'])
            ->whereDate('tgl_kunjungan', $tanggal)
            ->when($status, fn ($q) => $q->where('status', $status))
            ->orderBy('dokter_id')
            ->orderBy('no_antrian')
            ->paginate(20)
            ->withQueryString();

        return view('petugas.pendaftaran.index', compact('kunjungans', 'tanggal', 'status'));
    }

    public function create(Request $request): View
    {
        $pasienId = $request->input('pasien_id');
        $selectedPasien = $pasienId ? Pasien::find($pasienId) : null;
        $tanggal = $request->input('tanggal', today()->toDateString());

        // Dokter yang berjadwal dan hadir pada tanggal pendaftaran
        $dokters = JadwalPraktik::cari_dokter_berjadwal($tanggal);

        return view('petugas.pendaftaran.create', compact('selectedPasien', 'tanggal', 'dokters'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'pasien_id' => ['required', 'exists:pasiens,id'],
            'dokter_id' => ['required', 'exists:dokters,id'],
            'tgl_kunjungan' => ['required', 'date'],
            'keluhan' => ['nullable', 'string'],
        ]);

        $dokter = Dokter::findOrFail($validated['dokter_id']);
        if (! $dokter->hadirPada($validated['tgl_kunjungan'])) {
            return back()->withInput()->withErrors([
                'dokter_id' => 'Dokter yang dipilih tidak berjadwal atau tidak hadir pada tanggal tersebut.',
            ]);
        }

        $validated['petugas_id'] = $request->user()->id;

        try {
            $kunjungan = Kunjungan::simpan_kunjungan($validated);
        } catch (\Throwable $e) {
            return back()->withInput()->withErrors(['general' => $e->getMessage()]);
        }

        return redirect()
            ->route('petugas.pendaftaran.show', $kunjungan)
            ->with('success', 'Pendaftaran kunjungan berhasil disimpan dengan No Antrian: #' . $kunjungan->no_antrian);
    }

    public function show(Kunjungan $kunjungan): View
    {
        $kunjungan->load([
            'pasien.pasienAsuransis.perusahaanAsuransi',
            'dokter.user',
            'petugas',
            'pemindahanDokters.dokterAsal.user',
            'pemindahanDokters.dokterTujuan.user',
        ]);

        return view('petugas.pendaftaran.show', compact('kunjungan'));
    }

    public function batal(Kunjungan $kunjungan): RedirectResponse
    {
        try {
            $kunjungan->batalkan_kunjungan();
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'Kunjungan pasien berhasil dibatalkan.');
    }

    public function pemindahanForm(Kunjungan $kunjungan): View
    {
        $doktersTersedia = JadwalPraktik::cari_dokter_berjadwal($kunjungan->tgl_kunjungan)
            ->where('id', '!=', $kunjungan->dokter_id);

        return view('petugas.pendaftaran.pemindahan', compact('kunjungan', 'doktersTersedia'));
    }

    public function pindahDokter(Request $request, Kunjungan $kunjungan): RedirectResponse
    {
        $validated = $request->validate([
            'dokter_tujuan_id' => ['required', 'exists:dokters,id', 'different:' . $kunjungan->dokter_id],
            'alasan' => ['nullable', 'string', 'max:255'],
        ]);

        $dokterTujuan = Dokter::findOrFail($validated['dokter_tujuan_id']);

        try {
            $kunjungan->pindah_dokter($dokterTujuan, $validated['alasan']);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return redirect()
            ->route('petugas.pendaftaran.show', $kunjungan)
            ->with('success', 'Pasien berhasil dipindahkan ke dr. ' . $dokterTujuan->user->name . ' dengan No Antrian baru: #' . $kunjungan->no_antrian);
    }
}

