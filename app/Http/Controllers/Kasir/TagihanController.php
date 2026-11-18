<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\DetailTagihan;
use App\Models\KlaimAsuransi;
use App\Models\PasienAsuransi;
use App\Models\Pembayaran;
use App\Models\ResepDetail;
use App\Models\Tagihan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TagihanController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status');
        $search = $request->input('search');

        $tagihans = Tagihan::with(['kunjungan.pasien', 'kunjungan.dokter.user', 'kasir'])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($search, function ($query, $search) {
                $query->whereHas('kunjungan.pasien', function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                        ->orWhere('no_identitas', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('kasir.tagihan.index', compact('tagihans', 'status', 'search'));
    }

    public function show(Tagihan $tagihan): View
    {
        $tagihan->load([
            'kunjungan.pasien.pasienAsuransis.perusahaanAsuransi',
            'kunjungan.dokter.user',
            'kunjungan.resep.resepDetails.obat',
            'detailTagihans.kunjunganTindakan.tindakan',
            'detailTagihans.resepDetail.obat',
            'pembayarans.kasir',
            'pembayarans.klaimAsuransi',
            'klaimAsuransis.pasienAsuransi.perusahaanAsuransi',
            'klaimAsuransis.klaimDetails.detailTagihan',
        ]);

        $totalBiaya = (float) $tagihan->detailTagihans->sum('jumlah_biaya');
        $totalAsuransi = (float) $tagihan->detailTagihans->sum('ditanggung_asuransi');
        $totalPasien = (float) $tagihan->detailTagihans->sum('ditanggung_pasien');
        $sudahDibayar = (float) $tagihan->pembayarans->sum('jumlah');
        $sisaTagihan = max(0, $totalBiaya - $sudahDibayar);

        return view('kasir.tagihan.show', compact(
            'tagihan',
            'totalBiaya',
            'totalAsuransi',
            'totalPasien',
            'sudahDibayar',
            'sisaTagihan'
        ));
    }

    public function tebusObat(Request $request, Tagihan $tagihan, ResepDetail $resepDetail): RedirectResponse
    {
        $validated = $request->validate([
            'status_tebus' => ['required', 'in:Sudah,Tidak Diserahkan'],
        ]);

        DB::transaction(function () use ($resepDetail, $validated, $tagihan) {
            if ($validated['status_tebus'] === 'Sudah' && $resepDetail->status_tebus !== 'Sudah') {
                $resepDetail->obat->kurangi_stok($resepDetail->jumlah);
            }

            $resepDetail->ubah_status_tebus($validated['status_tebus']);
            $tagihan->hitung_ulang();
        });

        return back()->with('success', 'Status penyerahan obat berhasil diperbarui.');
    }

    public function ajukanKlaim(Request $request, Tagihan $tagihan): RedirectResponse
    {
        $validated = $request->validate([
            'pasien_asuransi_id' => ['required', 'exists:pasien_asuransis,id'],
            'porsi' => ['required', 'array'],
            'porsi.*' => ['required', 'numeric', 'min:0'],
        ]);

        $polis = PasienAsuransi::findOrFail($validated['pasien_asuransi_id']);

        try {
            DB::transaction(function () use ($tagihan, $polis, $validated) {
                // Update porsi asuransi per item detail tagihan
                foreach ($validated['porsi'] as $detailId => $porsiAsuransi) {
                    $detail = $tagihan->detailTagihans()->findOrFail($detailId);
                    $detail->hitung_porsi((float) $porsiAsuransi);
                }

                KlaimAsuransi::ajukan_klaim($tagihan, $polis);
            });
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return back()->with('success', 'Pengajuan klaim asuransi berhasil dikirim.');
    }

    public function prosesHasilKlaim(Request $request, Tagihan $tagihan, KlaimAsuransi $klaim): RedirectResponse
    {
        $validated = $request->validate([
            'hasil' => ['required', 'array'],
            'hasil.*' => ['required', 'numeric', 'min:0'],
            'alasan_penolakan' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($klaim, $validated, $request, $tagihan) {
            $klaim->catat_hasil_klaim($validated['hasil'], $validated['alasan_penolakan'] ?? null);

            // Jika ada yang disetujui, catat sebagai pembayaran via klaim asuransi
            $totalDisetujui = (float) $klaim->klaimDetails()->sum('jumlah_disetujui');
            if ($totalDisetujui > 0) {
                Pembayaran::create([
                    'tagihan_id' => $tagihan->id,
                    'kasir_id' => $request->user()->id,
                    'klaim_asuransi_id' => $klaim->id,
                    'metode' => 'Asuransi',
                    'jumlah' => $totalDisetujui,
                    'tgl_bayar' => now(),
                ]);
            }
        });

        return back()->with('success', 'Hasil klaim asuransi berhasil diproses dan dicatat.');
    }

    public function bayar(Request $request, Tagihan $tagihan): RedirectResponse
    {
        $validated = $request->validate([
            'metode' => ['required', 'in:Tunai,Transfer,Kartu Debit,Kartu Kredit'],
            'jumlah' => ['required', 'numeric', 'min:1'],
            'uang_bayar' => ['nullable', 'numeric'],
        ]);

        try {
            $result = DB::transaction(function () use ($tagihan, $request, $validated) {
                $hasil = Pembayaran::catat_pembayaran(
                    tagihan: $tagihan,
                    kasirId: $request->user()->id,
                    metode: $validated['metode'],
                    jumlah: (float) $validated['jumlah'],
                    klaimAsuransiId: null,
                    uangBayar: isset($validated['uang_bayar']) ? (float) $validated['uang_bayar'] : null
                );

                // Update detail tagihan yang belum lunas
                $sisaBayar = (float) $validated['jumlah'];
                foreach ($tagihan->detailTagihans()->where('status_pembayaran', '!=', 'Lunas')->get() as $item) {
                    if ($sisaBayar <= 0) {
                        break;
                    }
                    $tanggungan = (float) $item->ditanggung_pasien;
                    if ($sisaBayar >= $tanggungan) {
                        $item->ubah_status_pembayaran('Lunas');
                        $sisaBayar -= $tanggungan;
                    }
                }

                return $hasil;
            });
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        $msg = 'Pembayaran berhasil dicatat.';
        if ($validated['metode'] === 'Tunai' && isset($result['kembalian'])) {
            $msg .= ' Kembalian: Rp ' . number_format($result['kembalian'], 0, ',', '.');
        }

        return back()->with('success', $msg);
    }

    public function tutup(Request $request, Tagihan $tagihan): RedirectResponse
    {
        try {
            $tagihan->tutup_transaksi($request->user()->id);
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return redirect()
            ->route('kasir.tagihan.index')
            ->with('success', 'Transaksi tagihan berhasil ditutup.');
    }
}

