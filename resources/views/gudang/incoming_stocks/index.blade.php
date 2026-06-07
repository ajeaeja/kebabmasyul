@extends('layouts.app')

@section('title', 'Log Riwayat Stok Masuk')
@section('page_title', 'Riwayat Stok Masuk Supplier')

@section('content')
<div class="container-fluid p-0">
    <div class="card-custom p-0">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <div>
                <span class="text-dark font-weight-700">Daftar Pengadaan Bahan Baku</span>
                <p class="m-0 text-muted d-none d-md-block" style="font-size: 0.8rem; font-weight: 400;">Log transaksi penerimaan pasokan bahan baku yang masuk ke dalam gudang.</p>
            </div>
            
            <div class="d-flex gap-2 align-items-center">
                @if(Auth::user()->isGudang())
                    <a href="{{ route('incoming-stocks.create') }}" class="btn btn-accent rounded-3 font-weight-700 px-3 py-2 d-flex align-items-center justify-content-center" title="Catat Stok Masuk">
                        <i class="bi bi-plus-lg me-md-1"></i> <span class="d-none d-md-inline">Catat Stok Masuk</span>
                    </a>
                @endif
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle font-weight-600 px-3 py-2 rounded-3 d-flex align-items-center justify-content-center" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Export Data">
                        <i class="bi bi-download me-md-1"></i> <span class="d-none d-md-inline">Export Data</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                        <li><a class="dropdown-item" href="{{ route('generic.export', ['type' => 'incoming-stocks', 'format' => 'xls']) }}"><i class="bi bi-file-earmark-excel-fill text-success me-2"></i> Export Excel (.xls)</a></li>
                        <li><a class="dropdown-item" href="{{ route('generic.export', ['type' => 'incoming-stocks', 'format' => 'doc']) }}"><i class="bi bi-file-earmark-word-fill text-primary me-2"></i> Export Word (.doc)</a></li>
                        <li><a class="dropdown-item" href="{{ route('generic.export', ['type' => 'incoming-stocks', 'format' => 'pdf']) }}" target="_blank"><i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i> Export PDF</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.875rem;">
                    <thead>
                        <tr class="text-muted" style="font-size: 0.8rem;">
                            <th>SKU</th>
                            <th>NAMA BAHAN BAKU</th>
                            <th class="text-center">KUANTITAS MASUK</th>
                            <th>SATUAN</th>
                            <th>TANGGAL MASUK</th>
                            <th>CATATAN PENGADAAN</th>
                            <th class="text-center" style="width: 100px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incomingStocks as $stock)
                            <tr>
                                <td><code>{{ $stock->rawMaterial ? $stock->rawMaterial->sku : 'N/A' }}</code></td>
                                <td class="font-weight-700 text-dark">{{ $stock->rawMaterial ? $stock->rawMaterial->name : 'Deleted Material' }}</td>
                                <td class="text-center text-success font-weight-800">+{{ (float)$stock->quantity }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $stock->rawMaterial ? $stock->rawMaterial->unit : '-' }}</span></td>
                                <td>{{ date('d-m-Y', strtotime($stock->incoming_date)) }}</td>
                                <td class="text-muted" style="max-width: 300px;">{{ $stock->notes ?: '-' }}</td>
                                <td class="text-center">
                                    @if(Auth::user()->isOwner() || Auth::user()->isGudang())
                                        <a href="{{ route('incoming-stocks.edit', $stock->id) }}" class="btn btn-sm btn-outline-primary border-0 rounded-circle" title="Edit/Koreksi">
                                            <i class="bi bi-pencil-square fs-6"></i>
                                        </a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Belum ada log stok masuk tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
