<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PerusahaanAsuransi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PerusahaanAsuransiController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $asuransis = PerusahaanAsuransi::query()
            ->when($search, function ($query, $search) {
                $query->where('nama_asuransi', 'like', "%{$search}%");
            })
            ->withCount('pasienAsuransis')
            ->orderBy('nama_asuransi')
            ->paginate(15)
            ->withQueryString();

        return view('admin.asuransi.index', compact('asuransis', 'search'));
    }

    public function create(): View
    {
        return view('admin.asuransi.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_asuransi' => ['required', 'string', 'max:255', 'unique:perusahaan_asuransis,nama_asuransi'],
        ]);

        PerusahaanAsuransi::create($validated);

        return redirect()->route('admin.asuransi.index')->with('success', 'Perusahaan asuransi berhasil ditambahkan.');
    }

    public function edit(PerusahaanAsuransi $asuransi): View
    {
        return view('admin.asuransi.edit', compact('asuransi'));
    }

    public function update(Request $request, PerusahaanAsuransi $asuransi): RedirectResponse
    {
        $validated = $request->validate([
            'nama_asuransi' => ['required', 'string', 'max:255', 'unique:perusahaan_asuransis,nama_asuransi,' . $asuransi->id],
        ]);

        $asuransi->update($validated);

        return redirect()->route('admin.asuransi.index')->with('success', 'Perusahaan asuransi berhasil diperbarui.');
    }

    public function destroy(PerusahaanAsuransi $asuransi): RedirectResponse
    {
        if ($asuransi->pasienAsuransis()->exists()) {
            return back()->with('error', 'Perusahaan asuransi tidak dapat dihapus karena masih terkait dengan polis pasien.');
        }

        $asuransi->delete();

        return redirect()->route('admin.asuransi.index')->with('success', 'Perusahaan asuransi berhasil dihapus.');
    }
}

