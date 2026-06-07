@extends('layouts.app')

@section('title', 'Laporan Omset Harian Cabang')
@section('page_title', 'Laporan Omset Harian Cabang')

@section('content')
@php
    $totalOmset = 0;
    $totalQris = 0;
    $totalPortions = 0;
    $uninputCount = 0;
    
    foreach ($reports as $item) {
        if ($item->report) {
            $totalOmset += $item->report->omset;
            $totalQris += $item->report->qris_setoran;
            $totalPortions += $item->report->portions_sold;
        } else {
            $uninputCount++;
        }
    }
@endphp

<div class="container-fluid p-0">
    <!-- Header Title & Subtitle from Stitch -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h4 class="font-weight-800 text-dark mb-1">Laporan Omset Harian Cabang</h4>
            <p class="text-muted m-0" style="font-size: 0.9rem;">Laporan omset disalin dari WhatsApp pusat setelah kedai tutup setiap malam.</p>
        </div>
        <div class="d-flex gap-2">
            @if(Auth::user()->isAdmin())
                <a href="{{ route('branch-reports.create') }}" class="btn btn-accent rounded-3 font-weight-700 px-4 py-2.5 d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">add</span>
                    <span>Input Omset Cabang</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Search & Filter Card from Stitch -->
    <div class="card-custom p-4 mb-4">
        <form action="{{ route('branch-reports.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-4 col-12">
                <select class="form-select" id="branch_id" name="branch_id">
                    <option value="">-- Semua Cabang --</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 col-12">
                <select class="form-select" id="date_filter" name="date_filter" onchange="toggleCustomDates()">
                    <option value="">Semua Waktu</option>
                    <option value="today" {{ request('date_filter') === 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="yesterday" {{ request('date_filter') === 'yesterday' ? 'selected' : '' }}>Kemarin</option>
                    <option value="last_7_days" {{ request('date_filter') === 'last_7_days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                    <option value="last_30_days" {{ request('date_filter') === 'last_30_days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                    <option value="custom" {{ request('date_filter') === 'custom' ? 'selected' : '' }}>Pilih Periode</option>
                </select>
            </div>

            <!-- Custom Dates Row -->
            <div class="col-12 custom-date-input" style="display: {{ request('date_filter') === 'custom' ? 'block' : 'none' }};">
                <div class="row g-2">
                    <div class="col-md-6">
                        <label for="start_date" class="form-label text-muted font-weight-600" style="font-size: 0.75rem;">Mulai Tanggal</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-6">
                        <label for="end_date" class="form-label text-muted font-weight-600" style="font-size: 0.75rem;">Sampai Tanggal</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
                    </div>
                </div>
            </div>

            <div class="col-12 d-flex justify-content-end gap-2 mt-2">
                <button type="submit" class="btn btn-accent px-4 py-2.5 d-flex align-items-center gap-1 font-weight-700">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span>
                    Filter
                </button>
                <a href="{{ route('branch-reports.index') }}" class="btn btn-light border px-3 py-2.5 d-flex align-items-center gap-1">
                    <span class="material-symbols-outlined text-[18px]">refresh</span>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Dashboard Analytics Grid from Stitch -->
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-6">
            <div class="card-custom p-4 border-start border-4 border-danger h-100" style="border-left-color: #b22204 !important;">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="w-10 h-10 rounded-lg bg-danger bg-opacity-10 d-flex align-items-center justify-content-center text-primary" style="width: 40px; height: 40px;">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">payments</span>
                    </div>
                    <span class="text-[10px] font-bold text-success bg-success bg-opacity-10 px-2 py-0.5 rounded-full">+12%</span>
                </div>
                <p class="text-muted font-weight-700 uppercase mb-1" style="font-size: 0.7rem; tracking-wider: 1px;">TOTAL OMSET</p>
                <h4 class="font-weight-800 text-dark m-0">Rp {{ number_format($totalOmset, 0, ',', '.') }}</h4>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card-custom p-4 border-start border-4 border-secondary h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="w-10 h-10 rounded-lg bg-secondary-fixed d-flex align-items-center justify-content-center text-secondary" style="width: 40px; height: 40px;">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">qr_code_2</span>
                    </div>
                    <span class="text-[10px] font-bold text-success bg-success bg-opacity-10 px-2 py-0.5 rounded-full">Snappy</span>
                </div>
                <p class="text-muted font-weight-700 uppercase mb-1" style="font-size: 0.7rem; tracking-wider: 1px;">TRANSAKSI QRIS</p>
                <h4 class="font-weight-800 text-dark m-0">Rp {{ number_format($totalQris, 0, ',', '.') }}</h4>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card-custom p-4 border-start border-4 border-warning h-100" style="border-left-color: #ffc107 !important;">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="w-10 h-10 rounded-lg bg-warning bg-opacity-10 d-flex align-items-center justify-content-center text-warning" style="width: 40px; height: 40px; color: #ffc107 !important;">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">restaurant_menu</span>
                    </div>
                    <span class="text-[10px] font-bold text-muted bg-light px-2 py-0.5 rounded-full">Porsi</span>
                </div>
                <p class="text-muted font-weight-700 uppercase mb-1" style="font-size: 0.7rem; tracking-wider: 1px;">PORSI TERJUAL</p>
                <h4 class="font-weight-800 text-dark m-0">{{ $totalPortions }} Porsi</h4>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card-custom p-4 border-start border-4 border-danger h-100" style="border-left-color: #dc3545 !important;">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="w-10 h-10 rounded-lg bg-danger bg-opacity-10 d-flex align-items-center justify-content-center text-danger" style="width: 40px; height: 40px;">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">error</span>
                    </div>
                    <span class="text-[10px] font-bold text-danger bg-danger bg-opacity-10 px-2 py-0.5 rounded-full">Kritis</span>
                </div>
                <p class="text-muted font-weight-700 uppercase mb-1" style="font-size: 0.7rem; tracking-wider: 1px;">BELUM DIINPUT</p>
                <h4 class="font-weight-800 text-danger m-0">{{ $uninputCount }} Cabang</h4>
            </div>
        </div>
    </div>

    @if(Auth::user()->isOwner())
    <!-- Chart Card from Stitch -->
    <div class="card-custom p-4 mb-4">
        <div class="d-flex align-items-center gap-3 mb-4">
            <span class="material-symbols-outlined text-primary">show_chart</span>
            <h6 class="font-weight-700 text-dark m-0">Grafik Tren Omset Cabang</h6>
        </div>
        <div style="height: 250px; position: relative;">
            <canvas id="branchReportChart"></canvas>
        </div>
    </div>
    @endif

    <!-- Data Table Card from Stitch -->
    <div class="card-custom p-0">
        <div class="card-header-custom border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
            <div>
                <span class="text-dark font-weight-700">Daftar Omset Harian Kedai Cabang</span>
            </div>
            
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle font-weight-600 px-3 py-2 rounded-3 d-flex align-items-center justify-content-center" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Export Data">
                    <span class="material-symbols-outlined text-[18px] me-1">download</span>
                    <span>Export Data</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                    <li><a class="dropdown-item" href="{{ route('generic.export', array_merge(['type' => 'branch-reports', 'format' => 'xls'], request()->query())) }}"><i class="bi bi-file-earmark-excel-fill text-success me-2"></i> Export Excel (.xls)</a></li>
                    <li><a class="dropdown-item" href="{{ route('generic.export', array_merge(['type' => 'branch-reports', 'format' => 'doc'], request()->query())) }}"><i class="bi bi-file-earmark-word-fill text-primary me-2"></i> Export Word (.doc)</a></li>
                    <li><a class="dropdown-item" href="{{ route('generic.export', array_merge(['type' => 'branch-reports', 'format' => 'pdf'], request()->query())) }}" target="_blank"><i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i> Export PDF</a></li>
                </ul>
            </div>
        </div>
        <div class="p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border-collapse: collapse;">
                    <thead>
                        <tr class="bg-light text-muted uppercase font-weight-700" style="font-size: 0.75rem; border-bottom: 1px solid #f1f5f9;">
                            <th class="py-3 px-6">Nama Cabang</th>
                            <th class="py-3 px-6">Tanggal Laporan</th>
                            <th class="py-3 px-6 text-end">Setoran Tunai</th>
                            <th class="py-3 px-6 text-end">Transaksi QRIS</th>
                            <th class="py-3 px-6 text-center">Total Omset</th>
                            <th class="py-3 px-6 text-center">Porsi Terjual</th>
                            <th class="py-3 px-6">Catatan</th>
                            <th class="py-3 px-6 text-center" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($reports as $item)
                            @if($item->report)
                                <tr class="transition-colors" style="font-size: 0.875rem;">
                                    <td class="py-3.5 px-6 font-semibold text-on-surface">{{ $item->branch ? $item->branch->name : 'N/A' }}</td>
                                    <td class="py-3.5 px-6 text-muted">{{ date('d M Y', strtotime($item->date)) }}</td>
                                    <td class="py-3.5 px-6 text-end text-dark">Rp {{ number_format($item->report->cash_setoran, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-6 text-end text-dark">Rp {{ number_format($item->report->qris_setoran, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-6 text-center font-weight-800 text-danger">Rp {{ number_format($item->report->omset, 0, ',', '.') }}</td>
                                    <td class="py-3.5 px-6 text-center font-weight-700 text-dark">{{ $item->report->portions_sold }} Porsi</td>
                                    <td class="py-3.5 px-6 text-muted text-truncate" style="max-width: 200px;" title="{{ $item->report->notes }}">{{ $item->report->notes ?: '-' }}</td>
                                    <td class="py-3.5 px-6">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="{{ route('branch-reports.show', $item->report->id) }}" class="btn btn-sm btn-light border rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Detail Laporan">
                                                <span class="material-symbols-outlined text-muted" style="font-size: 18px;">visibility</span>
                                            </a>
                                            @if(Auth::user()->isAdmin())
                                                <a href="{{ route('branch-reports.edit', $item->report->id) }}" class="btn btn-sm btn-light border rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Edit">
                                                    <span class="material-symbols-outlined text-muted" style="font-size: 18px;">edit</span>
                                                </a>
                                                
                                                <form action="{{ route('branch-reports.destroy', $item->report->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan omset cabang ini secara permanen?')" class="m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light border rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Hapus">
                                                        <span class="material-symbols-outlined text-danger" style="font-size: 18px;">delete</span>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @else
                                <tr class="bg-red-50 bg-opacity-50 transition-colors" style="font-size: 0.875rem;">
                                    <td class="py-3.5 px-6 font-semibold text-danger">{{ $item->branch ? $item->branch->name : 'N/A' }}</td>
                                    <td class="py-3.5 px-6 text-danger">{{ date('d M Y', strtotime($item->date)) }}</td>
                                    <td class="py-3.5 px-6 text-end text-muted">-</td>
                                    <td class="py-3.5 px-6 text-end text-muted">-</td>
                                    <td class="py-3.5 px-6 text-center">
                                        <span class="badge bg-red-100 text-red-800 border border-red-200 rounded-pill px-3 py-1 font-weight-700" style="background-color: #fee2e2; color: #991b1b; border-color: #fca5a5 !important;">Belum Diinput</span>
                                    </td>
                                    <td class="py-3.5 px-6 text-center text-muted">-</td>
                                    <td class="py-3.5 px-6 text-danger text-opacity-70 italic font-medium">Belum ada laporan omset</td>
                                    <td class="py-3.5 px-6">
                                        @if(Auth::user()->isAdmin())
                                            <a href="{{ route('branch-reports.create', ['branch_id' => $item->branch->id, 'report_date' => $item->date]) }}" class="btn btn-sm btn-accent rounded-pill px-3 py-1 font-weight-700 d-flex align-items-center justify-content-center gap-1 mx-auto" style="font-size: 0.75rem; width: fit-content;">
                                                <span class="material-symbols-outlined text-white" style="font-size: 14px;">add_circle</span> Input
                                            </a>
                                        @else
                                            <span class="text-muted italic d-block text-center" style="font-size: 0.8rem;">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">Belum ada laporan omset yang diinput.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-6 py-3 border-top bg-light d-flex justify-content-between align-items-center">
                <div class="text-muted" style="font-size: 0.85rem;">
                    Menampilkan <span class="font-weight-700 text-dark">{{ $reports->firstItem() ?? 0 }} - {{ $reports->lastItem() ?? 0 }}</span> dari <span class="font-weight-700 text-dark">{{ $reports->total() }}</span> laporan
                </div>
                <div>
                    {{ $reports->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if(Auth::user()->isOwner())
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endif
<script>
    function toggleCustomDates() {
        const dateFilter = document.getElementById('date_filter').value;
        const customDateInputs = document.querySelectorAll('.custom-date-input');
        customDateInputs.forEach(el => {
            if (dateFilter === 'custom') {
                el.style.display = 'block';
            } else {
                el.style.display = 'none';
                el.querySelector('input').value = '';
            }
        });
    }

    @if(Auth::user()->isOwner())
    const ctxReport = document.getElementById('branchReportChart').getContext('2d');
    const chartData = @json($chartData);
    
    // Custom Chart.js Gradient
    const gradient = ctxReport.createLinearGradient(0, 0, 0, 250);
    gradient.addColorStop(0, 'rgba(178, 34, 4, 0.25)');
    gradient.addColorStop(1, 'rgba(178, 34, 4, 0)');

    new Chart(ctxReport, {
        type: 'line',
        data: {
            labels: chartData.labels,
            datasets: chartData.datasets.map(dataset => ({
                ...dataset,
                borderColor: '#b22204',
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointBackgroundColor: '#b22204',
                pointBorderColor: '#ffffff',
                pointHoverRadius: 7,
                pointHoverBackgroundColor: '#b22204',
                pointHoverBorderColor: '#ffffff',
            }))
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    grid: {
                        color: 'rgba(226, 232, 240, 0.5)'
                    },
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
    @endif
</script>
@endsection
