<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $unit = Unit::all();
        return view('unit', [
            'Unit' => $unit,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_unit' => 'required|string|max:255',
        ]);

        Unit::create([
            'nama_unit' => $request->nama_unit,
        ]);

        return redirect()->route('units.index')->with('success', 'Unit berhasil ditambahkan.');
    }

    // Method untuk update kategori
    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_unit' => 'required|string|max:255',
        ]);

        $unit = Unit::findOrFail($id);
        $unit->update($request->all());

        return redirect()->route('units.index')->with('success', 'Unit berhasil diubah.');
    }

    // Method untuk menghapus unit
    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);
        $unit->delete();

        return redirect()->route('units.index')->with('success', 'Unit berhasil dihapus.');
    }
}
