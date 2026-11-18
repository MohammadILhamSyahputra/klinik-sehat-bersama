<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tindakan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TindakanController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $tindakans = Tindakan::query()
            ->when($search, function ($query, $search) {
                $query->where('nama_tindakan', 'like', "%{$search}%")
                    ->orWhere('kode_tindakan', 'like', "%{$search}%");
            })
            ->orderBy('nama_tindakan')
            ->paginate(15)
            ->withQueryString();

        return view('admin.tindakan.index', compact('tindakans', 'search'));
    }

    public function create(): View
    {
        return view('admin.tindakan.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_tindakan' => ['required', 'string', 'max:50', 'unique:tindakans,kode_tindakan'],
            'nama_tindakan' => ['required', 'string', 'max:255'],
            'tarif' => ['required', 'numeric', 'min:0'],
        ]);

        Tindakan::create($validated);

        return redirect()->route('admin.tindakan.index')->with('success', 'Data tindakan berhasil ditambahkan.');
    }

    public function edit(Tindakan $tindakan): View
    {
        return view('admin.tindakan.edit', compact('tindakan'));
    }

    public function update(Request $request, Tindakan $tindakan): RedirectResponse
    {
        $validated = $request->validate([
            'kode_tindakan' => ['required', 'string', 'max:50', 'unique:tindakans,kode_tindakan,' . $tindakan->id],
            'nama_tindakan' => ['required', 'string', 'max:255'],
            'tarif' => ['required', 'numeric', 'min:0'],
        ]);

        $tindakan->update($validated);

        return redirect()->route('admin.tindakan.index')->with('success', 'Data tindakan berhasil diperbarui.');
    }

    public function destroy(Tindakan $tindakan): RedirectResponse
    {
        if ($tindakan->kunjunganTindakans()->exists()) {
            return back()->with('error', 'Tindakan tidak dapat dihapus karena sudah memiliki riwayat kunjungan.');
        }

        $tindakan->delete();

        return redirect()->route('admin.tindakan.index')->with('success', 'Data tindakan berhasil dihapus.');
    }
}

