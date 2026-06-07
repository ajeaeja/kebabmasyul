@extends('layouts.app')

@section('title', 'Persetujuan Edit Data (Approval)')
@section('page_title', 'Persetujuan Edit Data (Approval)')

@section('content')
<div class="container-fluid p-0">
    <div class="card-custom p-0">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <div>
                <span class="text-dark font-weight-700">Daftar Pengajuan Koreksi Data</span>
                <p class="m-0 text-muted" style="font-size: 0.8rem; font-weight: 400;">
                    @if(Auth::user()->isOwner())
                        Tinjau dan setujui atau tolak permintaan edit data dari Admin Utama.
                    @else
                        Daftar pengajuan edit data yang Anda kirimkan ke Owner.
                    @endif
                </p>
            </div>
        </div>
        <div class="p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="text-muted" style="font-size: 0.8rem;">
                            <th>ID</th>
                            <th>PEMOHON (ADMIN)</th>
                            <th>TIPE DATA</th>
                            <th>ALASAN KOREKSI</th>
                            <th>TANGGAL PENGAJUAN</th>
                            <th class="text-center">STATUS</th>
                            <th class="text-center" style="width: 150px;">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                            <tr style="font-size: 0.875rem;">
                                <td>#{{ $req->id }}</td>
                                <td class="font-weight-600 text-dark">
                                    {{ $req->user ? $req->user->name : 'Deleted Admin' }}
                                </td>
                                <td>
                                    @if($req->model_type === 'App\\Models\\BranchReport')
                                        <span class="badge bg-success bg-opacity-10 text-success">Laporan Omset</span>
                                    @elseif($req->model_type === 'App\\Models\\PartnerOrder')
                                        <span class="badge bg-warning bg-opacity-15 text-warning-emphasis">Pesanan Mitra</span>
                                    @elseif($req->model_type === 'App\\Models\\Partner')
                                        <span class="badge bg-primary bg-opacity-10 text-primary">Profil Mitra</span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary">Profil Cabang</span>
                                    @endif
                                </td>
                                <td style="max-width: 250px;" class="text-truncate text-muted" title="{{ $req->reason }}">
                                    "{{ $req->reason }}"
                                </td>
                                <td>{{ $req->created_at->format('d-m-Y H:i') }}</td>
                                <td class="text-center">
                                    @if($req->status === 'pending')
                                        <span class="badge bg-warning text-dark badge-pill-custom">Pending</span>
                                    @elseif($req->status === 'approved')
                                        <span class="badge bg-success text-white badge-pill-custom">Disetujui</span>
                                    @else
                                        <span class="badge bg-danger text-white badge-pill-custom">Ditolak</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('edit-requests.show', $req->id) }}" class="btn btn-sm btn-light border rounded-3 font-weight-600 px-3">
                                        Tinjau <i class="bi bi-eye ms-1"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Belum ada pengajuan edit data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
