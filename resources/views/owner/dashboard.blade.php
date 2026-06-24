@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="container-fluid p-0">
    <!-- Red Alert: Missing Daily Reports -->
    @if(count($missingReportBranches) > 0)
        <!-- Mobile/Tablet Alert: Clickable, no list -->
        <div class="d-md-none mb-4">
            <a href="{{ route('branch-reports.index') }}" class="text-decoration-none">
                <div class="alert alert-danger border-0 shadow-sm p-3 m-0" style="border-radius: 16px; animation: pulse-danger-bg 2s infinite; cursor: pointer;">
                    <div class="d-flex align-items-center">
                        <div class="bg-danger text-white rounded-circle p-2 d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; flex-shrink: 0;">
                            <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                        </div>
                        <div>
                            <h6 class="text-dark font-weight-800 m-0" style="font-size: 0.85rem;">🔴 Laporan Harian Terlambat</h6>
                            <p class="text-muted mt-1 mb-0" style="font-size: 0.725rem;">Ada cabang yang belum menyetor pendapatan hari ini. Klik untuk melihat detail.</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Desktop Alert: Original look, full list -->
        <div class="d-none d-md-block mb-4">
            <div class="alert alert-danger border-0 shadow-sm p-4 m-0" style="border-radius: 16px; animation: pulse-danger-bg 2s infinite;">
                <div class="d-flex align-items-start">
                    <div class="bg-danger text-white rounded-circle p-2 d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; flex-shrink: 0;">
                        <i class="bi bi-exclamation-octagon-fill fs-4"></i>
                    </div>
                    <div>
                        <h5 class="text-dark font-weight-800 m-0">🔴 Monitoring Keterlambatan Laporan Cabang</h5>
                        <p class="text-muted mt-1 mb-2" style="font-size: 0.9rem;">Cabang internal berikut belum menyetorkan/menginput laporan harian penjualan untuk hari ini:</p>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($missingReportBranches as $b)
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 font-weight-700 px-3 py-2" style="font-size: 0.85rem;">
                                    <i class="bi bi-shop me-1"></i> {{ $b->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Top KPI Row -->
    <div class="row">
        <!-- Card: Pendapatan Penjualan Hari Ini -->
        <div class="col-6 col-md-6 mb-4">
            <a href="{{ route('branch-reports.index') }}" class="text-decoration-none">
                <div class="card-custom kpi-card-custom h-100 p-4 d-flex flex-column justify-content-between" style="background: linear-gradient(135deg, #1e293b, #0f172a); color: #fff; transition: transform 0.2s; cursor: pointer;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3 kpi-header">
                            <span class="text-white-50 text-uppercase font-weight-700 kpi-card-title" style="font-size: 0.75rem; letter-spacing: 0.5px;">Pendapatan Penjualan Hari Ini</span>
                            <div class="bg-success bg-opacity-20 text-white rounded-circle p-2 d-flex align-items-center justify-content-center kpi-icon-container" style="width: 38px; height: 38px; flex-shrink: 0;">
                                <i class="bi bi-cash-stack"></i>
                            </div>
                        </div>
                        <h3 class="m-0 font-weight-800 text-white kpi-value">Rp {{ number_format($todayInternalOmset, 0, ',', '.') }}</h3>
                    </div>
                    <div class="mt-3 kpi-footer-wrapper">
                        @if($omsetChangeStatus === 'up')
                            <span class="text-success font-weight-700 kpi-card-footer" style="font-size: 0.8rem; color: #10b981 !important;">
                                <i class="bi bi-arrow-up-short fs-5 align-middle"></i> +{{ number_format($omsetChangePercentage, 1, ',', '.') }}% naik dari kemarin
                            </span>
                        @elseif($omsetChangeStatus === 'down')
                            <span class="text-danger font-weight-700 kpi-card-footer" style="font-size: 0.8rem; color: #ef4444 !important;">
                                <i class="bi bi-arrow-down-short fs-5 align-middle"></i> -{{ number_format($omsetChangePercentage, 1, ',', '.') }}% turun dari kemarin
                            </span>
                        @else
                            <span class="text-white-50 font-weight-600 kpi-card-footer" style="font-size: 0.8rem;">
                                <i class="bi bi-dash-circle me-1 align-middle"></i> Stabil dibanding kemarin
                            </span>
                        @endif
                    </div>
                </div>
            </a>
        </div>

        <!-- Card: Mitra Aktif -->
        <div class="col-6 col-md-6 mb-4">
            <a href="{{ route('partners.index') }}" class="text-decoration-none">
                <div class="card-custom kpi-card-custom h-100 p-4 d-flex flex-column justify-content-between" style="transition: transform 0.2s; cursor: pointer;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3 kpi-header">
                            <span class="text-muted text-uppercase font-weight-700 kpi-card-title" style="font-size: 0.75rem; letter-spacing: 0.5px;">Kemitraan Aktif</span>
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-2 d-flex align-items-center justify-content-center kpi-icon-container" style="width: 38px; height: 38px; flex-shrink: 0;">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                        <h3 class="m-0 font-weight-800 text-dark kpi-value">{{ $activePartners }} <span class="fs-6 text-muted kpi-value-span">Mitra Aktif</span></h3>
                    </div>
                    <div class="mt-3 kpi-footer-wrapper d-none d-md-block">
                        <span class="text-muted font-weight-500 kpi-card-footer" style="font-size: 0.8rem;">Dari total {{ $totalPartners }} mitra terdaftar</span>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Pending Edit Requests Alert -->
    @if($pendingEditRequestsCount > 0)
        <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center justify-content-between p-3 mb-4" style="border-radius: 12px;">
            <div class="d-flex align-items-center">
                <i class="bi bi-shield-exclamation me-3 fs-3 text-warning"></i>
                <div>
                    <strong class="text-dark d-block">Persetujuan Edit Data Tertunda</strong>
                    <span class="text-muted" style="font-size: 0.875rem;">Terdapat {{ $pendingEditRequestsCount }} pengajuan perubahan data dari Admin Utama yang memerlukan tinjauan Anda.</span>
                </div>
            </div>
            <a href="{{ route('edit-requests.index') }}" class="btn btn-warning btn-sm font-weight-600 px-3 py-2 rounded-3 text-dark">Tinjau Pengajuan</a>
        </div>
    @endif

    <!-- Charts Row -->
    <div class="row">
        <!-- Chart 1: Omset Cabang Harian/Mingguan/Bulanan -->
        <div class="col-lg-6 mb-4">
            <div class="card-custom p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="m-0 font-weight-700 text-dark">Grafik 1: Tren Pendapatan Penjualan Cabang Internal</h5>
                        <small class="text-muted">Hasil nominal pendapatan gabungan tunai + QRIS</small>
                    </div>
                    <select class="form-select form-select-sm border bg-light font-weight-600 text-dark rounded-3 px-3 py-2" id="periode_select" style="width: auto; cursor: pointer;">
                        <option value="today" {{ $periode === 'today' ? 'selected' : '' }}>Hari Ini</option>
                        <option value="yesterday" {{ $periode === 'yesterday' ? 'selected' : '' }}>Kemarin</option>
                        <option value="last_7_days" {{ $periode === 'last_7_days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                        <option value="last_30_days" {{ $periode === 'last_30_days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                        <option value="bulanan" {{ $periode === 'bulanan' ? 'selected' : '' }}>Bulanan</option>
                    </select>
                </div>
                <div style="height: 300px; position: relative;">
                    <canvas id="branchRevenueChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Chart 2: Tren Pembelian Kuantitas Bahan Baku Mitra -->
        <div class="col-lg-6 mb-4">
            <div class="card-custom p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h5 class="m-0 font-weight-700 text-dark">Grafik 2: Nominal Pembelian Mitra</h5>
                        <small class="text-muted">Berdasarkan total rupiah belanja bahan baku (Rp)</small>
                    </div>
                    <select class="form-select form-select-sm border bg-light font-weight-600 text-dark rounded-3 px-3 py-2" id="partner_select" style="width: auto; cursor: pointer;">
                        <option value="">-- Semua Mitra --</option>
                        @foreach($allPartners as $p)
                            <option value="{{ $p->id }}" {{ $partnerId == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div style="height: 300px; position: relative;">
                    <canvas id="partnerPurchasesChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Safety Stock & Export Module -->
    <div class="row">
        <!-- Safety Stock Alert Panel -->
        <div class="col-lg-7 mb-4">
            <div class="card-custom p-0">
                <div class="card-header-custom d-flex justify-content-between align-items-center bg-transparent">
                    <span class="text-dark font-weight-700"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Daftar Peringatan Stok Gudang (Safety Stock Alert)</span>
                    <span class="badge bg-danger rounded-pill">{{ $safetyAlertCount }} item</span>
                </div>
                <div class="p-3">
                    @if($safetyAlertCount > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead>
                                    <tr class="text-muted" style="font-size: 0.8rem;">
                                        <th>SKU</th>
                                        <th>BAHAN BAKU</th>
                                        <th class="text-center">STOK SEKARANG</th>
                                        <th class="text-center">LIMIT SEFETY</th>
                                        <th>SATUAN</th>
                                        <th>KONDISI</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($safetyStockAlerts as $item)
                                        <tr class="table-danger">
                                            <td><code>{{ $item->sku }}</code></td>
                                            <td class="font-weight-700 text-dark">{{ $item->name }}</td>
                                            <td class="text-center text-danger font-weight-800">{{ (float)$item->stock }}</td>
                                            <td class="text-center text-muted">{{ (float)$item->safety_stock }}</td>
                                            <td><span class="badge bg-light text-dark border">{{ $item->unit }}</span></td>
                                            <td>
                                                @if($item->stock <= 0)
                                                    <span class="badge bg-danger">Habis</span>
                                                @else
                                                    <span class="badge bg-danger alert-pulse">Kritis</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-check-circle-fill text-success fs-1 d-block mb-2"></i>
                            <strong class="text-dark">Persediaan Gudang Aman</strong>
                            <p class="m-0 mt-1" style="font-size: 0.85rem;">Seluruh stok bahan baku berada di atas batas safety stock minimum.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Export Module Panel -->
        <div class="col-lg-5 mb-4">
            <div class="card-custom p-4">
                <h5 class="font-weight-700 text-dark mb-3"><i class="bi bi-cloud-arrow-down-fill text-danger me-2"></i>Modul Ekspor Laporan & Unduh</h5>
                <p class="text-muted d-none d-md-block" style="font-size: 0.85rem;">Unduh data master dan rekapitulasi operasional pusat dalam format file Excel/CSV yang kompatibel.</p>
                
                <div class="d-grid gap-2 mt-3">
                    <a href="{{ route('export.omset') }}" class="btn btn-outline-dark d-flex align-items-center justify-content-between p-2 rounded-3">
                        <span><i class="bi bi-file-earmark-spreadsheet-fill text-success me-2 fs-5"></i> Rekap Pendapatan Penjualan Cabang</span>
                        <i class="bi bi-download"></i>
                    </a>
                    
                    <a href="{{ route('export.pembelian') }}" class="btn btn-outline-dark d-flex align-items-center justify-content-between p-2 rounded-3">
                        <span><i class="bi bi-cart-check-fill text-primary me-2 fs-5"></i> Total Pembelian per Mitra</span>
                        <i class="bi bi-download"></i>
                    </a>

                    <a href="{{ route('export.stok') }}" class="btn btn-outline-dark d-flex align-items-center justify-content-between p-2 rounded-3">
                        <span><i class="bi bi-box-fill text-warning me-2 fs-5"></i> Sisa Stok Gudang</span>
                        <i class="bi bi-download"></i>
                    </a>

                    <a href="{{ route('export.mitra') }}" class="btn btn-outline-dark d-flex align-items-center justify-content-between p-2 rounded-3">
                        <span><i class="bi bi-people-fill text-info me-2 fs-5"></i> Mitra Aktif & MOU MOU Kontrak</span>
                        <i class="bi bi-download"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Click Drill-down Modal Detail -->
<div class="modal fade" id="drillDownModal" tabindex="-1" aria-labelledby="drillDownModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header border-bottom py-3">
                <h5 class="modal-title font-weight-700 text-dark" id="drillDownModalLabel">Detail Data Grafik</h5>
                <button type="button" class="btn-close" data-bs-dismiss="false" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" id="drillDownModalBody">
                <!-- Loaded dynamically by JS -->
            </div>
            <div class="modal-footer border-top py-2">
                <button type="button" class="btn btn-light rounded-3 font-weight-600" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Drilldown modal initializer
    const drillDownModal = new bootstrap.Modal(document.getElementById('drillDownModal'));
    const modalBody = document.getElementById('drillDownModalBody');

    // 1. Line Chart: Omset Cabang Internal
    const ctxRevenue = document.getElementById('branchRevenueChart').getContext('2d');
    const branchChartData = @json($chartData);
    
    new Chart(ctxRevenue, {
        type: 'line',
        data: branchChartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            onClick: () => {
                window.location.href = "{{ route('branch-reports.index') }}";
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 15,
                        font: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { family: "'Plus Jakarta Sans', sans-serif", weight: '500' } }
                },
                y: {
                    grid: { color: '#f1f5f9' },
                    ticks: {
                        font: { family: "'Plus Jakarta Sans', sans-serif" },
                        callback: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID', { maximumSignificantDigits: 3 }).format(value);
                        }
                    }
                }
            }
        }
    });

    // 2. Line Chart: Partner Purchase Trends
    const ctxPartner = document.getElementById('partnerPurchasesChart').getContext('2d');
    const partnerChartData = @json($partnerChart);
    
    new Chart(ctxPartner, {
        type: 'line',
        data: partnerChartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            onClick: () => {
                window.location.href = "{{ route('orders.index') }}";
            },
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 15,
                        font: {
                            family: "'Plus Jakarta Sans', sans-serif",
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    padding: 12,
                    callbacks: {
                        label: function(context) {
                            let label = context.dataset.label || '';
                            if (label) {
                                label += ': ';
                            }
                            if (context.parsed.y !== null) {
                                label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                            }
                            return label;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { family: "'Plus Jakarta Sans', sans-serif", weight: '500' } }
                },
                y: {
                    grid: { color: '#f1f5f9' },
                    ticks: {
                        font: { family: "'Plus Jakarta Sans', sans-serif" },
                        callback: function(value) {
                            return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                        }
                    }
                }
            }
        }
    });

    // Dropdown change handlers to submit automatically
    document.getElementById('periode_select').addEventListener('change', function() {
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('periode', this.value);
        window.location.href = window.location.pathname + '?' + urlParams.toString();
    });

    document.getElementById('partner_select').addEventListener('change', function() {
        const urlParams = new URLSearchParams(window.location.search);
        if (this.value) {
            urlParams.set('partner_id', this.value);
        } else {
            urlParams.delete('partner_id');
        }
        window.location.href = window.location.pathname + '?' + urlParams.toString();
    });
</script>

<style>
    @keyframes pulse-danger-bg {
        0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.2); }
        70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }

    @media (max-width: 767.98px) {
        .kpi-card-custom {
            padding: 1rem !important;
        }
        .kpi-card-custom .kpi-value {
            font-size: 1.1rem !important;
        }
        .kpi-card-custom .kpi-value-span {
            font-size: 0.75rem !important;
        }
        .kpi-icon-container {
            width: 32px !important;
            height: 32px !important;
        }
        .kpi-icon-container i {
            font-size: 0.85rem !important;
        }
        .kpi-card-title {
            font-size: 0.65rem !important;
        }
        .kpi-card-footer {
            font-size: 0.7rem !important;
        }
        .kpi-header {
            margin-bottom: 0.5rem !important;
        }
        .kpi-footer-wrapper {
            margin-top: 0.5rem !important;
        }
    }
</style>
@endsection
