<?php

namespace App\Http\Controllers;

use App\Models\Information;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InformationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $informations = Information::latest()->get();

        return view('information.index', compact('informations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'file' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('informasi', 'public');
        }

        try {
            Information::create([
                'judul' => $validated['judul'],
                'isi' => $validated['isi'],
                'file_path' => $filePath,
            ]);

            return redirect()->route('information.index')->with('success', 'Informasi berhasil ditambahkan.');
        } catch (\Throwable $e) {
            if ($filePath) {
                Storage::disk('public')->delete($filePath);
            }

            return back()->withErrors(['error' => 'Gagal menyimpan informasi.'])->withInput();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Information $information)
    {
        $validated = $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'file' => 'nullable|file|mimes:pdf|max:5120',
        ]);

        $oldFilePath = $information->file_path;
        $uploadedPath = null;

        if ($request->hasFile('file')) {
            $uploadedPath = $request->file('file')->store('informasi', 'public');
        }

        try {
            $information->update([
                'judul' => $validated['judul'],
                'isi' => $validated['isi'],
                'file_path' => $uploadedPath ?? $oldFilePath,
            ]);

            if ($uploadedPath && $oldFilePath) {
                Storage::disk('public')->delete($oldFilePath);
            }

            return redirect()->route('information.index')->with('success', 'Informasi berhasil diperbarui.');
        } catch (\Throwable $e) {
            if ($uploadedPath) {
                Storage::disk('public')->delete($uploadedPath);
            }

            return back()->withErrors(['error' => 'Gagal memperbarui informasi.'])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Information $information)
    {
        try {
            if ($information->file_path) {
                Storage::disk('public')->delete($information->file_path);
            }

            $information->delete();

            return redirect()->route('information.index')->with('success', 'Informasi berhasil dihapus.');
        } catch (QueryException $e) {
            return redirect()->route('information.index')->with('error', 'Informasi tidak dapat dihapus karena masih digunakan di data/transaksi lain.');
        }
    }
}
