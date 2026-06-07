<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchReport;
use App\Models\EditRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class BranchReportController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'role:owner,admin',
            new Middleware('role:admin', except: ['index', 'show']),
        ];
    }

    /**
     * Display a listing of branch reports.
     */
    public function index(Request $request)
    {
        // Set default filter to last_7_days if not specified
        if (!$request->has('date_filter')) {
            $request->merge(['date_filter' => 'last_7_days']);
        }

        $filter = $request->input('date_filter');
        $startDate = null;
        $endDate = null;

        if ($filter === 'today') {
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d');
        } elseif ($filter === 'yesterday') {
            $startDate = date('Y-m-d', strtotime('-1 day'));
            $endDate = date('Y-m-d', strtotime('-1 day'));
        } elseif ($filter === 'last_7_days') {
            $startDate = date('Y-m-d', strtotime('-6 days'));
            $endDate = date('Y-m-d');
        } elseif ($filter === 'last_30_days') {
            $startDate = date('Y-m-d', strtotime('-29 days'));
            $endDate = date('Y-m-d');
        } elseif ($filter === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;
        } else {
            // Semua Waktu
            $minDate = BranchReport::min('report_date');
            $startDate = $minDate ?: date('Y-m-d', strtotime('-29 days'));
            $endDate = date('Y-m-d');
        }

        // Generate all dates in range descending
        $dates = [];
        $current = strtotime($endDate);
        $start = strtotime($startDate);
        while ($current >= $start) {
            $dates[] = date('Y-m-d', $current);
            $current = strtotime('-1 day', $current);
        }

        // Fetch active branches (or filtered branch)
        if ($request->filled('branch_id')) {
            $branchesQuery = Branch::where('id', $request->branch_id);
        } else {
            $branchesQuery = Branch::where('status', 'active');
        }
        $activeBranches = $branchesQuery->orderBy('name', 'asc')->get();

        // Fetch reports for these branches on these dates
        $branchIds = $activeBranches->pluck('id')->toArray();
        $dbReports = BranchReport::with('branch')
            ->whereIn('branch_id', $branchIds)
            ->whereIn('report_date', $dates)
            ->get()
            ->groupBy(function($item) {
                return $item->branch_id . '_' . $item->report_date;
            });

        // Construct grid items for all dates
        $gridItems = [];
        foreach ($dates as $date) {
            foreach ($activeBranches as $branch) {
                $key = $branch->id . '_' . $date;
                $report = isset($dbReports[$key]) ? $dbReports[$key]->first() : null;
                $gridItems[] = (object) [
                    'date' => $date,
                    'branch' => $branch,
                    'report' => $report,
                ];
            }
        }

        // Paginate the grid items: exactly 10 items per page
        $perPage = 10;
        $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage() ?: 1;
        $currentItems = array_slice($gridItems, ($currentPage - 1) * $perPage, $perPage);

        $reports = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentItems,
            count($gridItems),
            $perPage,
            $currentPage,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath(), 'query' => $request->query()]
        );
        
        $branches = Branch::orderBy('name', 'asc')->get();

        // Calculate chart data for internal branch revenue (dynamic based on filter)
        $labels = [];
        $dateRanges = [];

        if ($filter === 'today') {
            $labels[] = date('d M Y');
            $dateRanges[] = [
                'start' => date('Y-m-d') . ' 00:00:00',
                'end' => date('Y-m-d') . ' 23:59:59',
            ];
        } elseif ($filter === 'yesterday') {
            $labels[] = date('d M Y', strtotime('-1 day'));
            $dateRanges[] = [
                'start' => date('Y-m-d', strtotime('-1 day')) . ' 00:00:00',
                'end' => date('Y-m-d', strtotime('-1 day')) . ' 23:59:59',
            ];
        } elseif ($filter === 'last_7_days') {
            for ($i = 6; $i >= 0; $i--) {
                $d = date('Y-m-d', strtotime("-$i days"));
                $labels[] = date('d M', strtotime($d));
                $dateRanges[] = [
                    'start' => $d . ' 00:00:00',
                    'end' => $d . ' 23:59:59',
                ];
            }
        } elseif ($filter === 'last_30_days') {
            for ($i = 29; $i >= 0; $i--) {
                $d = date('Y-m-d', strtotime("-$i days"));
                $labels[] = date('d M', strtotime($d));
                $dateRanges[] = [
                    'start' => $d . ' 00:00:00',
                    'end' => $d . ' 23:59:59',
                ];
            }
        } elseif ($filter === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            $start = strtotime($request->start_date);
            $end = strtotime($request->end_date);
            $diff = ($end - $start) / 86400;
            if ($diff < 0) {
                $filter = 'monthly';
            } else {
                for ($i = 0; $i <= $diff; $i++) {
                    $d = date('Y-m-d', strtotime("+$i days", $start));
                    $labels[] = date('d M', strtotime($d));
                    $dateRanges[] = [
                        'start' => $d . ' 00:00:00',
                        'end' => $d . ' 23:59:59',
                    ];
                }
            }
        } else {
            $filter = 'monthly';
        }

        if ($filter === 'monthly') {
            for ($i = 5; $i >= 0; $i--) {
                $monthStr = date('Y-m', strtotime("-$i months"));
                $labels[] = date('M Y', strtotime($monthStr . '-01'));
                $dateRanges[] = [
                    'start' => $monthStr . '-01 00:00:00',
                    'end' => date('Y-m-t', strtotime($monthStr . '-01')) . ' 23:59:59',
                ];
            }
        }

        $chartBranchRevenue = [];
        foreach ($dateRanges as $range) {
            $sum = BranchReport::whereHas('branch')
                ->whereBetween('report_date', [substr($range['start'], 0, 10), substr($range['end'], 0, 10)])
                ->when($request->filled('branch_id'), function($q) use ($request) {
                    return $q->where('branch_reports.branch_id', $request->branch_id);
                })
                ->sum('omset');
            $chartBranchRevenue[] = (float) $sum;
        }

        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Omset Cabang',
                    'data' => $chartBranchRevenue,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.3,
                    'fill' => true
                ]
            ]
        ];

        return view('admin.branch_reports.index', compact('reports', 'branches', 'chartData'));
    }

    /**
     * Show the form for creating a new report.
     */
    public function create()
    {
        $branches = Branch::orderBy('name', 'asc')->get();
        return view('admin.branch_reports.create', compact('branches'));
    }

    /**
     * Store a newly created report.
     */
    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'report_date' => 'required|date',
            'cash_setoran' => 'required|numeric|min:0',
            'qris_setoran' => 'required|numeric|min:0',
            'portions_sold' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        // Enforce uniqueness of report per branch per date
        $exists = BranchReport::where('branch_id', $request->branch_id)
            ->where('report_date', $request->report_date)
            ->exists();

        if ($exists) {
            return back()->withErrors(['report_date' => 'Laporan omset untuk cabang dan tanggal tersebut sudah pernah diinput. Silakan ajukan edit jika ada kesalahan.'])->withInput();
        }

        $omset = $request->cash_setoran + $request->qris_setoran;

        BranchReport::create([
            'branch_id' => $request->branch_id,
            'report_date' => $request->report_date,
            'cash_setoran' => $request->cash_setoran,
            'qris_setoran' => $request->qris_setoran,
            'omset' => $omset,
            'portions_sold' => $request->portions_sold,
            'notes' => $request->notes,
        ]);

        return redirect()->route('branch-reports.index')->with('success', 'Laporan omset cabang berhasil dicatat.');
    }

    /**
     * Display the specified branch report.
     */
    public function show(BranchReport $branchReport)
    {
        $branchReport->load('branch');
        return view('admin.branch_reports.show', compact('branchReport'));
    }

    /**
     * Show the edit form.
     */
    public function edit(BranchReport $branchReport)
    {
        $branches = Branch::orderBy('name', 'asc')->get();
        return view('admin.branch_reports.edit', compact('branchReport', 'branches'));
    }

    /**
     * Process update: Owner updates directly, Admin creates an EditRequest.
     */
    public function update(Request $request, BranchReport $branchReport)
    {
        $validated = $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'report_date' => 'required|date',
            'cash_setoran' => 'required|numeric|min:0',
            'qris_setoran' => 'required|numeric|min:0',
            'portions_sold' => 'required|integer|min:0',
            'notes' => 'nullable|string',
        ]);

        $validated['omset'] = $request->cash_setoran + $request->qris_setoran;

        // Check for duplicate reports on date edit
        $exists = BranchReport::where('branch_id', $request->branch_id)
            ->where('report_date', $request->report_date)
            ->where('id', '!=', $branchReport->id)
            ->exists();

        if ($exists) {
            return back()->withErrors(['report_date' => 'Sudah ada laporan omset lain untuk cabang dan tanggal tersebut.'])->withInput();
        }

        // Enforce 24-hour edit limit logic
        $user = Auth::user();
        $isOlderThan24Hours = $branchReport->created_at->diffInHours(now()) > 24;

        if (!$user->isOwner() && $isOlderThan24Hours) {
            $request->validate([
                'edit_reason' => 'required|string|min:5',
            ], [
                'edit_reason.required' => 'Anda harus memasukkan alasan pengajuan edit data.',
                'edit_reason.min' => 'Alasan pengajuan edit data minimal 5 karakter.',
            ]);

            $originalData = $branchReport->only(['branch_id', 'report_date', 'cash_setoran', 'qris_setoran', 'omset', 'portions_sold', 'notes']);
            $requestedData = $validated;

            if ($originalData == $requestedData) {
                return back()->withErrors(['message' => 'Tidak ada perubahan data yang dideteksi.'])->withInput();
            }

            EditRequest::create([
                'user_id' => Auth::id(),
                'model_type' => BranchReport::class,
                'model_id' => $branchReport->id,
                'original_data' => $originalData,
                'requested_data' => $requestedData,
                'reason' => $request->edit_reason,
                'status' => 'pending',
            ]);

            return redirect()->route('branch-reports.index')->with('success', 'Pengajuan edit laporan omset berhasil dibuat dan menunggu persetujuan Owner.');
        }

        // Owner flow or within 24 hours -> direct update
        $branchReport->update($validated);
        return redirect()->route('branch-reports.index')->with('success', 'Laporan omset cabang berhasil diperbarui.');
    }

    /**
     * Delete report (Owner only).
     */
    public function destroy(BranchReport $branchReport)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Hanya Admin yang dapat menghapus laporan omset.');
        }

        $branchReport->delete();
        return redirect()->route('branch-reports.index')->with('success', 'Laporan omset berhasil dihapus.');
    }
}
