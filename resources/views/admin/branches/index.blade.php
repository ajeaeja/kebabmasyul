@extends('layouts.app')

@section('title', 'Data Cabang / Kedai')
@section('page_title', 'Kelola Cabang / Kedai')

@section('content')
<div class="container-fluid p-0">
    <!-- Search & Filter Card -->
    <div class="card-custom p-4 mb-4">
        <form action="{{ route('branches.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-5 col-12">
                <label for="search" class="form-label text-muted font-weight-600" style="font-size: 0.75rem;">Cari Nama Cabang</label>
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control border-start-0" id="search" name="search" placeholder="Masukkan nama cabang..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-4 col-12">
                <label for="status" class="form-label text-muted font-weight-600" style="font-size: 0.75rem;">Status Cabang</label>
                <select class="form-select" id="status" name="status">
                    <option value="">-- Semua Status --</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-3 col-12 d-grid gap-2 d-md-flex">
                <button type="submit" class="btn btn-accent flex-grow-1 py-2"><i class="bi bi-funnel me-1"></i> Filter</button>
                <a href="{{ route('branches.index') }}" class="btn btn-light border py-2"><i class="bi bi-arrow-counterclockwise"></i> Reset</a>
            </div>
        </form>
    </div>

    <!-- Main List Card -->
    <div class="card-custom p-0">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <div>
                <span class="text-dark font-weight-700">Daftar Cabang Kedai Masyul Kebab</span>
                <p class="m-0 text-muted d-none d-md-block" style="font-size: 0.8rem; font-weight: 400;">Mencakup cabang operasional pusat (internal) dan outlet franchise (mitra).</p>
            </div>
            <a href="{{ route('branches.create') }}" class="btn btn-accent rounded-3 font-weight-700 px-3 py-2 d-flex align-items-center justify-content-center" title="Tambah Cabang">
                <i class="bi bi-shop-window me-md-1"></i> <span class="d-none d-md-inline">Tambah Cabang</span>
            </a>
        </div>
        <div class="p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-muted" style="font-size: 0.8rem;">
                            <th>NAMA CABANG</th>
                            <th>TANGGAL DIBUKA</th>
                            <th>ALAMAT KEDAI</th>
                            <th class="text-center">STATUS</th>
                            <th class="text-center" style="width: 180px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($branches as $branch)
                            <tr style="font-size: 0.875rem;">
                                <td class="font-weight-700 text-dark">{{ $branch->name }}</td>
                                <td>{{ $branch->opened_date ? date('d-m-Y', strtotime($branch->opened_date)) : '-' }}</td>
                                <td style="max-width: 350px;" class="text-truncate">{{ $branch->address }}</td>
                                <td class="text-center">
                                    @if($branch->status === 'active')
                                        <span class="badge bg-success bg-opacity-10 text-success badge-pill-custom">Aktif</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger badge-pill-custom">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-2 justify-content-center">
                                        <a href="{{ route('branches.show', $branch->id) }}" class="btn btn-sm btn-outline-info border-0 rounded-circle" title="Detail Cabang">
                                            <i class="bi bi-eye fs-6"></i>
                                        </a>

                                        @if(Auth::user()->isAdmin())
                                            <a href="{{ route('branches.edit', $branch->id) }}" class="btn btn-sm btn-outline-primary border-0 rounded-circle" title="Edit/Ajukan Edit">
                                                <i class="bi bi-pencil-square fs-6"></i>
                                            </a>
                                            
                                            @if($branch->status === 'active')
                                                <form action="{{ route('branches.destroy', $branch->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menonaktifkan cabang ini?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 rounded-circle" title="Nonaktifkan Cabang">
                                                        <i class="bi bi-shop me-1" style="font-size: 1rem; color: #dc3545; filter: drop-shadow(0 0 1px #dc3545);"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">Belum ada data cabang terdaftar.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $branches->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
