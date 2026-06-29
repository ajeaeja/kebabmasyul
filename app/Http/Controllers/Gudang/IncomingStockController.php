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
            ->paginate(10)
            ->withQueryString();

        if (request()->ajax()) {
            return response()->json([
                'html' => view('gudang.incoming_stocks.fragments.table', compact('incomingStocks'))
                    ->render(),
            ]);
        }

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
        $isWithin24Hours = $incomingStock->incoming_date && \Carbon\Carbon::parse($incomingStock->incoming_date)->diffInHours(now()) < 24;

        if (!$user->isOwner() && !$isWithin24Hours) {
            $request->validate([
                'edit_reason' => 'required|string|min:5',
            ], [
                'edit_reason.required' => 'Anda harus memasukkan alasan pengajuan edit data.',
                'edit_reason.min' => 'Alasan pengajuan edit data minimal 5 karakter.',
            ]);

            $originalData = [
                'raw_material_id' => $incomingStock->raw_material_id,
                'quantity' => (float)$incomingStock->quantity,
                'incoming_date' => $incomingStock->incoming_date ? date('Y-m-d', strtotime($incomingStock->incoming_date)) : null,
                'notes' => (string)$incomingStock->notes,
            ];

            $requestedData = $validated;

            $hasChanged = false;
            foreach ($requestedData as $key => $val) {
                $origVal = isset($originalData[$key]) ? (string)$originalData[$key] : '';
                $reqVal = $val !== null ? (string)$val : '';
                if ($key === 'quantity') {
                    if (abs((float)$origVal - (float)$reqVal) > 0.0001) {
                        $hasChanged = true;
                        break;
                    }
                } else if ($origVal !== $reqVal) {
                    $hasChanged = true;
                    break;
                }
            }

            if (!$hasChanged) {
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
