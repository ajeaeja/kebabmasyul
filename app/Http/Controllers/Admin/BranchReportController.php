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

        // Fetch active branches
        $branchesQuery = Branch::where('status', 'active');
        $activeBranches = $branchesQuery->orderBy('name', 'asc')->get();

        // Fetch reports for these branches on these dates
        $branchIds = $activeBranches->pluck('id')->toArray();
        $dbReports = BranchReport::with('branch')
            ->whereIn('branch_id', $branchIds)
            ->whereIn('report_date', $dates)
            ->get()
            ->groupBy(function ($item) {
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

        // Calculate comparative table data per branch for the filtered date range
        $branchSummaries = [];
        $chronologicalDates = array_reverse($dates);
        foreach ($activeBranches as $branch) {
            $summary = BranchReport::where('branch_id', $branch->id)
                ->whereBetween('report_date', [$startDate, $endDate])
                ->selectRaw('SUM(cash_setoran) as total_cash, SUM(qris_setoran) as total_qris, SUM(omset) as total_omset, SUM(portions_sold) as total_portions, COUNT(id) as report_count')
                ->first();

            $dailyTrend = [];
            foreach ($chronologicalDates as $d) {
                $key = $branch->id . '_' . $d;
                $report = isset($dbReports[$key]) ? $dbReports[$key]->first() : null;
                $dailyTrend[] = $report ? (float) $report->omset : 0.0;
            }

            $branchSummaries[] = (object) [
                'branch' => $branch,
                'total_cash' => $summary->total_cash ?: 0,
                'total_qris' => $summary->total_qris ?: 0,
                'total_omset' => $summary->total_omset ?: 0,
                'total_portions' => $summary->total_portions ?: 0,
                'avg_omset' => $summary->report_count > 0 ? ($summary->total_omset / $summary->report_count) : 0,
                'daily_trend' => $dailyTrend,
            ];
        }

        return view('admin.branch_reports.index', compact('reports', 'branches', 'branchSummaries'));
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

        $user = Auth::user();
        $isWithin24Hours = $branchReport->report_date && \Carbon\Carbon::parse($branchReport->report_date)->diffInHours(now()) < 24;

        if (!$user->isOwner() && !$isWithin24Hours) {
            $request->validate([
                'edit_reason' => 'required|string|min:5',
            ], [
                'edit_reason.required' => 'Anda harus memasukkan alasan pengajuan edit data.',
                'edit_reason.min' => 'Alasan pengajuan edit data minimal 5 karakter.',
            ]);

            $originalData = [
                'branch_id' => $branchReport->branch_id,
                'report_date' => $branchReport->report_date ? date('Y-m-d', strtotime($branchReport->report_date)) : null,
                'cash_setoran' => (float) $branchReport->cash_setoran,
                'qris_setoran' => (float) $branchReport->qris_setoran,
                'omset' => (float) $branchReport->omset,
                'portions_sold' => (int) $branchReport->portions_sold,
                'notes' => (string) $branchReport->notes,
            ];

            $requestedData = $validated;

            $hasChanged = false;
            foreach ($requestedData as $key => $val) {
                $origVal = isset($originalData[$key]) ? (string) $originalData[$key] : '';
                $reqVal = $val !== null ? (string) $val : '';
                if ($key === 'cash_setoran' || $key === 'qris_setoran' || $key === 'omset') {
                    if (abs((float) $origVal - (float) $reqVal) > 0.0001) {
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
