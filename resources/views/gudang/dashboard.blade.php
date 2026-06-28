@extends('layouts.app')

@section('title', 'Dashboard')
@section('page_title', 'Dashboard')

@section('content')
<div class="container-fluid p-0">
    <!-- Top Action Banner -->
    <div class="card-custom p-4 mb-4" style="background: linear-gradient(135deg, #1e293b, #334155); color: #fff;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h5 class="font-weight-700 m-0"><i class="bi bi-box-seam-fill me-2"></i>Sistem Manajemen Stok & Fulfill Pesanan</h5>
                <p class="m-0 mt-1 opacity-75 d-none d-md-block" style="font-size: 0.875rem;">Kelola tingkat ketersediaan bahan baku Masyul Kebab, catat pengiriman barang supplier, dan proses pengiriman mitra.</p>
            </div>
            <a href="{{ route('incoming-stocks.create') }}" class="btn btn-accent rounded-3 font-weight-700 px-4 py-2">
                <i class="bi bi-plus-lg me-1"></i> Input Stok Masuk Baru
            </a>
        </div>
    </div>

    <!-- Alarm & Alerts Banner -->
    @if($safetyAlertCount > 0)
        <div class="row">
            <div class="col-12 mb-4">
                <div class="alert alert-danger alert-pulse border-0 shadow-sm d-flex align-items-center justify-content-between p-3" style="border-radius: 12px;">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill me-3 fs-3 text-danger"></i>
                        <div>
                            <strong class="text-dark d-block">Peringatan: {{ $safetyAlertCount }} Bahan Baku Di Bawah Stok Aman!</strong>
                            <span class="text-muted d-none d-md-inline" style="font-size: 0.85rem;">Segera lakukan pemesanan ulang ke supplier untuk menjaga kontinuitas persediaan.</span>
                        </div>
                    </div>
                    <a href="{{ route('raw-materials.index') }}" class="btn btn-danger btn-sm font-weight-600 px-3 py-2 rounded-3">Periksa Stok Kritis</a>
                </div>
            </div>
        </div>
    @endif

    <div class="row">
        <!-- Queue: Active Partner Orders for Status Change -->
        <div class="col-xl-7 col-lg-6 mb-4">
            <div class="card-custom p-0">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <span class="text-dark font-weight-700"><i class="bi bi-hourglass-split me-2"></i>Antrean Pengiriman Pesanan Mitra (WA input)</span>
                    <a href="{{ route('orders.index') }}" class="btn btn-link text-danger font-weight-600 p-0 text-decoration-none" style="font-size: 0.85rem;">Semua Pesanan</a>
                </div>
                <div class="p-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="text-muted" style="font-size: 0.8rem;">
                                    <th>NO</th>
                                    <th>MITRA</th>
                                    <th>TANGGAL KIRIM</th>
                                    <th>STATUS</th>
                                    <th class="text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($activeOrders as $order)
                                    <tr style="font-size: 0.875rem;">
                                        <td>#{{ $order->id }}</td>
                                        <td class="font-weight-600 text-dark">{{ $order->partner ? $order->partner->name : 'N/A' }}</td>
                                        <td>{{ date('d-m-Y', strtotime($order->order_date)) }}</td>
                                        <td>
                                            @if($order->status === 'menunggu_dipacking')
                                                <span class="badge bg-secondary">Menunggu Dipacking</span>
                                            @elseif($order->status === 'dipacking')
                                                <span class="badge bg-info">Dipacking</span>
                                            @elseif($order->status === 'dikirim')
                                                <span class="badge bg-warning text-dark">Dikirim</span>
                                            @elseif($order->status === 'selesai')
                                                <span class="badge bg-success">Selesai</span>
                                            @else
                                                <span class="badge bg-light text-dark">{{ $order->status }}</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-light btn-sm text-dark rounded-3 font-weight-600 py-1 px-3">
                                                Update Status <i class="bi bi-arrow-right-short ms-1"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Tidak ada pesanan aktif dalam antrean.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Recent Supplier Restocks Log -->
            <div class="card-custom p-0">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <span class="text-dark font-weight-700"><i class="bi bi-clock-history me-2"></i>Log Stok Masuk Terbaru</span>
                    <a href="{{ route('incoming-stocks.index') }}" class="btn btn-link text-danger font-weight-600 p-0 text-decoration-none" style="font-size: 0.85rem;">Riwayat Stok Masuk</a>
                </div>
                <div class="p-3">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0" style="font-size: 0.875rem;">
                            <thead>
                                <tr class="text-muted" style="font-size: 0.8rem;">
                                    <th>BAHAN BAKU</th>
                                    <th class="text-center">KUANTITAS MASUK</th>
                                    <th>TANGGAL MASUK</th>
                                    <th>CATATAN</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentIncoming as $in)
                                    <tr>
                                        <td class="font-weight-600 text-dark">{{ $in->rawMaterial ? $in->rawMaterial->name : 'N/A' }}</td>
                                        <td class="text-center text-success font-weight-700">+{{ (float)$in->quantity }}</td>
                                        <td>{{ date('d-m-Y', strtotime($in->incoming_date)) }}</td>
                                        <td class="text-muted" style="font-size: 0.8rem;">{{ $in->notes }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">Belum ada catatan stok masuk.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory List: Current Stock Grid -->
        <div class="col-xl-5 col-lg-6 mb-4">
            <div class="card-custom p-0">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <span class="text-dark font-weight-700"><i class="bi bi-boxes me-2"></i>Daftar Ketersediaan Stok Fisik</span>
                    <a href="{{ route('raw-materials.index') }}" class="btn btn-link text-danger font-weight-600 p-0 text-decoration-none" style="font-size: 0.85rem;">Detail Bahan Baku</a>
                </div>
                <div class="p-3">
                    <div class="list-group list-group-flush" style="max-height: 580px; overflow-y: auto;">
                        @foreach($rawMaterials as $mat)
                            @php
                                $percent = $mat->stock > 0 ? min(100, ($mat->stock / ($mat->safety_stock * 3)) * 100) : 0;
                                $barColor = 'bg-success';
                                if ($mat->isBelowSafetyStock()) {
                                    $barColor = 'bg-danger';
                                } elseif ($mat->stock <= ($mat->safety_stock * 1.5)) {
                                    $barColor = 'bg-warning';
                                }
                            @endphp
                            <div class="list-group-item px-0 py-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div>
                                        <strong class="text-dark" style="font-size: 0.9rem;">{{ $mat->name }}</strong>
                                        <code class="d-block text-muted" style="font-size: 0.725rem;">SKU: {{ $mat->sku }}</code>
                                    </div>
                                    <div class="text-end">
                                        <span class="fs-5 font-weight-800 {{ $mat->isBelowSafetyStock() ? 'text-danger' : 'text-dark' }}">{{ (float)$mat->stock }}</span>
                                        <span class="text-muted" style="font-size: 0.8rem;">{{ $mat->unit }}</span>
                                    </div>
                                </div>
                                
                                <div class="progress" style="height: 6px; border-radius: 50px; background-color: #f1f5f9;">
                                    <div class="progress-bar {{ $barColor }}" role="progressbar" style="width: {{ $percent }}%;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                </div>
                                
                                <div class="d-flex justify-content-between mt-1 text-muted" style="font-size: 0.725rem;">
                                    <span>Safety stock: {{ (float)$mat->safety_stock }} {{ $mat->unit }}</span>
                                    @if($mat->isBelowSafetyStock())
                                        <span class="text-danger font-weight-700"><i class="bi bi-exclamation-triangle"></i> Stok Kritis</span>
                                    @else
                                        <span class="text-success font-weight-600">Aman</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
