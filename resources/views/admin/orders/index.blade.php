@extends('layouts.app')

@section('title', 'Data Pesanan Bahan Baku Mitra')
@section('page_title', 'Kelola Pesanan Bahan Baku')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Title & Subtitle from Stitch -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h4 class="font-weight-800 text-dark mb-1">Kelola Pesanan Bahan Baku</h4>
            <p class="text-muted m-0" style="font-size: 0.9rem;">Mencatat pembelian bahan baku bulanan/mingguan dari mitra eksternal.</p>
        </div>
        <div class="d-flex gap-2">
            @if(Auth::user()->isAdmin())
                <a href="{{ route('orders.create') }}" class="btn btn-accent rounded-3 font-weight-700 px-4 py-2.5 d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">add</span>
                    <span>Buat Pesanan Baru</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Search & Filter Card from Stitch -->
    <div class="card-custom p-4 mb-4">
        <form action="{{ route('orders.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-3 col-12">
                <div class="position-relative">
                    <span class="material-symbols-outlined position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 20px;">search</span>
                    <input type="text" class="form-control" style="padding-left: 42px;" id="search" name="search" placeholder="Cari ID / Nama Mitra..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-2 col-12">
                <select class="form-select" id="partner_id" name="partner_id">
                    <option value="">-- Pilih Mitra --</option>
                    @foreach($partners as $p)
                        <option value="{{ $p->id }}" {{ request('partner_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-12">
                <select class="form-select" id="status" name="status">
                    <option value="">-- Status Proses --</option>
                    <option value="menunggu_dipacking" {{ request('status') === 'menunggu_dipacking' ? 'selected' : '' }}>Menunggu Dipacking</option>
                    <option value="dipacking" {{ request('status') === 'dipacking' ? 'selected' : '' }}>Dipacking</option>
                    <option value="dikirim" {{ request('status') === 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                    <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </div>
            @if(!Auth::user()->isGudang())
                <div class="col-md-2 col-12">
                    <select class="form-select" id="payment_status" name="payment_status">
                        <option value="">-- Status Bayar --</option>
                        <option value="lunas" {{ request('payment_status') === 'lunas' ? 'selected' : '' }}>Lunas</option>
                        <option value="belum_lunas" {{ request('payment_status') === 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                    </select>
                </div>
            @endif
            <div class="col-md-3 col-12">
                <select class="form-select" id="date_filter" name="date_filter" onchange="toggleCustomDates()">
                    <option value="">Semua Waktu</option>
                    <option value="today" {{ request('date_filter') === 'today' ? 'selected' : '' }}>Hari Ini</option>
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
                    Filter Data
                </button>
                <a href="{{ route('orders.index') }}" class="btn btn-light border px-3 py-2.5 d-flex align-items-center gap-1">
                    <span class="material-symbols-outlined text-[18px]">refresh</span>
                    Reset
                </a>
            </div>
        </form>
    </div>

    @if(Auth::user()->isOwner())
    <!-- Chart Section (Bento Style from Stitch) -->
    <div class="row g-4 mb-4">
        <div class="col-lg-9 col-12">
            <div class="card-custom p-4 h-100">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <span class="material-symbols-outlined text-primary p-2 bg-danger bg-opacity-10 rounded-3">trending_up</span>
                        <div>
                            <h6 class="font-weight-700 text-dark m-0">Grafik Tren Pembelian</h6>
                            <p class="text-xs text-muted m-0" style="font-size: 0.75rem;">Nominal pembelian bahan baku oleh mitra (IDR)</p>
                        </div>
                    </div>
                </div>
                <div style="height: 220px; position: relative;">
                    <div id="partnerPurchasesChart" style="height: 220px;"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-12">
            <div class="card-custom bg-dark text-white p-4 h-100 d-flex flex-column justify-content-between position-relative overflow-hidden" style="background-color: #111c2d !important;">
                <div class="position-relative" style="z-index: 1;">
                    <p class="text-white-50 text-uppercase tracking-wider font-weight-700 m-0" style="font-size: 0.7rem;">Total Pesanan Bulan Ini</p>
                    @php
                        $totalMonth = \App\Models\PartnerOrder::whereYear('order_date', date('Y'))
                            ->whereMonth('order_date', date('m'))
                            ->sum('total_price');
                    @endphp
                    <h3 class="text-white font-weight-800 tracking-tight mt-2">Rp {{ number_format($totalMonth, 0, ',', '.') }}</h3>
                    <div class="mt-3 d-flex align-items-center gap-2">
                        <span class="d-inline-flex align-items-center text-success text-xs font-weight-700" style="color: #4ade80 !important;">
                            <span class="material-symbols-outlined text-[16px] me-1">trending_up</span>
                            +12.5%
                        </span>
                        <span class="text-white-50" style="font-size: 0.7rem;">v.s. Bulan Lalu</span>
                    </div>
                </div>
                <div class="mt-4 position-relative" style="z-index: 1;">
                    <div class="rounded-3 p-3 mb-3" style="background-color: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.1);">
                        <p class="text-white-50 text-uppercase m-0" style="font-size: 0.65rem; font-weight: 700;">Pesanan Terbanyak</p>
                        @php
                            $topPartner = \App\Models\PartnerOrder::select('partner_id', \DB::raw('COUNT(*) as count'))
                                ->groupBy('partner_id')
                                ->orderBy('count', 'desc')
                                ->first();
                            $topPartnerName = $topPartner && $topPartner->partner ? $topPartner->partner->name : 'Tidak Ada';
                        @endphp
                        <p class="text-white font-weight-600 m-0" style="font-size: 0.85rem;">{{ $topPartnerName }}</p>
                    </div>
                    <a href="{{ route('branch-reports.index') }}" class="btn btn-light w-100 text-dark font-weight-700 py-2" style="font-size: 0.8rem; border-radius: 8px;">Lihat Laporan Detail</a>
                </div>
                <!-- blur sphere decoration -->
                <div class="position-absolute bg-danger bg-opacity-20 rounded-circle" style="width: 120px; height: 120px; right: -30px; bottom: -30px; filter: blur(40px);"></div>
            </div>
        </div>
    </div>
    @endif

    <!-- Table Section from Stitch -->
    <div id="table-container">
        @fragment('table-section')
        <div class="card-custom p-0">
        <div class="card-header-custom border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
            <div>
                <span class="text-dark font-weight-700">Daftar Pesanan Bahan Baku Mitra</span>
            </div>
            
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle font-weight-600 px-3 py-2 rounded-3 d-flex align-items-center justify-content-center" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Export Data">
                    <span class="material-symbols-outlined text-[18px] me-1">download</span>
                    <span>Export Data</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                    <li><a class="dropdown-item" href="{{ route('generic.export', array_merge(['type' => 'orders', 'format' => 'xls'], request()->query())) }}"><i class="bi bi-file-earmark-excel-fill text-success me-2"></i> Export Excel (.xls)</a></li>
                    <li><a class="dropdown-item" href="{{ route('generic.export', array_merge(['type' => 'orders', 'format' => 'doc'], request()->query())) }}"><i class="bi bi-file-earmark-word-fill text-primary me-2"></i> Export Word (.doc)</a></li>
                    <li><a class="dropdown-item" href="{{ route('generic.export', array_merge(['type' => 'orders', 'format' => 'pdf'], request()->query())) }}" target="_blank"><i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i> Export PDF</a></li>
                </ul>
            </div>
        </div>
        <div class="p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border-collapse: collapse;">
                    <thead>
                        <tr class="bg-light text-muted uppercase font-weight-700" style="font-size: 0.75rem; border-bottom: 1px solid #f1f5f9;">
                            <th class="py-3 px-4">No Pesanan</th>
                            <th class="py-3 px-4">Nama Mitra</th>
                            <th class="py-3 px-4">Tanggal Pesan</th>
                            <th class="py-3 px-4">Target Kirim</th>
                            <th class="py-3 px-4 text-center">Item</th>
                            @if(!Auth::user()->isGudang())
                                <th class="py-3 px-4">Status Bayar</th>
                                <th class="py-3 px-4 text-end">Total Tagihan</th>
                            @endif
                            <th class="py-3 px-4 text-center">Status Sekarang</th>
                            <th class="py-3 px-4 text-center" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($orders as $order)
                            <tr class="transition-colors group" style="font-size: 0.875rem;">
                                <td class="py-3.5 px-4"><strong>#{{ $order->id }}</strong></td>
                                <td class="py-3.5 px-4">
                                    <div class="font-semibold text-dark">{{ $order->partner ? $order->partner->name : 'N/A' }}</div>
                                    <div class="text-muted text-xs">{{ $order->partner ? $order->partner->address : '' }}</div>
                                </td>
                                <td class="py-3.5 px-4 text-muted">{{ date('d-m-Y', strtotime($order->order_date)) }}</td>
                                <td class="py-3.5 px-4 text-danger font-weight-600">{{ $order->shipping_date ? date('d-m-Y', strtotime($order->shipping_date)) : '-' }}</td>
                                <td class="py-3.5 px-4 text-center">
                                    <span class="px-2 py-1 bg-light rounded text-xs font-weight-700 border">{{ $order->items->count() }} Item</span>
                                </td>
                                
                                @if(!Auth::user()->isGudang())
                                    <td class="py-3.5 px-4">
                                        @if($order->payment_status === 'lunas')
                                            <span class="badge bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-pill px-3 py-1 font-weight-700" style="background-color: #d1fae5; color: #065f46; border-color: #a7f3d0 !important;">
                                                Lunas
                                            </span>
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
                                            <span class="badge bg-red-100 text-red-800 border border-red-200 rounded-pill px-3 py-1 font-weight-700" style="background-color: #fee2e2; color: #991b1b; border-color: #fca5a5 !important;">
                                                Belum Lunas
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-3.5 px-4 text-end font-weight-800 text-dark">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                @endif
                                
                                <td class="py-3.5 px-4 text-center">
                                    @if($order->status === 'menunggu_dipacking')
                                        <span class="badge bg-secondary rounded-pill px-3 py-1 font-weight-600">Menunggu Dipacking</span>
                                    @elseif($order->status === 'dipacking')
                                        <span class="badge bg-info text-white rounded-pill px-3 py-1 font-weight-600">Dipacking</span>
                                    @elseif($order->status === 'dikirim')
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1 font-weight-600">Dikirim</span>
                                    @elseif($order->status === 'selesai')
                                        <span class="badge bg-success rounded-pill px-3 py-1 font-weight-600">Selesai</span>
                                    @else
                                        <span class="badge bg-light text-dark rounded-pill px-3 py-1 font-weight-600">{{ $order->status }}</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-4">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-light border rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Detail">
                                            <span class="material-symbols-outlined text-muted" style="font-size: 18px;">visibility</span>
                                        </a>
                                        
                                        @if(Auth::user()->isAdmin())
                                            <a href="{{ route('orders.edit', $order->id) }}" class="btn btn-sm btn-light border rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Edit">
                                                <span class="material-symbols-outlined text-muted" style="font-size: 18px;">edit</span>
                                            </a>
                                            
                                            <form action="{{ route('orders.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesanan ini secara permanen?')" class="m-0">
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
                        @empty
                            <tr>
                                <td colspan="{{ Auth::user()->isGudang() ? 7 : 9 }}" class="text-center text-muted py-5">Belum ada transaksi pesanan terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="px-4 py-3 border-top bg-light d-flex justify-content-between align-items-center">
                <div class="text-muted" style="font-size: 0.85rem;">
                    Menampilkan <span class="font-weight-700 text-dark">{{ $orders->firstItem() ?? 0 }} - {{ $orders->lastItem() ?? 0 }}</span> dari <span class="font-weight-700 text-dark">{{ $orders->total() }}</span> pesanan
                </div>
                <div>
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        @endfragment
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

    @if(Auth::user()->isOwner())
    const partnerChartOptions = {
        chart: {
            type: 'area',
            height: 220,
            fontFamily: "'Plus Jakarta Sans', sans-serif",
            toolbar: { show: false }
        },
        stroke: {
            curve: 'smooth',
            width: 3,
            colors: ['#f59e0b']
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.3,
                opacityTo: 0.05,
                stops: [0, 90, 100]
            }
        },
        colors: ['#f59e0b'],
        series: [{
            name: 'Nominal Pembelian Mitra',
            data: @json($partnerChart['datasets'][0]['data'] ?? [])
        }],
        xaxis: {
            categories: @json($partnerChart['labels'] ?? []),
            labels: {
                style: {
                    colors: '#64748b',
                    fontWeight: 500
                }
            }
        },
        yaxis: {
            labels: {
                formatter: function(val) {
                    return 'Rp ' + new Intl.NumberFormat('id-ID', { maximumSignificantDigits: 3 }).format(val);
                },
                style: {
                    colors: '#64748b'
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(val);
                }
            }
        },
        markers: {
            size: 4,
            colors: ['#f59e0b'],
            strokeColors: '#fff',
            strokeWidth: 2,
            hover: { size: 6 }
        },
        grid: {
            borderColor: '#f1f5f9'
        }
    };

    window.partnerPurchasesChart = new ApexCharts(document.querySelector("#partnerPurchasesChart"), partnerChartOptions);
    window.partnerPurchasesChart.render();
    @endif

    // Intercept form submissions for AJAX
    const filterForm = document.querySelector('form');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            fetchData();
        });
    }

    // Intercept pagination clicks
    document.addEventListener('click', function(e) {
        const link = e.target.closest('#table-container .pagination a');
        if (link) {
            e.preventDefault();
            fetchData(link.href);
        }
    });

    function fetchData(url = null) {
        if (!url) {
            const formData = new FormData(filterForm);
            const params = new URLSearchParams(formData);
            url = window.location.pathname + '?' + params.toString();
        }

        // Push URL state
        window.history.pushState({}, '', url);

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.html) {
                document.getElementById('table-container').innerHTML = data.html;
            }
            if (data.chart_data && window.partnerPurchasesChart) {
                window.partnerPurchasesChart.updateOptions({
                    xaxis: {
                        categories: data.chart_labels
                    }
                });
                window.partnerPurchasesChart.updateSeries([{
                    name: 'Nominal Pembelian Mitra',
                    data: data.chart_data
                }]);
            }
        })
        .catch(err => console.error("Error fetching AJAX:", err));
    }
</script>
@endsection
