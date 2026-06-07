<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\EditRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class BranchController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'role:owner,admin',
            new Middleware('role:admin', except: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of branches.
     */
    public function index(Request $request)
    {
        $query = Branch::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $branches = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();
        return view('admin.branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new branch.
     */
    public function create()
    {
        return view('admin.branches.create');
    }

    /**
     * Store a newly created branch in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'opened_date' => 'required|date',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
            'pengelola_cabang' => 'nullable|string|max:255',
        ]);

        $validated['type'] = 'internal';

        Branch::create($validated);

        return redirect()->route('branches.index')->with('success', 'Data cabang baru berhasil ditambahkan.');
    }

    /**
     * Display the specified branch with its reports.
     */
    public function show(Branch $branch)
    {
        $reports = $branch->reports()->orderBy('report_date', 'desc')->paginate(10);
        return view('admin.branches.show', compact('branch', 'reports'));
    }

    /**
     * Show the form for editing the specified branch.
     */
    public function edit(Branch $branch)
    {
        return view('admin.branches.edit', compact('branch'));
    }

    /**
     * Update the specified branch in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'opened_date' => 'required|date',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
            'pengelola_cabang' => 'nullable|string|max:255',
        ]);

        $validated['type'] = 'internal';

        // Enforce 24-hour edit limit logic
        $user = Auth::user();
        $isOlderThan24Hours = $branch->created_at->diffInHours(now()) > 24;

        if (!$user->isOwner() && $isOlderThan24Hours) {
            $request->validate([
                'edit_reason' => 'required|string|min:5',
            ], [
                'edit_reason.required' => 'Anda harus memasukkan alasan pengajuan edit data.',
                'edit_reason.min' => 'Alasan pengajuan edit data minimal 5 karakter.',
            ]);

            $originalData = $branch->only(['name', 'address', 'opened_date', 'status', 'notes', 'pengelola_cabang']);
            $requestedData = $validated;

            if ($originalData == $requestedData) {
                return back()->withErrors(['message' => 'Tidak ada perubahan data yang dideteksi.'])->withInput();
            }

            EditRequest::create([
                'user_id' => Auth::id(),
                'model_type' => Branch::class,
                'model_id' => $branch->id,
                'original_data' => $originalData,
                'requested_data' => $requestedData,
                'reason' => $request->input('edit_reason'),
                'status' => 'pending',
            ]);

            return redirect()->route('branches.index')->with('success', 'Pengajuan edit data cabang berhasil dibuat dan menunggu persetujuan Owner.');
        }

        // Direct update (Owner or within 24 hours)
        $branch->update($validated);
        return redirect()->route('branches.index')->with('success', 'Data cabang berhasil diperbarui.');
    }

    /**
     * Remove/Deactivate the specified branch.
     */
    public function destroy(Branch $branch)
    {
        // soft-deactivation: change status to inactive
        $branch->update(['status' => 'inactive']);
        return redirect()->route('branches.index')->with('success', 'Cabang berhasil dinonaktifkan.');
    }
}
