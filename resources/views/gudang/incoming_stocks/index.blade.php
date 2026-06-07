@extends('layouts.app')

@section('title', 'Log Riwayat Stok Masuk')
@section('page_title', 'Riwayat Stok Masuk Supplier')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Title & Subtitle from Stitch -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h4 class="font-weight-800 text-dark mb-1">Riwayat Stok Masuk Supplier</h4>
            <p class="text-muted m-0" style="font-size: 0.9rem;">Log transaksi penerimaan pasokan bahan baku yang masuk ke dalam gudang.</p>
        </div>
        <div class="d-flex gap-2">
            @if(Auth::user()->isGudang())
                <a href="{{ route('incoming-stocks.create') }}" class="btn btn-accent rounded-3 font-weight-700 px-4 py-2.5 d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">add</span>
                    <span>Tambah Pengadaan</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Main Table Section from Stitch -->
    <div class="card-custom p-0">
        <div class="card-header-custom border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
            <div>
                <span class="text-dark font-weight-700">Daftar Pengadaan Bahan Baku</span>
            </div>
            
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle font-weight-600 px-3 py-2 rounded-3 d-flex align-items-center justify-content-center" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Export Data">
                    <span class="material-symbols-outlined text-[18px] me-1">download</span>
                    <span>Export Data</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                    <li><a class="dropdown-item" href="{{ route('generic.export', ['type' => 'incoming-stocks', 'format' => 'xls']) }}"><i class="bi bi-file-earmark-excel-fill text-success me-2"></i> Export Excel (.xls)</a></li>
                    <li><a class="dropdown-item" href="{{ route('generic.export', ['type' => 'incoming-stocks', 'format' => 'doc']) }}"><i class="bi bi-file-earmark-word-fill text-primary me-2"></i> Export Word (.doc)</a></li>
                    <li><a class="dropdown-item" href="{{ route('generic.export', ['type' => 'incoming-stocks', 'format' => 'pdf']) }}" target="_blank"><i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i> Export PDF</a></li>
                </ul>
            </div>
        </div>
        <div class="p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border-collapse: collapse;">
                    <thead>
                        <tr class="bg-light text-muted uppercase font-weight-700" style="font-size: 0.75rem; border-bottom: 1px solid #f1f5f9;">
                            <th class="py-3 px-6">SKU</th>
                            <th class="py-3 px-6">Nama Bahan Baku</th>
                            <th class="py-3 px-6 text-center">Kuantitas Masuk</th>
                            <th class="py-3 px-6">Satuan</th>
                            <th class="py-3 px-6">Tanggal Masuk</th>
                            <th class="py-3 px-6">Catatan Pengadaan</th>
                            @if(Auth::user()->isOwner() || Auth::user()->isGudang())
                                <th class="py-3 px-6 text-center" style="width: 100px;">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($incomingStocks as $stock)
                            <tr class="transition-colors" style="font-size: 0.875rem;">
                                <td class="py-3.5 px-6 font-weight-700 text-primary">
                                    {{ $stock->rawMaterial ? $stock->rawMaterial->sku : 'N/A' }}
                                </td>
                                <td class="py-3.5 px-6 font-semibold text-on-surface">
                                    {{ $stock->rawMaterial ? $stock->rawMaterial->name : 'Deleted Material' }}
                                </td>
                                <td class="py-3.5 px-6 text-center text-emerald-600 font-weight-800" style="color: #059669 !important;">
                                    +{{ (float)$stock->quantity }}
                                </td>
                                <td class="py-3.5 px-6">
                                    <span class="bg-light text-dark px-2.5 py-1 rounded text-xs font-weight-600 border uppercase">
                                        {{ $stock->rawMaterial ? $stock->rawMaterial->unit : '-' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-6 text-muted">
                                    {{ date('d-m-Y', strtotime($stock->incoming_date)) }}
                                </td>
                                <td class="py-3.5 px-6 text-on-surface-variant italic" style="max-width: 300px;">
                                    {{ $stock->notes ?: '-' }}
                                </td>
                                @if(Auth::user()->isOwner() || Auth::user()->isGudang())
                                    <td class="py-3.5 px-6 text-center">
                                        <a href="{{ route('incoming-stocks.edit', $stock->id) }}" class="btn btn-sm btn-light border rounded-3 d-flex align-items-center justify-content-center mx-auto" style="width: 32px; height: 32px;" title="Koreksi">
                                            <span class="material-symbols-outlined text-muted" style="font-size: 18px;">edit_note</span>
                                        </a>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">Belum ada log stok masuk tercatat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-3 border-top bg-light d-flex justify-content-between align-items-center">
                <div class="text-muted" style="font-size: 0.85rem;">
                    Menampilkan <span class="font-weight-700 text-dark">{{ $incomingStocks->firstItem() ?? 0 }} - {{ $incomingStocks->lastItem() ?? 0 }}</span> dari <span class="font-weight-700 text-dark">{{ $incomingStocks->total() }}</span> pengadaan
                </div>
                <div>
                    {{ $incomingStocks->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
