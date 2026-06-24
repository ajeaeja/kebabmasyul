@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->id)
@section('page_title', 'Detail Pesanan #' . $order->id)

@section('content')
<div class="container-fluid p-0" style="max-width: 900px; margin: 0 auto;">
    <div class="row">
        <!-- Main Column: Order Items -->
        <div class="col-md-8 mb-4">
            <div class="card-custom p-0">
                <div class="card-header-custom">
                    <span class="text-dark font-weight-700"><i class="bi bi-box-seam me-2"></i>Item Bahan Baku Yang Dipesan</span>
                </div>
                <div class="p-4">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead>
                                <tr class="text-muted" style="font-size: 0.8rem;">
                                    <th>SKU</th>
                                    <th>NAMA BARANG</th>
                                    <th class="text-center">KUANTITAS</th>
                                    <th>SATUAN</th>
                                    @if(!Auth::user()->isGudang())
                                        <th class="text-end">HARGA SATUAN</th>
                                        <th class="text-end">SUBTOTAL</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr style="font-size: 0.875rem;">
                                        <td><code>{{ $item->rawMaterial ? $item->rawMaterial->sku : 'N/A' }}</code></td>
                                        <td class="font-weight-600 text-dark">{{ $item->rawMaterial ? $item->rawMaterial->name : 'Bahan Baku Terhapus' }}</td>
                                        <td class="text-center font-weight-700">{{ (float)$item->quantity }}</td>
                                        <td><span class="badge bg-light text-dark border">{{ $item->rawMaterial ? $item->rawMaterial->unit : '-' }}</span></td>
                                        
                                        @if(!Auth::user()->isGudang())
                                            <td class="text-end">Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                                            <td class="text-end font-weight-700 text-dark">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if(!Auth::user()->isGudang())
                        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                            <div style="width: 280px;">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted" style="font-size: 0.85rem;">Total Bahan Baku:</span>
                                    <span class="text-dark font-weight-700">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2 pb-2 border-bottom">
                                    <span class="text-muted" style="font-size: 0.85rem;">Ongkos Kirim:</span>
                                    <span class="text-dark font-weight-700">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="text-muted font-weight-700 text-uppercase" style="font-size: 0.75rem;">Total Tagihan:</span>
                                    <h4 class="m-0 font-weight-800 text-danger">Rp {{ number_format($order->total_price + $order->shipping_cost, 0, ',', '.') }}</h4>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Status & Meta -->
        <div class="col-md-4 mb-4">
            <!-- Metadata Card -->
            <div class="card-custom p-4 mb-4">
                <h6 class="font-weight-700 text-dark border-bottom pb-2 mb-3">Informasi Pemesanan</h6>
                <div class="mb-3">
                    <span class="text-muted d-block" style="font-size: 0.75rem;">Outlet Mitra Pemesan</span>
                    <strong class="text-dark">{{ $order->partner ? $order->partner->name : 'N/A' }}</strong>
                </div>
                <div class="mb-3">
                    <span class="text-muted d-block" style="font-size: 0.75rem;">Pemilik Outlet</span>
                    <span>{{ $order->partner ? $order->partner->owner_name : 'N/A' }}</span>
                </div>
                <div class="mb-3">
                    <span class="text-muted d-block" style="font-size: 0.75rem;">WhatsApp Mitra</span>
                    <span>{{ $order->partner ? $order->partner->phone : 'N/A' }}</span>
                </div>
                <div class="mb-3">
                    <span class="text-muted d-block" style="font-size: 0.75rem;">Tanggal Order</span>
                    <span>{{ date('d F Y', strtotime($order->order_date)) }}</span>
                </div>
                <div class="mb-3">
                    <span class="text-muted d-block" style="font-size: 0.75rem;">Target Tanggal Pengiriman</span>
                    <span>{{ $order->shipping_date ? date('d F Y', strtotime($order->shipping_date)) : '-' }}</span>
                </div>
                <div class="mb-3">
                    <span class="text-muted d-block" style="font-size: 0.75rem;">Ekspedisi (Bus/Travel/Resi)</span>
                    <span class="badge bg-light text-dark border font-weight-500 py-1 px-2 d-inline-block text-wrap" style="max-width: 100%;">{{ $order->expedition_info ?: '-' }}</span>
                </div>
                @if(!Auth::user()->isGudang())
                    <div class="mb-3">
                        <span class="text-muted d-block" style="font-size: 0.75rem;">Ongkos Kirim</span>
                        <strong class="text-dark">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</strong>
                    </div>
                    <div class="mb-3">
                        <span class="text-muted d-block" style="font-size: 0.75rem;">Status Pembayaran</span>
                        @if($order->payment_status === 'lunas')
                            <span class="badge bg-success bg-opacity-10 text-success mb-2">Lunas</span>
                            @if($order->payment_method)
                                <div class="mt-1">
                                    <span class="text-muted d-block mb-1" style="font-size: 0.7rem;">Metode Pembayaran:</span>
                                    <strong class="text-dark" style="font-size: 0.85rem;">
                                        @if($order->payment_method === 'transfer')
                                            Transfer Bank
                                        @elseif($order->payment_method === 'qris')
                                            QRIS
                                        @elseif($order->payment_method === 'cash')
                                            Tunai / Cash
                                        @else
                                            {{ ucfirst($order->payment_method) }}
                                        @endif
                                    </strong>
                                </div>
                            @endif
                            @if(Auth::user()->isAdmin() || Auth::user()->isOwner())
                                <div class="mt-2">
                                    <form action="{{ route('orders.payment', $order->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="mark_unpaid" value="1">
                                        <button type="submit" class="btn btn-sm btn-outline-danger border-0 p-0 text-decoration-underline" style="font-size: 0.75rem;" onclick="return confirm('Ubah status pembayaran menjadi Belum Lunas?')">
                                            <i class="bi bi-x-circle-fill me-1"></i> Ubah Jadi Belum Lunas
                                        </button>
                                    </form>
                                </div>
                            @endif
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger mb-2">Belum Lunas</span>
                            @if(Auth::user()->isAdmin() || Auth::user()->isOwner())
                                <div class="mt-2">
                                    <form action="{{ route('orders.payment', $order->id) }}" method="POST" class="d-flex flex-column gap-2 bg-light p-2 rounded-3 border" style="max-width: 250px;">
                                        @csrf
                                        <label for="payment_method_confirm" class="font-weight-600 m-0 text-dark" style="font-size: 0.75rem;">Pilih Metode Pembayaran:</label>
                                        <select id="payment_method_confirm" name="payment_method" class="form-select form-select-sm" required>
                                            <option value="" disabled selected>-- Pilih Metode --</option>
                                            <option value="transfer">Transfer Bank</option>
                                            <option value="qris">QRIS</option>
                                            <option value="cash">Tunai / Cash</option>
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-accent w-100 rounded-3 py-1 font-weight-600" style="font-size: 0.75rem;">Konfirmasi Lunas</button>
                                    </form>
                                </div>
                            @endif
                        @endif
                    </div>
                @endif
                <div>
                    <span class="text-muted d-block" style="font-size: 0.75rem;">Status Pesanan</span>
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
                </div>
            </div>

            <!-- Status Edit Card (Gudang or Admin only) -->
            @if(Auth::user()->isGudang() || Auth::user()->isAdmin())
                <div class="card-custom p-4">
                    <h6 class="font-weight-700 text-dark border-bottom pb-2 mb-3"><i class="bi bi-gear-fill me-2 text-danger"></i>Update Status Fulfill</h6>
                    <form action="{{ route('orders.update-status', $order->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="mb-3">
                            <label for="status" class="form-label font-weight-600">Pilih Status Baru</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="menunggu_dipacking" {{ $order->status === 'menunggu_dipacking' ? 'selected' : '' }}>Menunggu Dipacking</option>
                                <option value="dipacking" {{ $order->status === 'dipacking' ? 'selected' : '' }}>Dipacking</option>
                                <option value="dikirim" {{ $order->status === 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                                <option value="selesai" {{ $order->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                            </select>
                        </div>
                        
                        <div class="card bg-light border-0 rounded-3 p-3 mb-3">
                            <p class="m-0 text-muted" style="font-size: 0.75rem;"><i class="bi bi-info-circle-fill text-danger me-1"></i><strong>Info:</strong> Stok barang sudah otomatis dipotong ketika pesanan ini dibuat oleh Admin.</p>
                        </div>

                        <button type="submit" class="btn btn-accent w-100 rounded-3 font-weight-700 py-2">Update Status</button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
