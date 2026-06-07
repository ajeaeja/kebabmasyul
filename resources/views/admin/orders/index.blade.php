@extends('layouts.app')

@section('title', 'Data Pesanan Bahan Baku Mitra')
@section('page_title', 'Kelola Pesanan Bahan Baku')

@section('content')
<div class="container-fluid p-0">
    <!-- Search & Filter Card -->
    <div class="card-custom p-4 mb-4">
        <form action="{{ route('orders.index') }}" method="GET" class="row g-2 align-items-end">
            <!-- Row 1 -->
            <div class="col-md-3 col-12">
                <label for="search" class="form-label text-muted font-weight-600" style="font-size: 0.75rem;">Cari Pesanan</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-start-0" id="search" name="search" placeholder="ID / Nama Mitra..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3 col-12">
                <label for="partner_id" class="form-label text-muted font-weight-600" style="font-size: 0.75rem;">Pilih Mitra</label>
                <select class="form-select" id="partner_id" name="partner_id">
                    <option value="">-- Semua Mitra --</option>
                    @foreach($partners as $p)
                        <option value="{{ $p->id }}" {{ request('partner_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-6">
                <label for="status" class="form-label text-muted font-weight-600" style="font-size: 0.75rem;">Status Proses</label>
                <select class="form-select" id="status" name="status">
                    <option value="">-- Semua Status --</option>
                    <option value="menunggu_dipacking" {{ request('status') === 'menunggu_dipacking' ? 'selected' : '' }}>Menunggu Dipacking</option>
                    <option value="dipacking" {{ request('status') === 'dipacking' ? 'selected' : '' }}>Dipacking</option>
                    <option value="dikirim" {{ request('status') === 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            @if(!Auth::user()->isGudang())
                <div class="col-md-2 col-6">
                    <label for="payment_status" class="form-label text-muted font-weight-600" style="font-size: 0.75rem;">Status Pembayaran</label>
                    <select class="form-select" id="payment_status" name="payment_status">
                        <option value="">-- Semua Status --</option>
                        <option value="lunas" {{ request('payment_status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="belum_lunas" {{ request('payment_status') === 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                    </select>
                </div>
            @endif
            <div class="col-md-2 col-12">
                <label for="date_filter" class="form-label text-muted font-weight-600" style="font-size: 0.75rem;">Periode Tanggal</label>
                <select class="form-select" id="date_filter" name="date_filter" onchange="toggleCustomDates()">
                    <option value="">Semua Waktu</option>
                    <option value="today" {{ request('date_filter') === 'today' ? 'selected' : '' }}>Hari Ini</option>
                    <option value="last_7_days" {{ request('date_filter') === 'last_7_days' ? 'selected' : '' }}>7 Hari Terakhir</option>
                    <option value="last_30_days" {{ request('date_filter') === 'last_30_days' ? 'selected' : '' }}>30 Hari Terakhir</option>
                    <option value="custom" {{ request('date_filter') === 'custom' ? 'selected' : '' }}>Pilih Periode</option>
                </select>
            </div>

            <!-- Row 2: Custom Dates (will show if custom is selected) -->
            <div class="col-md-3 col-6 custom-date-input" style="display: {{ request('date_filter') === 'custom' ? 'block' : 'none' }};">
                <label for="start_date" class="form-label text-muted font-weight-600" style="font-size: 0.75rem;">Mulai Tanggal</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3 col-6 custom-date-input" style="display: {{ request('date_filter') === 'custom' ? 'block' : 'none' }};">
                <label for="end_date" class="form-label text-muted font-weight-600" style="font-size: 0.75rem;">Sampai Tanggal</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}">
            </div>

            <div class="col-md-3 col-12 d-grid gap-2 d-md-flex ms-auto mt-2">
                <button type="submit" class="btn btn-accent flex-grow-1 py-2"><i class="bi bi-funnel me-1"></i> Filter</button>
                <a href="{{ route('orders.index') }}" class="btn btn-light border py-2"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
            </div>
        </form>
    </div>

    @if(Auth::user()->isOwner())
    <!-- Chart Card -->
    <div class="card-custom p-4 mb-4">
        <h6 class="font-weight-700 text-dark mb-3"><i class="bi bi-graph-up text-warning me-2"></i>Grafik Tren Nominal Pembelian Bahan Baku</h6>
        <div style="height: 250px; position: relative;">
            <canvas id="partnerPurchasesChart"></canvas>
        </div>
    </div>
    @endif

    <div class="card-custom p-0">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <div>
                <span class="text-dark font-weight-700">Daftar Pesanan Bahan Baku Mitra</span>
                <p class="m-0 text-muted d-none d-md-block" style="font-size: 0.8rem; font-weight: 400;">Mencatat pembelian bahan baku bulanan/mingguan dari mitra eksternal.</p>
            </div>
            
            <div class="d-flex gap-2 align-items-center">
                @if(Auth::user()->isAdmin())
                    <a href="{{ route('orders.create') }}" class="btn btn-accent rounded-3 font-weight-700 px-3 py-2 d-flex align-items-center justify-content-center" title="Input Pesanan Baru">
                        <i class="bi bi-cart-plus-fill me-md-1"></i> <span class="d-none d-md-inline">Input Pesanan Baru</span>
                    </a>
                @endif
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle font-weight-600 px-3 py-2 rounded-3 d-flex align-items-center justify-content-center" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Export Data">
                        <i class="bi bi-download me-md-1"></i> <span class="d-none d-md-inline">Export Data</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                        <li><a class="dropdown-item" href="{{ route('generic.export', array_merge(['type' => 'orders', 'format' => 'xls'], request()->query())) }}"><i class="bi bi-file-earmark-excel-fill text-success me-2"></i> Export Excel (.xls)</a></li>
                        <li><a class="dropdown-item" href="{{ route('generic.export', array_merge(['type' => 'orders', 'format' => 'doc'], request()->query())) }}"><i class="bi bi-file-earmark-word-fill text-primary me-2"></i> Export Word (.doc)</a></li>
                        <li><a class="dropdown-item" href="{{ route('generic.export', array_merge(['type' => 'orders', 'format' => 'pdf'], request()->query())) }}" target="_blank"><i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i> Export PDF</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-muted" style="font-size: 0.8rem;">
                            <th>NO PESANAN</th>
                            <th>NAMA MITRA</th>
                            <th>TANGGAL PESAN</th>
                            <th>TARGET KIRIM</th>
                            <th>JENIS BARANG</th>
                            @if(!Auth::user()->isGudang())
                                <th>STATUS BAYAR</th>
                                <th class="text-end">TOTAL TAGIHAN</th>
                            @endif
                            <th class="text-center">STATUS SEKARANG</th>
                            <th class="text-center" style="width: 180px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            <tr style="font-size: 0.875rem;">
                                <td><strong>#{{ $order->id }}</strong></td>
                                <td class="font-weight-700 text-dark">{{ $order->partner ? $order->partner->name : 'N/A' }}</td>
                                <td>{{ date('d-m-Y', strtotime($order->order_date)) }}</td>
                                <td>{{ $order->shipping_date ? date('d-m-Y', strtotime($order->shipping_date)) : '-' }}</td>
                                <td class="text-center">{{ $order->items->count() }} Item</td>
                                
                                @if(!Auth::user()->isGudang())
                                    <td>
                                        @if($order->payment_status === 'lunas')
                                            <span class="badge bg-success bg-opacity-10 text-success">Lunas</span>
                                            @if($order->payment_method)
                                                <div class="text-muted font-weight-500 mt-1" style="font-size: 0.7rem;">
                                                    @if($order->payment_method === 'transfer')
                                                        Transfer
                                                    @elseif($order->payment_method === 'qris')
                                                        QRIS
                                                    @elseif($order->payment_method === 'cash')
                                                        Cash
                                                    @else
                                                        {{ ucfirst($order->payment_method) }}
                                                    @endif
                                                </div>
                                            @endif
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger">Belum Lunas</span>
                                        @endif
                                    </td>
                                    <td class="text-end font-weight-700 text-dark">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                @endif
                                
                                <td class="text-center">
                                    @if($order->status === 'menunggu_dipacking')
                                        <span class="badge bg-secondary badge-pill-custom">Menunggu Dipacking</span>
                                    @elseif($order->status === 'dipacking')
                                        <span class="badge bg-info badge-pill-custom">Dipacking</span>
                                    @elseif($order->status === 'dikirim')
                                        <span class="badge bg-warning text-dark badge-pill-custom">Dikirim</span>
                                    @elseif($order->status === 'selesai')
                                        <span class="badge bg-success badge-pill-custom">Selesai</span>
                                    @else
                                        <span class="badge bg-light text-dark badge-pill-custom">{{ $order->status }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-info border-0 rounded-circle" title="Detail Pesanan">
                                            <i class="bi bi-eye fs-6"></i>
                                        </a>
                                        
                                        @if(Auth::user()->isAdmin())
                                            <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-sm btn-outline-primary border-0 rounded-circle" title="Ajukan Edit">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                        @endif

                                        @if(Auth::user()->isAdmin())
                                            <form action="{{ route('orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesanan ini secara permanen?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger border-0 rounded-circle" title="Hapus Permanen">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ Auth::user()->isGudang() ? 7 : 9 }}" class="text-center text-muted py-4">Belum ada transaksi pesanan terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="mt-4">
                {{ $orders->links() }}
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
    const ctxPartner = document.getElementById('partnerPurchasesChart').getContext('2d');
    const partnerChartData = @json($partnerChart);
    new Chart(ctxPartner, {
        type: 'line',
        data: partnerChartData,
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' },
                tooltip: {
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
