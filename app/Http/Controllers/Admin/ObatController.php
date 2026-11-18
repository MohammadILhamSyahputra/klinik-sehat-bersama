<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Obat;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ObatController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->input('search');

        $obats = Obat::query()
            ->when($search, function ($query, $search) {
                $query->where('nama_obat', 'like', "%{$search}%")
                    ->orWhere('kode_obat', 'like', "%{$search}%");
            })
            ->orderBy('nama_obat')
            ->paginate(15)
            ->withQueryString();

        return view('admin.obat.index', compact('obats', 'search'));
    }

    public function create(): View
    {
        return view('admin.obat.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kode_obat' => ['required', 'string', 'max:50', 'unique:obats,kode_obat'],
            'nama_obat' => ['required', 'string', 'max:255'],
            'stok' => ['required', 'integer', 'min:0'],
            'harga' => ['required', 'numeric', 'min:0'],
        ]);

        Obat::create($validated);

        return redirect()->route('admin.obat.index')->with('success', 'Data obat berhasil ditambahkan.');
    }

    public function edit(Obat $obat): View
    {
        return view('admin.obat.edit', compact('obat'));
    }

    public function update(Request $request, Obat $obat): RedirectResponse
    {
        $validated = $request->validate([
            'kode_obat' => ['required', 'string', 'max:50', 'unique:obats,kode_obat,' . $obat->id],
            'nama_obat' => ['required', 'string', 'max:255'],
            'stok' => ['required', 'integer', 'min:0'],
            'harga' => ['required', 'numeric', 'min:0'],
        ]);

        $obat->update($validated);

        return redirect()->route('admin.obat.index')->with('success', 'Data obat berhasil diperbarui.');
    }

    public function destroy(Obat $obat): RedirectResponse
    {
        if ($obat->resepDetails()->exists()) {
            return back()->with('error', 'Obat tidak dapat dihapus karena sudah pernah diresepkan.');
        }

        $obat->delete();

        return redirect()->route('admin.obat.index')->with('success', 'Data obat berhasil dihapus.');
    }
}

