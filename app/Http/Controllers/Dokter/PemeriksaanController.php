<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\Diagnosis;
use App\Models\Dokter;
use App\Models\Kunjungan;
use App\Models\KunjunganDiagnosis;
use App\Models\Obat;
use App\Models\Resep;
use App\Models\ResepDetail;
use App\Models\Tagihan;
use App\Models\Tindakan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PemeriksaanController extends Controller
{
    private function getDokter(Request $request): Dokter
    {
        $dokter = $request->user()->dokter;
        if (! $dokter) {
            abort(403, 'Akun Anda tidak terhubung dengan profil dokter.');
        }

        return $dokter;
    }

    public function antrian(Request $request): View
    {
        $dokter = $this->getDokter($request);
        $tanggal = $request->input('tanggal', today()->toDateString());

        $antrian = Kunjungan::with(['pasien'])
            ->where('dokter_id', $dokter->id)
            ->whereDate('tgl_kunjungan', $tanggal)
            ->orderBy('no_antrian')
            ->get();

        return view('dokter.pemeriksaan.antrian', compact('antrian', 'tanggal', 'dokter'));
    }

    public function panggil(Request $request, Kunjungan $kunjungan): RedirectResponse
    {
        $dokter = $this->getDokter($request);
        if ($kunjungan->dokter_id !== $dokter->id) {
            abort(403);
        }

        try {
            $kunjungan->panggil_pasien();
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return redirect()->route('dokter.pemeriksaan.periksa', $kunjungan);
    }

    public function periksa(Request $request, Kunjungan $kunjungan): View
    {
        $dokter = $this->getDokter($request);
        if ($kunjungan->dokter_id !== $dokter->id) {
            abort(403);
        }

        $kunjungan->load([
            'pasien.pasienAsuransis.perusahaanAsuransi',
            'kunjunganDiagnoses.diagnosis',
            'kunjunganDiagnoses.revisiDiagnoses.dokter.user',
            'kunjunganTindakans.tindakan',
            'resep.resepDetails.obat',
        ]);

        $riwayatPasien = $kunjungan->pasien->tampil_riwayat();
        $masterDiagnoses = Diagnosis::tampil_diagnosis();
        $masterTindakans = Tindakan::tampil_tindakan();
        $masterObats = Obat::tampil_obat();

        return view('dokter.pemeriksaan.periksa', compact(
            'kunjungan',
            'riwayatPasien',
            'masterDiagnoses',
            'masterTindakans',
            'masterObats',
            'dokter'
        ));
    }

    public function simpanCatatan(Request $request, Kunjungan $kunjungan): RedirectResponse
    {
        $dokter = $this->getDokter($request);
        if ($kunjungan->dokter_id !== $dokter->id) {
            abort(403);
        }

        $validated = $request->validate([
            'hasil_pemeriksaan' => ['required', 'string'],
            'catatan_tambahan' => ['nullable', 'string'],
        ]);

        $kunjungan->update($validated);

        return back()->with('success', 'Catatan pemeriksaan berhasil disimpan.');
    }

    public function tambahDiagnosis(Request $request, Kunjungan $kunjungan): RedirectResponse
    {
        $dokter = $this->getDokter($request);
        if ($kunjungan->dokter_id !== $dokter->id) {
            abort(403);
        }

        $validated = $request->validate([
            'diagnosis_id' => ['required', 'exists:diagnoses,id'],
            'jenis' => ['required', 'in:Utama,Sekunder'],
        ]);

        KunjunganDiagnosis::tambah_diagnosis($kunjungan, (int) $validated['diagnosis_id'], $validated['jenis']);

        return back()->with('success', 'Diagnosis berhasil ditambahkan.');
    }

    public function ubahDiagnosis(Request $request, Kunjungan $kunjungan, KunjunganDiagnosis $kunjunganDiagnosis): RedirectResponse
    {
        $dokter = $this->getDokter($request);
        if ($kunjungan->dokter_id !== $dokter->id) {
            abort(403);
        }

        $validated = $request->validate([
            'diagnosis_baru_id' => ['required', 'exists:diagnoses,id', 'different:' . $kunjunganDiagnosis->diagnosis_id],
            'alasan' => ['required', 'string', 'max:255'],
        ]);

        $baru = Diagnosis::findOrFail($validated['diagnosis_baru_id']);
        $kunjunganDiagnosis->ubah_diagnosis($baru, $dokter->id, $validated['alasan']);

        return back()->with('success', 'Diagnosis berhasil diperbarui dan riwayat revisi telah dicatat.');
    }

    public function hapusDiagnosis(Request $request, Kunjungan $kunjungan, KunjunganDiagnosis $kunjunganDiagnosis): RedirectResponse
    {
        $dokter = $this->getDokter($request);
        if ($kunjungan->dokter_id !== $dokter->id) {
            abort(403);
        }

        $kunjunganDiagnosis->delete();

        if ($kunjungan->tagihan) {
            $kunjungan->tagihan->hitung_ulang();
        }

        return back()->with('success', 'Diagnosis berhasil dihapus.');
    }

    public function tambahTindakan(Request $request, Kunjungan $kunjungan): RedirectResponse
    {
        $dokter = $this->getDokter($request);
        if ($kunjungan->dokter_id !== $dokter->id) {
            abort(403);
        }

        $validated = $request->validate([
            'tindakan_id' => ['required', 'exists:tindakans,id'],
            'jumlah' => ['required', 'integer', 'min:1'],
        ]);

        $tindakan = Tindakan::findOrFail($validated['tindakan_id']);
        $kunjungan->kunjunganTindakans()->create([
            'tindakan_id' => $tindakan->id,
            'jumlah' => $validated['jumlah'],
            'tarif_saat_itu' => $tindakan->tarif,
        ]);

        if ($kunjungan->tagihan) {
            $kunjungan->tagihan->hitung_ulang();
        }

        return back()->with('success', 'Tindakan berhasil ditambahkan.');
    }

    public function hapusTindakan(Request $request, Kunjungan $kunjungan, int $tindakanId): RedirectResponse
    {
        $dokter = $this->getDokter($request);
        if ($kunjungan->dokter_id !== $dokter->id) {
            abort(403);
        }

        $kt = $kunjungan->kunjunganTindakans()->findOrFail($tindakanId);
        $kt->delete();

        if ($kunjungan->tagihan) {
            $kunjungan->tagihan->hitung_ulang();
        }

        return back()->with('success', 'Tindakan berhasil dihapus.');
    }

    public function tambahObat(Request $request, Kunjungan $kunjungan): RedirectResponse
    {
        $dokter = $this->getDokter($request);
        if ($kunjungan->dokter_id !== $dokter->id) {
            abort(403);
        }

        $validated = $request->validate([
            'obat_id' => ['required', 'exists:obats,id'],
            'jumlah' => ['required', 'integer', 'min:1'],
            'aturan_pakai' => ['required', 'string', 'max:255'],
        ]);

        $resep = Resep::buat_resep($kunjungan);
        $obat = Obat::findOrFail($validated['obat_id']);

        $detail = ResepDetail::tambah_obat($resep, $obat, (int) $validated['jumlah'], $validated['aturan_pakai']);

        $msg = 'Obat berhasil ditambahkan ke resep.';
        if (! $detail->tersedia) {
            $msg .= ' Peringatan: Stok obat ini saat ini tidak mencukupi di apotek!';
        }

        return back()->with('success', $msg);
    }

    public function hapusObat(Request $request, Kunjungan $kunjungan, ResepDetail $resepDetail): RedirectResponse
    {
        $dokter = $this->getDokter($request);
        if ($kunjungan->dokter_id !== $dokter->id) {
            abort(403);
        }

        $resepDetail->delete();

        if ($kunjungan->tagihan) {
            $kunjungan->tagihan->hitung_ulang();
        }

        return back()->with('success', 'Obat berhasil dihapus dari resep.');
    }

    public function selesaiPemeriksaan(Request $request, Kunjungan $kunjungan): RedirectResponse
    {
        $dokter = $this->getDokter($request);
        if ($kunjungan->dokter_id !== $dokter->id) {
            abort(403);
        }

        if (empty($kunjungan->hasil_pemeriksaan)) {
            return back()->withErrors(['hasil_pemeriksaan' => 'Harap isi dan simpan hasil pemeriksaan sebelum menyelesaikan kunjungan.']);
        }

        if (! $kunjungan->kunjunganDiagnoses()->exists()) {
            return back()->withErrors(['diagnosis' => 'Minimal harus ada 1 diagnosis untuk menyelesaikan pemeriksaan.']);
        }

        DB::transaction(function () use ($kunjungan) {
            $kunjungan->selesaikan_pemeriksaan();
            Tagihan::hitung_tagihan($kunjungan);
        });

        return redirect()
            ->route('dokter.pemeriksaan.antrian')
            ->with('success', 'Pemeriksaan untuk pasien ' . $kunjungan->pasien->nama . ' telah selesai. Tagihan otomatis dibuat.');
    }
}

