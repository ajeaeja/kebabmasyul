@extends('layouts.app')

@section('title', 'Pendapatan Penjualan Harian Cabang')
@section('page_title', 'Pendapatan Penjualan Harian Cabang')

@section('content')
<div class="container-fluid p-0">

    <!-- Search & Filter Card -->
    <div class="card-custom p-4 mb-4">
        <form action="{{ route('branch-reports.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-4 col-12">
                <label for="date_filter" class="form-label text-muted font-weight-600" style="font-size: 0.75rem;">Periode Tanggal</label>
                <select class="form-select" id="date_filter" name="date_filter" onchange="toggleCustomDates()">
                    <option value="">Semua Waktu</option>
                    <option value="today" {{ request('date_filter') === 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="yesterday" {{ request('date_filter') === 'yesterday' ? 'selected' : '' }}>Kemarin</option>
                    <option value="last_7_days" {{ request('date_filter', 'last_7_days') === 'last_7_days' ? 'selected' : '' }}>7 Hari Terakhir</option>
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
    <!-- Comparative Table per Branch -->
    <div class="card-custom p-0 mb-4">
        <div class="card-header-custom bg-transparent border-bottom">
            <span class="text-dark font-weight-700"><i class="bi bi-bar-chart-line-fill text-danger me-2"></i>Perbandingan Pendapatan Penjualan Antar Cabang</span>
        </div>
        <div class="p-3">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-muted" style="font-size: 0.8rem;">
                            <th>NAMA CABANG</th>
                            <th class="text-end" style="width: 25%;">TOTAL PENDAPATAN</th>
                            <th class="text-center" style="width: 35%;">VISUAL TREN</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($branchSummaries as $summary)
                            @php
                                $trend = $summary->daily_trend;
                                $maxVal = count($trend) > 0 ? max($trend) : 0;
                                $minVal = count($trend) > 0 ? min($trend) : 0;
                                $range = $maxVal - $minVal;
                                
                                $width = 240;
                                $height = 35;
                                $points = [];
                                
                                if (count($trend) > 1) {
                                    foreach ($trend as $i => $val) {
                                        $x = ($i / (count($trend) - 1)) * $width;
                                        $y = $height - 3;
                                        if ($range > 0) {
                                            $y = 3 + ($height - 6) * (1 - ($val - $minVal) / $range);
                                        } elseif ($maxVal > 0) {
                                            $y = 3 + ($height - 6) * (1 - $val / $maxVal);
                                        }
                                        $points[] = "$x,$y";
                                    }
                                    $pointsString = implode(' ', $points);
                                } else {
                                    $pointsString = "0,".($height/2)." ".$width.",".($height/2);
                                }
                                
                                $trendColor = '#ef4444'; // Red default
                                if (count($trend) >= 2) {
                                    $first = $trend[0];
                                    $last = end($trend);
                                    if ($last >= $first) {
                                        $trendColor = '#10b981'; // Green
                                    }
                                } else {
                                    $trendColor = '#10b981';
                                }
                            @endphp
                            <tr style="font-size: 0.875rem;">
                                <td class="font-weight-700 text-dark">
                                    <i class="bi bi-shop me-2 text-muted"></i>{{ $summary->branch->name }}
                                </td>
                                <td class="text-end font-weight-800 text-primary" style="font-size: 1rem;">
                                    Rp {{ number_format($summary->total_omset, 0, ',', '.') }}
                                </td>
                                <td class="text-center py-2">
                                    <div class="d-inline-block p-1 bg-light bg-opacity-50 rounded" style="border: 1px dashed rgba(0,0,0,0.05);">
                                        <svg width="{{ $width }}" height="{{ $height }}" style="overflow: visible; display: block;">
                                            <!-- Trend line -->
                                            <polyline
                                                fill="none"
                                                stroke="{{ $trendColor }}"
                                                stroke-width="2.5"
                                                points="{{ $pointsString }}"
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                            />
                                        </svg>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <div class="card-custom p-0">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <div>
                <span class="text-dark font-weight-700">Daftar Pendapatan Penjualan Harian Kedai Cabang</span>
                <p class="m-0 text-muted d-none d-md-block" style="font-size: 0.8rem; font-weight: 400;">Laporan pendapatan penjualan disalin dari WhatsApp pusat setelah kedai tutup setiap malam.</p>
            </div>
            
            <div class="d-flex gap-2 align-items-center">
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('branch-reports.create') }}" class="btn btn-accent rounded-3 font-weight-700 px-3 py-2 d-flex align-items-center justify-content-center" title="Input Pendapatan Penjualan Cabang">
                        <i class="bi bi-wallet2 me-md-1"></i> <span class="d-none d-md-inline">Input Pendapatan Penjualan Cabang</span>
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
                            <th class="text-end">TOTAL PENDAPATAN</th>
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
                                                
                                                <form action="{{ route('branch-reports.destroy', $item->report->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan pendapatan penjualan cabang ini secara permanen?')">
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
                                    <td class="text-muted italic">Belum ada pendapatan penjualan</td>
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
                                <td colspan="8" class="text-center text-muted py-4">Belum ada pendapatan penjualan yang diinput.</td>
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
</script>
@endsection
