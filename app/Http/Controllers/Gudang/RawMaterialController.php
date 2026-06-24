<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RawMaterialController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'role:owner,gudang,admin',
            new Middleware('role:owner,gudang', except: ['index']),
        ];
    }

    /**
     * Display a listing of the raw materials.
     */
    public function index()
    {
        $materials = RawMaterial::orderBy('name', 'asc')->get();
        return view('gudang.raw_materials.index', compact('materials'));
    }

    /**
     * Show the form for creating a new raw material.
     */
    public function create()
    {
        return view('gudang.raw_materials.create');
    }

    /**
     * Store a newly created raw material in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'sku' => 'required|string|unique:raw_materials,sku|max:50',
            'name' => 'required|string|max:255',
            'stock' => 'required|numeric|min:0',
            'unit' => 'required|string|max:20',
            'safety_stock' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ];

        // Only Owner can set the price
        if (Auth::user()->isOwner()) {
            $rules['price'] = 'required|numeric|min:0';
        }

        $validated = $request->validate($rules);

        if (!Auth::user()->isOwner()) {
            $validated['price'] = 0.00; // default for Gudang
        }

        RawMaterial::create($validated);

        return redirect()->route('raw-materials.index')->with('success', 'Bahan baku baru berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified raw material.
     */
    public function edit(RawMaterial $rawMaterial)
    {
        return view('gudang.raw_materials.edit', compact('rawMaterial'));
    }

    /**
     * Update the specified raw material in storage.
     */
    public function update(Request $request, RawMaterial $rawMaterial)
    {
        $rules = [
            'sku' => 'required|string|max:50|unique:raw_materials,sku,' . $rawMaterial->id,
            'name' => 'required|string|max:255',
            'stock' => 'required|numeric|min:0',
            'unit' => 'required|string|max:20',
            'safety_stock' => 'required|numeric|min:0',
            'status' => 'required|in:active,inactive',
        ];

        if (Auth::user()->isOwner()) {
            $rules['price'] = 'required|numeric|min:0';
        }

        $validated = $request->validate($rules);

        // If Gudang updates, do not alter the price
        if (!Auth::user()->isOwner()) {
            unset($validated['price']);
        }

        $user = Auth::user();

        if (!$user->isOwner()) {
            $request->validate([
                'edit_reason' => 'required|string|min:5',
            ], [
                'edit_reason.required' => 'Anda harus memasukkan alasan pengajuan edit data.',
                'edit_reason.min' => 'Alasan pengajuan edit data minimal 5 karakter.',
            ]);

            $originalData = $rawMaterial->only(['sku', 'name', 'stock', 'unit', 'safety_stock', 'status']);
            $requestedData = $validated;

            if ($originalData == $requestedData) {
                return back()->withErrors(['message' => 'Tidak ada perubahan data yang dideteksi.'])->withInput();
            }

            \App\Models\EditRequest::create([
                'user_id' => Auth::id(),
                'model_type' => RawMaterial::class,
                'model_id' => $rawMaterial->id,
                'original_data' => $originalData,
                'requested_data' => $requestedData,
                'reason' => $request->edit_reason,
                'status' => 'pending',
            ]);

            return redirect()->route('raw-materials.index')->with('success', 'Pengajuan edit bahan baku berhasil dibuat dan menunggu persetujuan Owner.');
        }

        $rawMaterial->update($validated);

        return redirect()->route('raw-materials.index')->with('success', 'Bahan baku berhasil diperbarui.');
    }

    /**
     * Remove the specified raw material from storage (Owner only).
     */
    public function destroy(RawMaterial $rawMaterial)
    {
        if (!Auth::user()->isGudang()) {
            abort(403, 'Hanya Gudang yang dapat menghapus bahan baku.');
        }

        $rawMaterial->delete();
        return redirect()->route('raw-materials.index')->with('success', 'Bahan baku berhasil dihapus.');
    }
}
