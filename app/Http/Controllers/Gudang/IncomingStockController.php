<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use App\Models\IncomingStock;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class IncomingStockController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'role:owner,gudang',
            new Middleware('role:gudang', only: ['create', 'store']),
        ];
    }

    /**
     * Display a listing of incoming stock.
     */
    public function index()
    {
        $incomingStocks = IncomingStock::with('rawMaterial')
            ->orderBy('incoming_date', 'desc')
            ->orderBy('id', 'desc')
            ->get();
        return view('gudang.incoming_stocks.index', compact('incomingStocks'));
    }

    /**
     * Show the form for creating a new incoming stock transaction.
     */
    public function create()
    {
        $rawMaterials = RawMaterial::orderBy('name', 'asc')->get();
        return view('gudang.incoming_stocks.create', compact('rawMaterials'));
    }

    /**
     * Store a newly created incoming stock in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'raw_material_id' => 'required|exists:raw_materials,id',
            'quantity' => 'required|numeric|min:0.01',
            'incoming_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        IncomingStock::create($validated);

        return redirect()->route('incoming-stocks.index')->with('success', 'Stok masuk berhasil dicatat dan kuantitas bahan baku otomatis ditambahkan.');
    }

    /**
     * Show the form for editing the specified incoming stock.
     */
    public function edit(IncomingStock $incomingStock)
    {
        $rawMaterials = RawMaterial::orderBy('name', 'asc')->get();
        return view('gudang.incoming_stocks.edit', compact('incomingStock', 'rawMaterials'));
    }

    /**
     * Update the specified incoming stock.
     */
    public function update(Request $request, IncomingStock $incomingStock)
    {
        $validated = $request->validate([
            'raw_material_id' => 'required|exists:raw_materials,id',
            'quantity' => 'required|numeric|min:0.01',
            'incoming_date' => 'required|date',
            'notes' => 'nullable|string',
        ]);

        $user = Auth::user();

        // 24 hour rule: if user is not Owner and data is older than 24 hours
        if (!$user->isOwner() && $incomingStock->created_at->diffInHours(now()) > 24) {
            $request->validate([
                'edit_reason' => 'required|string|min:5',
            ], [
                'edit_reason.required' => 'Anda harus memasukkan alasan pengajuan edit data.',
                'edit_reason.min' => 'Alasan pengajuan edit data minimal 5 karakter.',
            ]);

            $originalData = $incomingStock->only(['raw_material_id', 'quantity', 'incoming_date', 'notes']);
            $requestedData = $validated;

            if ($originalData == $requestedData) {
                return back()->withErrors(['message' => 'Tidak ada perubahan data yang dideteksi.'])->withInput();
            }

            \App\Models\EditRequest::create([
                'user_id' => Auth::id(),
                'model_type' => IncomingStock::class,
                'model_id' => $incomingStock->id,
                'original_data' => $originalData,
                'requested_data' => $requestedData,
                'reason' => $request->edit_reason,
                'status' => 'pending',
            ]);

            return redirect()->route('incoming-stocks.index')->with('success', 'Pengajuan edit stok masuk berhasil dibuat dan menunggu persetujuan Owner.');
        }

        // Direct update if Owner OR within 24 hours
        $incomingStock->update($validated);

        return redirect()->route('incoming-stocks.index')->with('success', 'Data stok masuk berhasil diperbarui.');
    }
}
