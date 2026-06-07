@extends('layouts.app')

@section('title', 'Data Cabang / Kedai')
@section('page_title', 'Kelola Cabang / Kedai')

@section('content')
<div class="container-fluid p-0">
    <!-- Header Title & Subtitle from Stitch -->
    <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
            <h4 class="font-weight-800 text-dark mb-1">Kelola Cabang / Kedai</h4>
            <p class="text-muted m-0" style="font-size: 0.9rem;">Manage and monitor all active franchise locations.</p>
        </div>
        @if(Auth::user()->isAdmin())
            <a href="{{ route('branches.create') }}" class="btn btn-accent rounded-3 font-weight-700 px-4 py-2.5 d-flex align-items-center gap-2">
                <span class="material-symbols-outlined text-[20px]">add</span>
                <span>Tambah Cabang</span>
            </a>
        @endif
    </div>

    <!-- Search & Filter Card from Stitch -->
    <div class="card-custom p-4 mb-4">
        <form action="{{ route('branches.index') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-md-5 col-12">
                <div class="position-relative">
                    <span class="material-symbols-outlined position-absolute text-muted" style="left: 12px; top: 50%; transform: translateY(-50%); font-size: 20px;">search</span>
                    <input type="text" class="form-control" style="padding-left: 42px; py: 2.5;" id="search" name="search" placeholder="Cari nama cabang..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4 col-12">
                <div class="position-relative">
                    <select class="form-select" id="status" name="status" style="padding-right: 40px;">
                        <option value="">-- Semua Status --</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3 col-12 d-flex gap-2">
                <button type="submit" class="btn btn-accent flex-grow-1 py-2.5 d-flex align-items-center justify-content-center gap-1">
                    <span class="material-symbols-outlined text-[18px]">filter_list</span>
                    Filter
                </button>
                <a href="{{ route('branches.index') }}" class="btn btn-light border py-2.5 d-flex align-items-center justify-content-center gap-1">
                    <span class="material-symbols-outlined text-[18px]">refresh</span>
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Main List Card from Stitch -->
    <div class="card-custom p-0">
        <div class="card-header-custom border-bottom py-3 px-4">
            <span class="text-dark font-weight-700">Daftar Cabang</span>
        </div>
        <div class="p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border-collapse: collapse;">
                    <thead>
                        <tr class="bg-light text-muted uppercase font-weight-700" style="font-size: 0.75rem; border-bottom: 1px solid #f1f5f9;">
                            <th class="py-3 px-6">NAMA CABANG</th>
                            <th class="py-3 px-6">TANGGAL DIBUKA</th>
                            <th class="py-3 px-6">ALAMAT KEDAI</th>
                            <th class="py-3 px-6 text-center">STATUS</th>
                            <th class="py-3 px-6 text-center" style="width: 140px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($branches as $branch)
                            <tr class="transition-colors group" style="font-size: 0.875rem;">
                                <td class="py-3.5 px-6">
                                    <div class="font-semibold text-on-surface">{{ $branch->name }}</div>
                                    <div class="text-secondary text-sm">Pengelola: {{ $branch->pengelola_cabang ?? 'Pusat' }}</div>
                                </td>
                                <td class="py-3.5 px-6 text-on-surface-variant">
                                    {{ $branch->opened_date ? date('d M Y', strtotime($branch->opened_date)) : '-' }}
                                </td>
                                <td class="py-3.5 px-6 max-w-xs truncate text-on-surface-variant" title="{{ $branch->address }}">
                                    {{ $branch->address }}
                                </td>
                                <td class="py-3.5 px-6 text-center">
                                    @if($branch->status === 'active')
                                        <span class="badge bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-pill px-3 py-1.5 font-weight-700" style="background-color: #d1fae5; color: #065f46; border-color: #a7f3d0 !important;">
                                            <span class="d-inline-block rounded-circle bg-emerald-600 me-1" style="width: 6px; height: 6px; background-color: #059669;"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="badge bg-red-100 text-red-800 border border-red-200 rounded-pill px-3 py-1.5 font-weight-700" style="background-color: #fee2e2; color: #991b1b; border-color: #fca5a5 !important;">
                                            <span class="d-inline-block rounded-circle bg-red-600 me-1" style="width: 6px; height: 6px; background-color: #dc2626;"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('branches.show', $branch->id) }}" class="btn btn-sm btn-light border rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Detail">
                                            <span class="material-symbols-outlined text-muted" style="font-size: 18px;">visibility</span>
                                        </a>

                                        @if(Auth::user()->isAdmin())
                                            <a href="{{ route('branches.edit', $branch->id) }}" class="btn btn-sm btn-light border rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Edit">
                                                <span class="material-symbols-outlined text-muted" style="font-size: 18px;">edit</span>
                                            </a>
                                            
                                            @if($branch->status === 'active')
                                                <form action="{{ route('branches.destroy', $branch->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan cabang ini?')" class="m-0">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-light border rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="Nonaktifkan">
                                                        <span class="material-symbols-outlined text-danger" style="font-size: 18px;">block</span>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">Belum ada data cabang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-3 border-top bg-light d-flex justify-content-between align-items-center">
                <div class="text-muted" style="font-size: 0.85rem;">
                    Menampilkan <span class="font-weight-700 text-dark">{{ $branches->firstItem() ?? 0 }} - {{ $branches->lastItem() ?? 0 }}</span> dari <span class="font-weight-700 text-dark">{{ $branches->total() }}</span> cabang
                </div>
                <div>
                    {{ $branches->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
