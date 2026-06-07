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
    <div class="mb-4 flex flex-col md:flex-row md:items-end justify-between gap-4">
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
        <div class="col-md-3 col-6">
            <div class="card-custom rounded-xl p-4 border-start border-4 border-danger h-100" style="border-left-color: #b22204 !important;">
                <div class="d-flex items-center justify-between mb-2">
                    <span class="text-muted font-weight-700 uppercase tracking-wider" style="font-size: 0.7rem;">Total Request</span>
                    <span class="material-symbols-outlined text-primary bg-danger bg-opacity-10 p-2 rounded-3" style="color: #b22204 !important;">pending_actions</span>
                </div>
                <div class="text-3xl font-bold text-on-surface">{{ $totalRequests }}</div>
                <div class="text-[10px] text-green-600 font-bold mt-2">Seluruh pengajuan</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card-custom rounded-xl p-4 border-start border-4 border-warning h-100" style="border-left-color: #ffc107 !important;">
                <div class="d-flex items-center justify-between mb-2">
                    <span class="text-muted font-weight-700 uppercase tracking-wider" style="font-size: 0.7rem;">Pending Approval</span>
                    <span class="material-symbols-outlined text-warning bg-warning bg-opacity-10 p-2 rounded-3" style="color: #ffc107 !important;">hourglass_empty</span>
                </div>
                <div class="text-3xl font-bold text-on-surface">{{ $pendingRequests }}</div>
                <div class="text-[10px] text-warning font-bold mt-2">Butuh ulasan segera</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card-custom rounded-xl p-4 border-start border-4 border-success h-100" style="border-left-color: #198754 !important;">
                <div class="d-flex items-center justify-between mb-2">
                    <span class="text-muted font-weight-700 uppercase tracking-wider" style="font-size: 0.7rem;">Approved</span>
                    <span class="material-symbols-outlined text-success bg-success bg-opacity-10 p-2 rounded-3" style="color: #198754 !important;">check_circle</span>
                </div>
                <div class="text-3xl font-bold text-on-surface">{{ $approvedRequests }}</div>
                <div class="text-[10px] text-success font-bold mt-2">Telah disetujui</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="card-custom rounded-xl p-4 border-start border-4 border-danger h-100" style="border-left-color: #dc3545 !important;">
                <div class="d-flex items-center justify-between mb-2">
                    <span class="text-muted font-weight-700 uppercase tracking-wider" style="font-size: 0.7rem;">Rejected</span>
                    <span class="material-symbols-outlined text-danger bg-danger bg-opacity-10 p-2 rounded-3">cancel</span>
                </div>
                <div class="text-3xl font-bold text-on-surface">{{ $rejectedRequests }}</div>
                <div class="text-[10px] text-danger font-bold mt-2">Ditolak / Batal</div>
            </div>
        </div>
    </div>

    <!-- Data Table Card from Stitch -->
    <div class="card-custom rounded-xl overflow-hidden border border-outline-variant/10">
        <div class="bg-white px-6 py-3 border-b border-outline-variant d-flex items-center justify-between">
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
                                        <span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-pill text-[11px] font-bold border border-blue-100 uppercase">Laporan Omset</span>
                                    @elseif($req->model_type === 'App\\Models\\PartnerOrder')
                                        <span class="bg-warning bg-opacity-10 text-warning-emphasis px-3 py-1 rounded-pill text-[11px] font-bold border border-warning border-opacity-20 uppercase">Pesanan Mitra</span>
                                    @elseif($req->model_type === 'App\\Models\\Partner')
                                        <span class="bg-primary bg-opacity-10 text-primary px-3 py-1 rounded-pill text-[11px] font-bold border border-primary border-opacity-20 uppercase">Profil Mitra</span>
                                    @else
                                        <span class="bg-secondary bg-opacity-10 text-secondary px-3 py-1 rounded-pill text-[11px] font-bold border border-secondary border-opacity-20 uppercase">Profil Cabang</span>
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
