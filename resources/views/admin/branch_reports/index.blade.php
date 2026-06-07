@extends('layouts.app')

@section('title', 'Laporan Omset Harian Cabang')
@section('page_title', 'Laporan Omset Harian Cabang')

@section('content')
<div class="container-fluid p-0">
    <!-- Search & Filter Card -->
    <div class="card-custom p-4 mb-4">
        <form action="{{ route('branch-reports.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-4 col-12">
                <label for="branch_id" class="form-label text-muted font-weight-600" style="font-size: 0.75rem;">Pilih Cabang</label>
                <select class="form-select" id="branch_id" name="branch_id">
                    <option value="">-- Semua Cabang --</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ request('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 col-12">
                <label for="date_filter" class="form-label text-muted font-weight-600" style="font-size: 0.75rem;">Periode Tanggal</label>
                <select class="form-select" id="date_filter" name="date_filter" onchange="toggleCustomDates()">
                    <option value="">Semua Waktu</option>
                    <option value="today" {{ request('date_filter') === 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="yesterday" {{ request('date_filter') === 'yesterday' ? 'selected' : '' }}>Kemarin</option>
                    <option value="last_7_days" {{ request('date_filter') === 'last_7_days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                    <option value="last_30_days" {{ request('date_filter') === 'last_30_days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                    <option value="custom" {{ request('date_filter') === 'custom' ? 'selected' : '' }}>Pilih Periode</option>
                </select>
            </div>

            <!-- Custom Dates (will show if custom is selected) -->
            <div class="col-md-3 col-6 custom-date-input" style="display: {{ request('date_filter') === 'custom' ? 'block' : 'none' }};">
                <label for="start_date" class="form-label text-muted font-weight-600" style="font-size: 0.75rem;">Mulai Tanggal</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3 col-6 custom-date-input" style="display: {{ request('date_filter') === 'custom' ? 'block' : 'none' }};">
                <label for="end_date" class="form-label text-muted font-weight-600" style="font-size: 0.75rem;">Sampai Tanggal</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
            </div>

            <div class="col-md-4 col-12 d-grid gap-2 d-md-flex mt-2">
                <button type="submit" class="btn btn-accent flex-grow-1 py-2"><i class="bi bi-funnel me-1"></i> Filter</button>
                <a href="{{ route('branch-reports.index') }}" class="btn btn-light border py-2"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
            </div>
        </form>
    </div>

    @if(Auth::user()->isOwner())
    <!-- Chart Card -->
    <div class="card-custom p-4 mb-4">
        <h6 class="font-weight-700 text-dark mb-3"><i class="bi bi-graph-up text-danger me-2"></i>Grafik Tren Omset Cabang</h6>
        <div style="height: 250px; position: relative;">
            <canvas id="branchReportChart"></canvas>
        </div>
    </div>
    @endif

    <div class="card-custom p-0">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <div>
                <span class="text-dark font-weight-700">Daftar Omset Harian Kedai Cabang</span>
                <p class="m-0 text-muted d-none d-md-block" style="font-size: 0.8rem; font-weight: 400;">Laporan omset disalin dari WhatsApp pusat setelah kedai tutup setiap malam.</p>
            </div>
            
            <div class="d-flex gap-2 align-items-center">
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('branch-reports.create') }}" class="btn btn-accent rounded-3 font-weight-700 px-3 py-2 d-flex align-items-center justify-content-center" title="Input Omset Cabang">
                        <i class="bi bi-wallet2 me-md-1"></i> <span class="d-none d-md-inline">Input Omset Cabang</span>
                    </a>
                @endif
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle font-weight-600 px-3 py-2 rounded-3 d-flex align-items-center justify-content-center" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Export Data">
                        <i class="bi bi-download me-md-1"></i> <span class="d-none d-md-inline">Export Data</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                        <li><a class="dropdown-item" href="{{ route('generic.export', array_merge(['type' => 'branch-reports', 'format' => 'xls'], request()->query())) }}"><i class="bi bi-file-earmark-excel-fill text-success me-2"></i> Export Excel (.xls)</a></li>
                        <li><a class="dropdown-item" href="{{ route('generic.export', array_merge(['type' => 'branch-reports', 'format' => 'doc'], request()->query())) }}"><i class="bi bi-file-earmark-word-fill text-primary me-2"></i> Export Word (.doc)</a></li>
                        <li><a class="dropdown-item" href="{{ route('generic.export', array_merge(['type' => 'branch-reports', 'format' => 'pdf'], request()->query())) }}" target="_blank"><i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i> Export PDF</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-muted" style="font-size: 0.8rem;">
                            <th>NAMA CABANG</th>
                            <th>TANGGAL LAPORAN</th>
                            <th class="text-end">SETORAN TUNAI</th>
                            <th class="text-end">TRANSAKSI QRIS</th>
                            <th class="text-end">TOTAL OMSET</th>
                            <th class="text-center">PORSI TERJUAL</th>
                            <th>CATATAN</th>
                            <th class="text-center" style="width: 150px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $item)
                            @if($item->report)
                                <tr style="font-size: 0.875rem;">
                                    <td class="font-weight-700 text-dark">{{ $item->branch ? $item->branch->name : 'N/A' }}</td>
                                    <td>{{ date('d F Y', strtotime($item->date)) }}</td>
                                    <td class="text-end text-dark">Rp {{ number_format($item->report->cash_setoran, 0, ',', '.') }}</td>
                                    <td class="text-end text-dark">Rp {{ number_format($item->report->qris_setoran, 0, ',', '.') }}</td>
                                    <td class="text-end font-weight-800 text-danger">Rp {{ number_format($item->report->omset, 0, ',', '.') }}</td>
                                    <td class="text-center font-weight-600 text-dark">{{ $item->report->portions_sold }} porsi</td>
                                    <td style="max-width: 200px;" class="text-truncate text-muted">{{ $item->report->notes ?: '-' }}</td>
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="{{ route('branch-reports.show', $item->report->id) }}" class="btn btn-sm btn-outline-info border-0 rounded-circle" title="Detail Laporan">
                                                <i class="bi bi-eye fs-6"></i>
                                            </a>
                                            @if(Auth::user()->isAdmin())
                                                <a href="{{ route('branch-reports.edit', $item->report->id) }}" class="btn btn-sm btn-outline-primary border-0 rounded-circle" title="Edit/Ajukan Edit">
                                                    <i class="bi bi-pencil-square fs-6"></i>
                                                </a>
                                                
                                                <form action="{{ route('branch-reports.destroy', $item->report->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan omset cabang ini secara permanen?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 rounded-circle" title="Hapus">
                                                        <i class="bi bi-trash fs-6"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @else
                                <tr class="table-danger" style="font-size: 0.875rem; background-color: #fdf2f2;">
                                    <td class="font-weight-700 text-danger">{{ $item->branch ? $item->branch->name : 'N/A' }}</td>
                                    <td class="text-danger">{{ date('d F Y', strtotime($item->date)) }}</td>
                                    <td class="text-end text-muted">-</td>
                                    <td class="text-end text-muted">-</td>
                                    <td class="text-end"><span class="badge bg-danger bg-opacity-10 text-danger font-weight-700 px-2 py-1 rounded">Belum Diinput</span></td>
                                    <td class="text-center text-muted">-</td>
                                    <td class="text-muted italic">Belum ada laporan omset</td>
                                    <td class="text-center">
                                        @if(Auth::user()->isAdmin())
                                            <a href="{{ route('branch-reports.create', ['branch_id' => $item->branch->id, 'report_date' => $item->date]) }}" class="btn btn-sm btn-danger rounded-pill px-3 py-1 font-weight-700" style="font-size: 0.75rem;">
                                                <i class="bi bi-plus-circle me-1"></i> Input
                                            </a>
                                        @else
                                            <span class="text-muted italic" style="font-size: 0.8rem;">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Belum ada laporan omset yang diinput.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="mt-4">
                {{ $reports->links() }}
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
    new Chart(ctxReport, {
        type: 'line',
        data: chartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            },
            scales: {
                y: {
                    ticks: {
                        callback: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                }
            }
        }
    });
    @endif
</script>
@endsection
