<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use Illuminate\Http\Request;

class PeriodeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $semua_periode = Periode::all();
        // $semua_periode = Periode::orderBy('id', 'desc')->get();

        return view('periode.index', compact('semua_periode'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('periode.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'periode' => ['required', 'string', 'max:16', 'min:15'],
            'singkatan' => ['required', 'string', 'max:3', 'min:3', 'unique:periode,singkatan'],
        ], [
            'periode.required' => 'Nama periode wajib diisi.',
            'singkatan.required' => 'Singkatan wajib diisi.',
            'singkatan.unique' => 'Singkatan sudah dipakai periode lain.',
            'periode.max' => 'Nama periode maksimal 16 karakter.',
            'periode.min' => 'Nama periode minimal 15 karakter.',
            'singkatan.max' => 'Nama singkatan maksimal 3 karakter.',
            'singkatan.min' => 'Nama singkatan minimal 3 karakter.',
        ]);

        $data['status'] = $request->boolean('status');

        Periode::create($data);

        return redirect()->route('periode.index')
            ->with('success', 'Periode berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Periode $periode)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Periode $periode)
    {
        return view('periode.edit', compact('periode'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Periode $periode)
    {
        $data = $request->validate([
            'periode' => ['required', 'string', 'max:50'],
            'singkatan' => ['required', 'string', 'max:10', 'unique:periode,singkatan,' . $periode->id],
        ], [
            'periode.required' => 'Nama periode wajib diisi.',
            'singkatan.required' => 'Singkatan wajib diisi.',
            'singkatan.unique' => 'Singkatan sudah dipakai periode lain.',
        ]);

        $data['status'] = $request->boolean('status');

        $periode->update($data);

        return redirect()->route('periode.index')
            ->with('success', 'Periode berhasil diubah.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Periode $periode)
    {

        $periode->delete();

        return redirect()->route('periode.index')
            ->with('success', 'Periode berhasil dihapus.');
    }
}
