<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\EditRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PartnerController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'role:owner,admin',
            new Middleware('role:admin', except: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of partners.
     */
    public function index(Request $request)
    {
        $query = Partner::query();

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('owner_name', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $partners = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
        return view('admin.partners.index', compact('partners'));
    }

    /**
     * Show the form for creating a new partner.
     */
    public function create()
    {
        return view('admin.partners.create');
    }

    /**
     * Store a newly created partner in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'jenis_paket' => 'required|in:Silver,Gold,Platinum',
            'join_date' => 'required|date',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
            'mou_document' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($request->hasFile('mou_document')) {
            $file = $request->file('mou_document');
            $filename = time() . '_' . $file->getClientOriginalName();
            if (!file_exists(public_path('uploads/mou'))) {
                mkdir(public_path('uploads/mou'), 0777, true);
            }
            $file->move(public_path('uploads/mou'), $filename);
            $validated['mou_path'] = 'uploads/mou/' . $filename;
        }

        Partner::create($validated);

        return redirect()->route('partners.index')->with('success', 'Data mitra baru berhasil ditambahkan.');
    }

    public function show(Partner $partner, Request $request)
    {
        $query = $partner->orders()->with('items.rawMaterial');

        if ($request->filled('filter')) {
            if ($request->filter === 'today') {
                $query->whereDate('order_date', today());
            } elseif ($request->filter === 'last_7_days') {
                $query->where('order_date', '>=', now()->subDays(7)->toDateString());
            } elseif ($request->filter === 'last_30_days') {
                $query->where('order_date', '>=', now()->subDays(30)->toDateString());
            }
        }

        $orders = $query->orderBy('id', 'desc')->paginate(5)->withQueryString();
        return view('admin.partners.show', compact('partner', 'orders'));
    }



    /**
     * Show the form for editing the specified partner.
     */
    public function edit(Partner $partner)
    {
        return view('admin.partners.edit', compact('partner'));
    }

    /**
     * Update the specified partner in storage.
     */
    public function update(Request $request, Partner $partner)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'owner_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string',
            'jenis_paket' => 'required|in:Silver,Gold,Platinum',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
            'mou_document' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        // Keep existing path by default
        $validated['mou_path'] = $partner->mou_path;

        $user = Auth::user();

        if (!$user->isOwner()) {
            $request->validate([
                'edit_reason' => 'required|string|min:5',
            ], [
                'edit_reason.required' => 'Anda harus memasukkan alasan pengajuan edit data.',
                'edit_reason.min' => 'Alasan pengajuan edit data minimal 5 karakter.',
            ]);

            // Save file uploaded temporarily
            if ($request->hasFile('mou_document')) {
                $file = $request->file('mou_document');
                $filename = time() . '_' . $file->getClientOriginalName();
                if (!file_exists(public_path('uploads/mou/temp'))) {
                    mkdir(public_path('uploads/mou/temp'), 0777, true);
                }
                $file->move(public_path('uploads/mou/temp'), $filename);
                $validated['mou_path'] = 'uploads/mou/temp/' . $filename;
            }

            $originalData = $partner->only(['name', 'owner_name', 'phone', 'address', 'jenis_paket', 'status', 'notes', 'mou_path']);
            $requestedData = $validated;

            // Check if there are actual changes
            if ($originalData == $requestedData) {
                return back()->withErrors(['message' => 'Tidak ada perubahan data yang dideteksi.'])->withInput();
            }

            EditRequest::create([
                'user_id' => Auth::id(),
                'model_type' => Partner::class,
                'model_id' => $partner->id,
                'original_data' => $originalData,
                'requested_data' => $requestedData,
                'reason' => $request->input('edit_reason'),
                'status' => 'pending',
            ]);

            return redirect()->route('partners.index')->with('success', 'Pengajuan edit data mitra berhasil dibuat dan menunggu persetujuan Owner.');
        }

        // Direct update (Owner or within 24 hours)
        if ($request->hasFile('mou_document')) {
            $file = $request->file('mou_document');
            $filename = time() . '_' . $file->getClientOriginalName();
            if (!file_exists(public_path('uploads/mou'))) {
                mkdir(public_path('uploads/mou'), 0777, true);
            }
            $file->move(public_path('uploads/mou'), $filename);
            $validated['mou_path'] = 'uploads/mou/' . $filename;
        }

        $partner->update($validated);
        return redirect()->route('partners.index')->with('success', 'Data mitra berhasil diperbarui.');
    }

    /**
     * Remove/Deactivate the specified partner.
     */
    public function destroy(Partner $partner)
    {
        // soft-deactivation: change status to inactive
        $partner->update(['status' => 'inactive']);
        return redirect()->route('partners.index')->with('success', 'Mitra berhasil dinonaktifkan.');
    }
}
