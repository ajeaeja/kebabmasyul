@extends('layouts.app')

@section('title', 'Detail Kemitraan')
@section('page_title', 'Detail Profil Kemitraan')

@section('content')
<div class="container-fluid p-0">
    <div class="mb-3">
        <a href="{{ route('partners.index') }}" class="btn btn-light rounded-3 font-weight-600">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Mitra
        </a>
    </div>

    <div class="row">
        <!-- Left Column: Profile Card & Document -->
        <div class="col-lg-5 mb-4">
            <div class="card-custom mb-4">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <span class="text-dark font-weight-700"><i class="bi bi-person-badge-fill me-2 text-danger"></i>Profil Utama Mitra</span>
                    @if($partner->status === 'active')
                        <span class="badge bg-success bg-opacity-10 text-success">Aktif</span>
                    @else
                        <span class="badge bg-danger bg-opacity-10 text-danger">Nonaktif</span>
                    @endif
                </div>
                <div class="p-4">
                    <table class="table table-borderless align-middle mb-0" style="font-size: 0.9rem;">
                        <tr>
                            <td class="text-muted font-weight-600 ps-0" style="width: 140px;">Nama Mitra</td>
                            <td class="text-dark font-weight-700">: {{ $partner->name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-600 ps-0">Nama Pemilik</td>
                            <td class="text-dark">: {{ $partner->owner_name }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-600 ps-0">No. WhatsApp</td>
                            <td>: 
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $partner->phone) }}" target="_blank" class="text-success font-weight-600 text-decoration-none">
                                    <i class="bi bi-whatsapp me-1"></i>{{ $partner->phone }}
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-600 ps-0">Alamat Kedai</td>
                            <td class="text-dark" style="white-space: pre-line;">: {{ $partner->address }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-600 ps-0">Paket Kemitraan</td>
                            <td>: 
                                <span class="badge bg-warning bg-opacity-10 text-warning font-weight-700 px-3 py-1">
                                    {{ $partner->jenis_paket }}
                                </span>
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-600 ps-0">Tanggal Join</td>
                            <td class="text-dark">: {{ $partner->join_date ? date('d-m-Y', strtotime($partner->join_date)) : '-' }}</td>
                        </tr>
                        @if($partner->mou_end_date)
                        <tr>
                            <td class="text-muted font-weight-600 ps-0">Akhir MOU</td>
                            <td class="text-danger">: {{ date('d-m-Y', strtotime($partner->mou_end_date)) }}</td>
                        </tr>
                        @endif
                    </table>

                    @if($partner->notes)
                        <div class="mt-4 pt-3 border-top">
                            <label class="text-muted font-weight-700 d-block mb-1" style="font-size: 0.8rem;">Catatan Tambahan:</label>
                            <p class="text-dark bg-light p-3 rounded-3 m-0" style="font-size: 0.85rem; white-space: pre-line;">{{ $partner->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Documents Card -->
            <div class="card-custom">
                <div class="card-header-custom">
                    <span class="text-dark font-weight-700"><i class="bi bi-file-earmark-pdf-fill me-2 text-danger"></i>Dokumen MOU Kerja Sama</span>
                </div>
                <div class="p-4">
                    @if($partner->mou_path)
                        <div class="d-flex align-items-center bg-light p-3 rounded-3 mb-3">
                            <div class="bg-danger bg-opacity-10 text-danger rounded-3 p-2 me-3">
                                <i class="bi bi-file-earmark-text fs-4"></i>
                            </div>
                            <div class="text-truncate">
                                <strong class="text-dark d-block text-truncate" style="font-size: 0.85rem;">{{ basename($partner->mou_path) }}</strong>
                                <small class="text-muted" style="font-size: 0.75rem;">MOU Dokumen Resmi</small>
                            </div>
                        </div>

                        <div class="row g-2">
                            <div class="col-6">
                                <a href="{{ asset($partner->mou_path) }}" target="_blank" class="btn btn-outline-dark w-100 py-2 rounded-3 font-weight-600" style="font-size: 0.85rem;">
                                    <i class="bi bi-eye me-1"></i> Lihat Dokumen
                                </a>
                            </div>
                            <div class="col-6">
                                <a href="{{ asset($partner->mou_path) }}" download class="btn btn-accent w-100 py-2 rounded-3 font-weight-700" style="font-size: 0.85rem;">
                                    <i class="bi bi-download me-1"></i> Unduh File
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-3 text-muted">
                            <i class="bi bi-file-earmark-excel fs-2 d-block mb-2"></i>
                            <span>Dokumen MOU belum diunggah.</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Riwayat Pesanan Placeholder -->
        <div class="col-lg-7 mb-4">
            <div class="card-custom">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <span class="text-dark font-weight-700"><i class="bi bi-receipt-cutoff me-2 text-danger"></i>Riwayat Pesanan Bahan Baku</span>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $orders->total() }} Pesanan</span>
                </div>
                <div class="p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.875rem;">
                            <thead>
                                <tr class="text-muted" style="font-size: 0.8rem;">
                                    <th>NO. ORDER</th>
                                    <th>TANGGAL</th>
                                    <th class="text-end">TOTAL HARGA</th>
                                    <th class="text-center">STATUS</th>
                                    <th class="text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td class="font-weight-700 text-dark">#{{ $order->id }}</td>
                                        <td>{{ date('d-m-Y', strtotime($order->order_date)) }}</td>
                                        <td class="text-end font-weight-600 text-dark">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
                                        <td class="text-center">
                                            @if($order->status === 'selesai')
                                                <span class="badge bg-success bg-opacity-10 text-success">Selesai</span>
                                            @elseif($order->status === 'menunggu_dipacking')
                                                <span class="badge bg-warning bg-opacity-10 text-warning">Menunggu</span>
                                            @elseif($order->status === 'dipacking')
                                                <span class="badge bg-info bg-opacity-10 text-info">Dipacking</span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger">Batal</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-light btn-sm text-dark rounded-circle" title="Lihat"><i class="bi bi-eye"></i></a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Belum ada riwayat pesanan bahan baku untuk mitra ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
