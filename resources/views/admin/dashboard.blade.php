@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="container-fluid p-0">
    <!-- Quick Actions Top Bar -->
    <!-- Mobile/Tablet View: Quick Action Icons Grid -->
    <div class="d-md-none mb-4">
        <div class="card-custom p-3 shadow-sm border-0" style="border-radius: 16px; background: linear-gradient(135deg, #ee4d2d, #ff763b); color: #fff;">
            <h6 class="font-weight-800 text-white mb-3"><i class="bi bi-lightning-charge-fill me-1"></i>Pusat Input Cepat WA</h6>
            <div class="row g-2 text-center">
                <div class="col-3">
                    <a href="{{ route('orders.create') }}" class="text-decoration-none">
                        <div class="bg-white rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; transition: transform 0.2s; color: #ee4d2d;">
                            <i class="bi bi-cart-plus-fill fs-5"></i>
                        </div>
                        <span class="d-block text-white mt-2 font-weight-600" style="font-size: 0.65rem; line-height: 1.2;">Pesanan</span>
                    </a>
                </div>
                <div class="col-3">
                    <a href="{{ route('branch-reports.create') }}" class="text-decoration-none">
                        <div class="bg-white rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; transition: transform 0.2s; color: #ee4d2d;">
                            <i class="bi bi-wallet2 fs-5"></i>
                        </div>
                        <span class="d-block text-white mt-2 font-weight-600" style="font-size: 0.65rem; line-height: 1.2;">Omset</span>
                    </a>
                </div>
                <div class="col-3">
                    <a href="{{ route('partners.create') }}" class="text-decoration-none">
                        <div class="bg-white rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; transition: transform 0.2s; color: #ee4d2d;">
                            <i class="bi bi-person-plus-fill fs-5"></i>
                        </div>
                        <span class="d-block text-white mt-2 font-weight-600" style="font-size: 0.65rem; line-height: 1.2;">Mitra</span>
                    </a>
                </div>
                <div class="col-3">
                    <a href="{{ route('branches.create') }}" class="text-decoration-none">
                        <div class="bg-white rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; transition: transform 0.2s; color: #ee4d2d;">
                            <i class="bi bi-shop-window fs-5"></i>
                        </div>
                        <span class="d-block text-white mt-2 font-weight-600" style="font-size: 0.65rem; line-height: 1.2;">Cabang</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Desktop View: Full orange block with buttons -->
    <div class="d-none d-md-block card-custom p-4 mb-4" style="background: linear-gradient(135deg, #ee4d2d, #ff763b); color: #fff;">
        <h5 class="font-weight-700 m-0"><i class="bi bi-lightning-charge-fill me-2"></i>Pusat Input WhatsApp (Single-Gate Center)</h5>
        <p class="m-0 mt-1 opacity-75" style="font-size: 0.875rem;">Salin dan input laporan pesanan bahan baku mitra & omset harian kedai dari WhatsApp ke dalam sistem di bawah ini.</p>
        
        <div class="d-flex flex-wrap gap-2 mt-3">
            <a href="{{ route('orders.create') }}" class="btn btn-light btn-sm font-weight-700 px-3 py-2 text-danger rounded-3">
                <i class="bi bi-cart-plus-fill me-1"></i> Input Pesanan Mitra
            </a>
            <a href="{{ route('branch-reports.create') }}" class="btn btn-light btn-sm font-weight-700 px-3 py-2 text-danger rounded-3">
                <i class="bi bi-wallet2 me-1"></i> Input Omset Cabang
            </a>
            <a href="{{ route('partners.create') }}" class="btn btn-light btn-sm font-weight-700 px-3 py-2 text-danger rounded-3">
                <i class="bi bi-person-plus-fill me-1"></i> Tambah Mitra Baru
            </a>
            <a href="{{ route('branches.create') }}" class="btn btn-light btn-sm font-weight-700 px-3 py-2 text-danger rounded-3">
                <i class="bi bi-shop-window me-1"></i> Tambah Cabang Baru
            </a>
        </div>
    </div>

    <!-- Aggregated Metrics -->
    <div class="row">
        <div class="col-6 col-md-6 mb-4">
            <a href="{{ route('partners.index') }}" class="text-decoration-none">
                <div class="card-custom kpi-card-custom p-4 d-flex justify-content-between align-items-center" style="transition: transform 0.2s; cursor: pointer;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div>
                        <span class="text-muted text-uppercase font-weight-700 kpi-card-title" style="font-size: 0.75rem;">Total Mitra Kemitraan</span>
                        <h3 class="m-0 font-weight-800 mt-2 text-dark kpi-value">{{ $totalPartners }} Mitra</h3>
                    </div>
                    <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-3 kpi-icon-container d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; flex-shrink: 0;">
                        <i class="bi bi-people-fill fs-3"></i>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-6 mb-4">
            <a href="{{ route('branches.index') }}" class="text-decoration-none">
                <div class="card-custom kpi-card-custom p-4 d-flex justify-content-between align-items-center" style="transition: transform 0.2s; cursor: pointer;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                    <div>
                        <span class="text-muted text-uppercase font-weight-700 kpi-card-title" style="font-size: 0.75rem;">Total Cabang Terdaftar</span>
                        <h3 class="m-0 font-weight-800 mt-2 text-dark kpi-value">{{ $totalBranches }} Cabang</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3 kpi-icon-container d-flex align-items-center justify-content-center" style="width: 52px; height: 52px; flex-shrink: 0;">
                        <i class="bi bi-shop fs-3"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Column Left: Recent Inputs -->
        <div class="col-xl-8">
            <!-- Recent Orders -->
            <div class="card-custom p-0 mb-4">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <span class="text-dark font-weight-700"><i class="bi bi-receipt me-2"></i>Pesanan Bahan Baku Terkini (WA Input)</span>
                    <a href="{{ route('orders.index') }}" class="btn btn-link text-danger font-weight-600 p-0 text-decoration-none" style="font-size: 0.85rem;">Semua Pesanan</a>
                </div>
                <div class="p-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-muted" style="font-size: 0.8rem;">
                                    <th>ID</th>
                                    <th>MITRA</th>
                                    <th>TANGGAL</th>
                                    <th class="text-end">TOTAL HARGA</th>
                                    <th class="text-center">STATUS</th>
                                    <th class="text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentOrders as $order)
                                    <tr style="font-size: 0.875rem;">
                                        <td>#{{ $order->id }}</td>
                                        <td class="font-weight-600 text-dark">{{ $order->partner ? $order->partner->name : 'N/A' }}</td>
                                        <td>{{ date('d-m-Y', strtotime($order->order_date)) }}</td>
                                         <td class="text-end font-weight-600">Rp {{ number_format($order->total_price + $order->shipping_cost, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            @if($order->status === 'menunggu_dipacking')
                                                <span class="badge bg-secondary text-white">Menunggu</span>
                                            @elseif($order->status === 'dipacking')
                                                <span class="badge bg-info text-white">Dipacking</span>
                                            @elseif($order->status === 'dikirim')
                                                <span class="badge bg-warning text-dark">Dikirim</span>
                                            @elseif($order->status === 'selesai')
                                                <span class="badge bg-success text-white">Selesai</span>
                                            @else
                                                <span class="badge bg-danger text-white">Dibatalkan</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-light btn-sm text-dark rounded-circle" title="Detail"><i class="bi bi-eye"></i></a>
                                            <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-light btn-sm text-primary rounded-circle" title="Ajukan Edit"><i class="bi bi-pencil-square"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">Belum ada pesanan yang diinput.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Recent Omset Reports -->
            <div class="card-custom p-0 mb-4">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <span class="text-dark font-weight-700"><i class="bi bi-cash-coin me-2"></i>Laporan Omset Cabang Terkini (WA Input)</span>
                    <a href="{{ route('branch-reports.index') }}" class="btn btn-link text-danger font-weight-600 p-0 text-decoration-none" style="font-size: 0.85rem;">Semua Laporan</a>
                </div>
                <div class="p-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-muted" style="font-size: 0.8rem;">
                                    <th>CABANG</th>
                                    <th>TANGGAL</th>
                                    <th class="text-end">NOMINAL OMSET</th>
                                    <th class="text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentReports as $report)
                                    <tr style="font-size: 0.875rem;">
                                        <td class="font-weight-600 text-dark">{{ $report->branch ? $report->branch->name : 'N/A' }}</td>
                                        <td>{{ date('d-m-Y', strtotime($report->report_date)) }}</td>
                                        <td class="text-end font-weight-700 text-dark">Rp {{ number_format($report->omset, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('branch-reports.edit', $report->id) }}" class="btn btn-light btn-sm text-primary rounded-circle" title="Ajukan Edit"><i class="bi bi-pencil-square"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada laporan omset yang diinput.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Column Right: Edit Request Tracker -->
        <div class="col-xl-4">
            <div class="card-custom p-0">
                <div class="card-header-custom">
                    <span class="text-dark font-weight-700"><i class="bi bi-shield-lock me-2"></i>Status Pengajuan Edit Saya</span>
                </div>
                <div class="p-3">
                    @if(count($myPendingRequests) > 0)
                        <div class="list-group list-group-flush">
                            @foreach($myPendingRequests as $req)
                                <div class="list-group-item px-0 py-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill font-weight-600" style="font-size: 0.75rem;">Menunggu Owner</span>
                                        <small class="text-muted">{{ $req->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="m-0 font-weight-700 text-dark" style="font-size: 0.875rem;">
                                        @if($req->model_type === 'App\\Models\\BranchReport')
                                            Laporan Omset Cabang
                                        @elseif($req->model_type === 'App\\Models\\PartnerOrder')
                                            Pesanan Bahan Baku Mitra
                                        @elseif($req->model_type === 'App\\Models\\Partner')
                                            Profil Data Mitra
                                        @else
                                            Data Cabang
                                        @endif
                                    </p>
                                    <p class="m-0 text-muted mt-1 text-truncate" style="font-size: 0.8rem;">
                                        Alasan: "{{ $req->reason }}"
                                    </p>
                                    <a href="{{ route('edit-requests.show', $req->id) }}" class="d-inline-block mt-2 text-decoration-none font-weight-700" style="font-size: 0.8rem; color: var(--accent-color);">Lihat Pengajuan &rarr;</a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-shield-slash fs-2 d-block mb-2 text-muted"></i>
                            <span style="font-size: 0.85rem;">Tidak ada pengajuan edit Anda yang sedang menunggu persetujuan.</span>
                        </div>
                    @endif
                    
                    <div class="mt-3 border-top pt-3 text-center">
                        <a href="{{ route('edit-requests.index') }}" class="btn btn-light w-100 btn-sm font-weight-600 rounded-3">Riwayat Pengajuan Edit Saya</a>
                    </div>
                </div>
            </div>

            <!-- Export Module Panel -->
            <div class="card-custom p-4 mt-4">
                <h5 class="font-weight-700 text-dark mb-3"><i class="bi bi-cloud-arrow-down-fill text-danger me-2"></i>Modul Ekspor Laporan & Unduh</h5>
                <p class="text-muted d-none d-md-block" style="font-size: 0.85rem;">Unduh data master dan rekapitulasi operasional pusat dalam format file Excel/CSV yang kompatibel.</p>
                
                <div class="d-grid gap-2 mt-3">
                    <a href="{{ route('export.pembelian') }}" class="btn btn-outline-dark d-flex align-items-center justify-content-between p-2 rounded-3" style="font-size: 0.875rem;">
                        <span><i class="bi bi-cart-check-fill text-primary me-2 fs-5"></i> Total Pembelian per Mitra</span>
                        <i class="bi bi-download"></i>
                    </a>

                    <a href="{{ route('export.mitra') }}" class="btn btn-outline-dark d-flex align-items-center justify-content-between p-2 rounded-3" style="font-size: 0.875rem;">
                        <span><i class="bi bi-people-fill text-info me-2 fs-5"></i> Mitra Aktif & MOU</span>
                        <i class="bi bi-download"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
<style>
    @media (max-width: 767.98px) {
        .kpi-card-custom {
            padding: 1rem !important;
        }
        .kpi-card-custom .kpi-value {
            font-size: 1.05rem !important;
            margin-top: 0.25rem !important;
        }
        .kpi-icon-container {
            width: 36px !important;
            height: 36px !important;
            padding: 0 !important;
            border-radius: 8px !important;
        }
        .kpi-icon-container i {
            font-size: 1.15rem !important;
        }
        .kpi-card-title {
            font-size: 0.65rem !important;
            display: block;
            line-height: 1.2;
        }
    }
</style>
@endsection
