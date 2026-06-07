@extends('layouts.app')

@section('title', 'Stok Bahan Baku Gudang')
@section('page_title', 'Stok & Persediaan Gudang')

@section('content')
<div class="container-fluid p-0">
    <!-- Header with actions -->
    <div class="card-custom p-0">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <div>
                <span class="text-dark font-weight-700">Daftar Bahan Baku Pusat</span>
                <p class="m-0 text-muted d-none d-md-block" style="font-size: 0.8rem; font-weight: 400;">Bahan baku utama untuk operasional outlet kebab.</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                @if(Auth::user()->isGudang())
                    <a href="{{ route('raw-materials.create') }}" class="btn btn-accent rounded-3 font-weight-700 px-3 py-2 d-flex align-items-center justify-content-center" title="Tambah Bahan Baku">
                        <i class="bi bi-plus-lg me-md-1"></i> <span class="d-none d-md-inline">Tambah Bahan Baku</span>
                    </a>
                @endif
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle font-weight-600 px-3 py-2 rounded-3 d-flex align-items-center justify-content-center" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Export Data">
                        <i class="bi bi-download me-md-1"></i> <span class="d-none d-md-inline">Export Data</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                        <li><a class="dropdown-item" href="{{ route('generic.export', ['type' => 'raw-materials', 'format' => 'xls']) }}"><i class="bi bi-file-earmark-excel-fill text-success me-2"></i> Export Excel (.xls)</a></li>
                        <li><a class="dropdown-item" href="{{ route('generic.export', ['type' => 'raw-materials', 'format' => 'doc']) }}"><i class="bi bi-file-earmark-word-fill text-primary me-2"></i> Export Word (.doc)</a></li>
                        <li><a class="dropdown-item" href="{{ route('generic.export', ['type' => 'raw-materials', 'format' => 'pdf']) }}" target="_blank"><i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i> Export PDF</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-muted" style="font-size: 0.8rem;">
                            <th>SKU</th>
                            <th>NAMA BAHAN BAKU</th>
                            <th class="text-center">STOK SEKARANG</th>
                            <th class="text-center">SAFETY STOCK</th>
                            <th>SATUAN</th>
                            @if(!Auth::user()->isGudang())
                                <th class="text-end">HARGA MITRA (PACK)</th>
                            @endif
                            <th class="text-center">KONDISI STOK</th>
                            <th class="text-center">STATUS AKTIF</th>
                            @if(Auth::user()->isGudang())
                                <th class="text-center" style="width: 150px;">AKSI</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($materials as $material)
                            @php
                                $isCritical = $material->isBelowSafetyStock();
                            @endphp
                            <tr style="font-size: 0.875rem;" class="{{ $isCritical ? 'table-danger' : '' }}">
                                <td><code>{{ $material->sku }}</code></td>
                                <td class="font-weight-700 text-dark">
                                    {{ $material->name }}
                                </td>
                                <td class="text-center font-weight-800 {{ $isCritical ? 'text-danger' : 'text-dark' }}">
                                    {{ (float)$material->stock }}
                                </td>
                                <td class="text-center text-muted">{{ (float)$material->safety_stock }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $material->unit }}</span></td>
                                
                                @if(!Auth::user()->isGudang())
                                    <td class="text-end font-weight-600 text-dark">Rp {{ number_format($material->price, 0, ',', '.') }}</td>
                                 @endif
                                
                                <td class="text-center">
                                    @if($material->stock <= 0)
                                        <span class="badge bg-danger">Habis</span>
                                    @elseif($isCritical)
                                        <span class="badge bg-danger alert-pulse">Kritis (≤ Min)</span>
                                    @else
                                        <span class="badge bg-success">Aman</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($material->status === 'active')
                                        <span class="badge bg-success bg-opacity-10 text-success badge-pill-custom">Aktif</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger badge-pill-custom">Nonaktif</span>
                                    @endif
                                </td>
                                @if(Auth::user()->isGudang())
                                    <td class="text-center">
                                        <div class="d-flex gap-2 justify-content-center">
                                            <a href="{{ route('raw-materials.edit', $material->id) }}" class="btn btn-sm btn-outline-primary border-0 rounded-circle" title="Edit Data">
                                                <i class="bi bi-pencil-square fs-6"></i>
                                            </a>
                                            
                                            <form action="{{ route('raw-materials.destroy', $material->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus bahan baku ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger border-0 rounded-circle" title="Hapus">
                                                    <i class="bi bi-trash fs-6"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">Belum ada bahan baku terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
