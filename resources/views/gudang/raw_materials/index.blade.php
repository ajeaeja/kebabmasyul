@extends('layouts.app')

@section('title', 'Stok Bahan Baku Gudang')
@section('page_title', 'Stok & Persediaan Gudang')

@section('content')
@php
    $totalSKU = \App\Models\RawMaterial::count();
    $criticalSKU = \App\Models\RawMaterial::whereColumn('stock', '<=', 'safety_stock')->count();
    
    // Sum stock * price
    $totalAsetValue = \App\Models\RawMaterial::sum(\DB::raw('stock * price'));
    
    // Barang Keluar (from completed orders last 7 days)
    $barangKeluar = \App\Models\PartnerOrderItem::whereHas('order', function($q) {
        $q->where('status', 'selesai')
          ->where('order_date', '>=', now()->subDays(7));
    })->sum('quantity');
@endphp

<div class="container-fluid p-0">
    <!-- Header Title & Subtitle from Stitch -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h4 class="font-weight-800 text-dark mb-1">Stok & Persediaan Gudang</h4>
            <p class="text-muted m-0" style="font-size: 0.9rem;">Kelola stok utama untuk operasional outlet kebab di seluruh cabang.</p>
        </div>
        <div class="d-flex gap-2">
            @if(Auth::user()->isGudang())
                <a href="{{ route('raw-materials.create') }}" class="btn btn-accent rounded-3 font-weight-700 px-4 py-2.5 d-flex align-items-center gap-2">
                    <span class="material-symbols-outlined text-[20px]">add</span>
                    <span>Tambah Bahan Baku</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Inventory Stats Bento Grid from Stitch -->
    <div class="row g-4 mb-4">
        <div class="col-md-3 col-6">
            <div class="card-custom p-4 border-start border-4 border-danger h-100" style="border-left-color: #b22204 !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted font-weight-700 uppercase tracking-wider mb-1" style="font-size: 0.7rem;">TOTAL SKU</p>
                        <h4 class="font-weight-800 text-dark m-0">{{ $totalSKU }} Item</h4>
                    </div>
                    <span class="material-symbols-outlined text-primary bg-danger bg-opacity-10 p-2 rounded-3" style="color: #b22204 !important;">inventory</span>
                </div>
                <p class="text-success font-weight-700 mt-3 mb-0" style="font-size: 0.75rem;">
                    <span class="material-symbols-outlined text-sm">trending_up</span> Aktif Terdaftar
                </p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card-custom p-4 border-start border-4 border-danger h-100" style="border-left-color: #dc3545 !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted font-weight-700 uppercase tracking-wider mb-1" style="font-size: 0.7rem;">STOK KRITIS</p>
                        <h4 class="font-weight-800 text-danger m-0">{{ $criticalSKU }} Item</h4>
                    </div>
                    <span class="material-symbols-outlined text-danger bg-danger bg-opacity-10 p-2 rounded-3">warning</span>
                </div>
                <p class="text-danger font-weight-700 mt-3 mb-0" style="font-size: 0.75rem;">
                    Perlu restock segera
                </p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card-custom p-4 border-start border-4 border-secondary h-100">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted font-weight-700 uppercase tracking-wider mb-1" style="font-size: 0.7rem;">BARANG KELUAR</p>
                        <h4 class="font-weight-800 text-dark m-0">{{ $barangKeluar }} pcs</h4>
                    </div>
                    <span class="material-symbols-outlined text-secondary bg-secondary-fixed p-2 rounded-3">output</span>
                </div>
                <p class="text-muted font-weight-700 mt-3 mb-0" style="font-size: 0.75rem;">
                    7 hari terakhir
                </p>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card-custom p-4 border-start border-4 border-warning h-100" style="border-left-color: #ffc107 !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted font-weight-700 uppercase tracking-wider mb-1" style="font-size: 0.7rem;">ESTIMASI ASET</p>
                        <h4 class="font-weight-800 text-dark m-0">Rp {{ number_format($totalAsetValue, 0, ',', '.') }}</h4>
                    </div>
                    <span class="material-symbols-outlined text-warning bg-warning bg-opacity-10 p-2 rounded-3" style="color: #ffc107 !important;">payments</span>
                </div>
                <p class="text-success font-weight-700 mt-3 mb-0" style="font-size: 0.75rem;">
                    <span class="material-symbols-outlined text-sm">check_circle</span> Valuasi Terkini
                </p>
            </div>
        </div>
    </div>

    <!-- Main Data Table Card from Stitch -->
    <div class="card-custom p-0">
        <div class="card-header-custom border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
            <div>
                <span class="text-dark font-weight-700">Daftar Bahan Baku Pusat</span>
            </div>
            
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle font-weight-600 px-3 py-2 rounded-3 d-flex align-items-center justify-content-center" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Export Data">
                    <span class="material-symbols-outlined text-[18px] me-1">download</span>
                    <span>Export Data</span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                    <li><a class="dropdown-item" href="{{ route('generic.export', ['type' => 'raw-materials', 'format' => 'xls']) }}"><i class="bi bi-file-earmark-excel-fill text-success me-2"></i> Export Excel (.xls)</a></li>
                    <li><a class="dropdown-item" href="{{ route('generic.export', ['type' => 'raw-materials', 'format' => 'doc']) }}"><i class="bi bi-file-earmark-word-fill text-primary me-2"></i> Export Word (.doc)</a></li>
                    <li><a class="dropdown-item" href="{{ route('generic.export', ['type' => 'raw-materials', 'format' => 'pdf']) }}" target="_blank"><i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i> Export PDF</a></li>
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
                            <th class="py-3 px-6 text-center">Stok Sekarang</th>
                            <th class="py-3 px-6 text-center">Safety Stock</th>
                            <th class="py-3 px-6">Satuan</th>
                            @if(!Auth::user()->isGudang())
                                <th class="py-3 px-6 text-end">Harga Mitra (Pack)</th>
                            @endif
                            <th class="py-3 px-6 text-center">Kondisi Stok</th>
                            <th class="py-3 px-6 text-center">Status Aktif</th>
                            @if(Auth::user()->isGudang())
                                <th class="py-3 px-6 text-center" style="width: 140px;">Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($materials as $material)
                            @php
                                $isCritical = $material->isBelowSafetyStock();
                            @endphp
                            <tr class="transition-colors group {{ $isCritical ? 'bg-danger bg-opacity-10 border-start border-4 border-danger' : '' }}" style="font-size: 0.875rem; border-left-color: #dc3545 !important;">
                                <td class="py-3.5 px-6 font-weight-700 {{ $isCritical ? 'text-danger' : 'text-primary' }}">{{ $material->sku }}</td>
                                <td class="py-3.5 px-6 font-semibold text-on-surface">{{ $material->name }}</td>
                                <td class="py-3.5 px-6 text-center font-weight-800 {{ $isCritical ? 'text-danger' : 'text-dark' }}">
                                    {{ (float)$material->stock }}
                                </td>
                                <td class="py-3.5 px-6 text-center text-muted">{{ (float)$material->safety_stock }}</td>
                                <td class="py-3.5 px-6">
                                    <span class="bg-light text-dark px-2.5 py-1 rounded text-xs font-weight-600 border uppercase">
                                        {{ $material->unit }}
                                    </span>
                                </td>
                                
                                @if(!Auth::user()->isGudang())
                                    <td class="py-3.5 px-6 text-end font-weight-700 text-dark">Rp {{ number_format($material->price, 0, ',', '.') }}</td>
                                @endif
                                
                                <td class="py-3.5 px-6 text-center">
                                    @if($material->stock <= 0)
                                        <span class="badge bg-red-100 text-red-800 border border-red-200 rounded-pill px-3 py-1 font-weight-700" style="background-color: #fee2e2; color: #991b1b; border-color: #fca5a5 !important;">Habis</span>
                                    @elseif($isCritical)
                                        <span class="badge bg-red-100 text-red-800 border border-red-200 rounded-pill px-3 py-1 font-weight-700" style="background-color: #fee2e2; color: #991b1b; border-color: #fca5a5 !important;">Kritis (≤Min)</span>
                                    @else
                                        <span class="badge bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-pill px-3 py-1 font-weight-700" style="background-color: #d1fae5; color: #065f46; border-color: #a7f3d0 !important;">Aman</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 text-center">
                                    @if($material->status === 'active')
                                        <span class="badge bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-pill px-3 py-1 font-weight-700" style="background-color: #d1fae5; color: #065f46; border-color: #a7f3d0 !important;">Aktif</span>
                                    @else
                                        <span class="badge bg-red-100 text-red-800 border border-red-200 rounded-pill px-3 py-1 font-weight-700" style="background-color: #fee2e2; color: #991b1b; border-color: #fca5a5 !important;">Nonaktif</span>
                                    @endif
                                </td>
                                @if(Auth::user()->isGudang())
                                    <td class="py-3.5 px-6">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="{{ route('raw-materials.edit', $material->id) }}" class="btn btn-sm btn-light border rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Edit">
                                                <span class="material-symbols-outlined text-muted" style="font-size: 18px;">edit</span>
                                            </a>
                                            
                                            <form action="{{ route('raw-materials.destroy', $material->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus bahan baku ini?')" class="m-0">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-light border rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Hapus">
                                                    <span class="material-symbols-outlined text-danger" style="font-size: 18px;">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-5">Belum ada bahan baku terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-3 border-top bg-light d-flex justify-content-between align-items-center">
                <div class="text-muted" style="font-size: 0.85rem;">
                    Menampilkan <span class="font-weight-700 text-dark">{{ $materials->firstItem() ?? 0 }} - {{ $materials->lastItem() ?? 0 }}</span> dari <span class="font-weight-700 text-dark">{{ $materials->total() }}</span> bahan baku
                </div>
                <div>
                    {{ $materials->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
