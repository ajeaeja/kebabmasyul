@extends('layouts.app')

@section('title', 'Persetujuan Edit Data (Approval)')
@section('page_title', 'Persetujuan Edit Data (Approval)')

@section('content')
@php
    $totalRequests = \App\Models\EditRequest::count();
    $pendingRequests = \App\Models\EditRequest::where('status', 'pending')->count();
    $approvedRequests = \App\Models\EditRequest::where('status', 'approved')->count();
    $rejectedRequests = \App\Models\EditRequest::where('status', 'rejected')->count();
@endphp

<div class="container-fluid p-0">
    <!-- Header Title & Subtitle from Stitch -->
    <!-- Header Title & Subtitle from Stitch -->
    <div class="mb-4 d-flex flex-column flex-md-row align-items-md-end justify-content-between gap-4">
        <div>
            <h4 class="font-weight-800 text-dark mb-1">Persetujuan Edit Data (Approval)</h4>
            <p class="text-muted m-0" style="font-size: 0.9rem;">
                @if(Auth::user()->isOwner())
                    Tinjau dan setujui atau tolak permintaan edit data dari Admin Utama untuk menjaga integritas data operasional.
                @else
                    Daftar pengajuan edit data yang Anda kirimkan ke Owner.
                @endif
            </p>
        </div>
    </div>

    <!-- Dashboard Overview Bento (Small Accents) from Stitch -->
    <div class="row g-4 mb-4">
        <!-- Card 1: Total Requests -->
        <div class="col-md-3 col-6">
            <div class="card-custom p-4 border-start border-4 h-100" style="border-left-color: var(--accent-color) !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted font-weight-700 uppercase tracking-wider mb-1" style="font-size: 0.7rem;">TOTAL PENGAJUAN</p>
                        <h4 class="font-weight-800 text-dark m-0">{{ $totalRequests }}</h4>
                    </div>
                    <span class="material-symbols-outlined p-2 rounded-3" style="color: var(--accent-color); background-color: rgba(238, 77, 45, 0.1);">pending_actions</span>
                </div>
                <p class="text-muted font-weight-700 mt-3 mb-0" style="font-size: 0.75rem;">
                    Seluruh riwayat pengajuan
                </p>
            </div>
        </div>

        <!-- Card 2: Pending Approval -->
        <div class="col-md-3 col-6">
            <div class="card-custom p-4 border-start border-4 h-100" style="border-left-color: #ff9800 !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted font-weight-700 uppercase tracking-wider mb-1" style="font-size: 0.7rem;">PENDING APPROVAL</p>
                        <h4 class="font-weight-800 text-warning m-0">{{ $pendingRequests }}</h4>
                    </div>
                    <span class="material-symbols-outlined p-2 rounded-3" style="color: #ff9800; background-color: rgba(255, 152, 0, 0.1);">hourglass_empty</span>
                </div>
                <p class="text-warning font-weight-700 mt-3 mb-0" style="font-size: 0.75rem;">
                    Butuh ulasan segera
                </p>
            </div>
        </div>

        <!-- Card 3: Approved -->
        <div class="col-md-3 col-6">
            <div class="card-custom p-4 border-start border-4 h-100" style="border-left-color: #2e7d32 !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted font-weight-700 uppercase tracking-wider mb-1" style="font-size: 0.7rem;">DISETUJUI</p>
                        <h4 class="font-weight-800 text-success m-0">{{ $approvedRequests }}</h4>
                    </div>
                    <span class="material-symbols-outlined p-2 rounded-3" style="color: #2e7d32; background-color: rgba(46, 125, 50, 0.1);">check_circle</span>
                </div>
                <p class="text-success font-weight-700 mt-3 mb-0" style="font-size: 0.75rem;">
                    Telah disetujui Owner
                </p>
            </div>
        </div>

        <!-- Card 4: Rejected -->
        <div class="col-md-3 col-6">
            <div class="card-custom p-4 border-start border-4 h-100" style="border-left-color: #c62828 !important;">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <p class="text-muted font-weight-700 uppercase tracking-wider mb-1" style="font-size: 0.7rem;">DITOLAK / BATAL</p>
                        <h4 class="font-weight-800 text-danger m-0">{{ $rejectedRequests }}</h4>
                    </div>
                    <span class="material-symbols-outlined p-2 rounded-3" style="color: #c62828; background-color: rgba(198, 40, 40, 0.1);">cancel</span>
                </div>
                <p class="text-danger font-weight-700 mt-3 mb-0" style="font-size: 0.75rem;">
                    Ditolak atau dibatalkan
                </p>
            </div>
        </div>
    </div>

    <!-- Data Table Card from Stitch -->
    <div class="card-custom rounded-xl overflow-hidden border border-outline-variant/10">
        <div class="bg-white px-6 py-3 border-b border-outline-variant d-flex align-items-center justify-content-between">
            <h4 class="font-title-md text-title-md text-on-surface m-0">Tabel Pengajuan</h4>
        </div>
        <div class="p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="border-collapse: collapse;">
                    <thead>
                        <tr class="bg-light text-muted uppercase font-weight-700" style="font-size: 0.75rem; border-bottom: 1px solid #f1f5f9;">
                            <th class="py-3 px-6">ID</th>
                            <th class="py-3 px-6">Pemohon (Admin)</th>
                            <th class="py-3 px-6">Tipe Data</th>
                            <th class="py-3 px-6">Alasan Koreksi</th>
                            <th class="py-3 px-6">Tanggal Pengajuan</th>
                            <th class="py-3 px-6 text-center">Status</th>
                            <th class="py-3 px-6 text-center" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($requests as $req)
                            <tr class="transition-colors group" style="font-size: 0.875rem;">
                                <td class="py-3.5 px-6 text-on-surface-variant font-bold">#{{ $req->id }}</td>
                                <td class="py-3.5 px-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="w-8 h-8 rounded-circle bg-danger bg-opacity-10 text-danger d-flex align-items-center justify-content-center font-bold" style="font-size: 0.8rem;">
                                            {{ strtoupper(substr($req->user ? $req->user->name : 'AD', 0, 2)) }}
                                        </div>
                                        <span class="font-weight-600 text-dark">{{ $req->user ? $req->user->name : 'Deleted Admin' }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-6">
                                    @if($req->model_type === 'App\\Models\\BranchReport')
                                        <span class="bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill font-weight-700 border border-primary border-opacity-20 uppercase" style="font-size: 11px;">Laporan Omset</span>
                                    @elseif($req->model_type === 'App\\Models\\PartnerOrder')
                                        <span class="bg-warning bg-opacity-10 text-warning px-3 py-1 rounded-pill font-weight-700 border border-warning border-opacity-20 uppercase" style="font-size: 11px;">Pesanan Mitra</span>
                                    @elseif($req->model_type === 'App\\Models\\Partner')
                                        <span class="bg-info bg-opacity-10 text-info px-3 py-1 rounded-pill font-weight-700 border border-info border-opacity-20 uppercase" style="font-size: 11px;">Profil Mitra</span>
                                    @else
                                        <span class="bg-secondary bg-opacity-10 text-secondary px-3 py-1 rounded-pill font-weight-700 border border-secondary border-opacity-20 uppercase" style="font-size: 11px;">Profil Cabang</span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 max-w-[250px] text-truncate text-muted" title="{{ $req->reason }}">
                                    "{{ $req->reason }}"
                                </td>
                                <td class="py-3.5 px-6 text-on-surface-variant font-body-md">{{ $req->created_at->format('d-m-Y H:i') }}</td>
                                <td class="py-3.5 px-6 text-center">
                                    @if($req->status === 'pending')
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1.5 font-weight-700" style="background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a !important;">
                                            Pending
                                        </span>
                                    @elseif($req->status === 'approved')
                                        <span class="badge bg-emerald-100 text-emerald-800 border border-emerald-200 rounded-pill px-3 py-1.5 font-weight-700" style="background-color: #d1fae5; color: #065f46; border-color: #a7f3d0 !important;">
                                            Disetujui
                                        </span>
                                    @else
                                        <span class="badge bg-red-100 text-red-800 border border-red-200 rounded-pill px-3 py-1.5 font-weight-700" style="background-color: #fee2e2; color: #991b1b; border-color: #fca5a5 !important;">
                                            Ditolak
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3.5 px-6 text-center">
                                    <a href="{{ route('edit-requests.show', $req->id) }}" class="btn btn-accent px-4 py-2 rounded-3 text-white font-weight-700 d-flex align-items-center justify-content-center gap-1 mx-auto" style="width: fit-content;">
                                        <span class="material-symbols-outlined text-white" style="font-size: 16px;">visibility</span> Tinjau
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">Belum ada pengajuan edit data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="px-6 py-3 border-top bg-light d-flex justify-content-between align-items-center">
                <div class="text-muted" style="font-size: 0.85rem;">
                    Menampilkan <span class="font-weight-700 text-dark">{{ $requests->firstItem() ?? 0 }} - {{ $requests->lastItem() ?? 0 }}</span> dari <span class="font-weight-700 text-dark">{{ $requests->total() }}</span> pengajuan
                </div>
                <div>
                    {{ $requests->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
