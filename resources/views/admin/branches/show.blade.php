@extends('layouts.app')

@section('title', 'Detail Cabang')
@section('page_title', 'Detail Informasi Cabang')

@section('content')
<div class="container-fluid p-0">
    <div class="mb-3">
        <a href="{{ route('branches.index') }}" class="btn btn-light rounded-3 font-weight-600">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Cabang
        </a>
    </div>

    <div class="row">
        <!-- Left Column: Branch Info -->
        <div class="col-lg-5 mb-4">
            <div class="card-custom mb-4">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <span class="text-dark font-weight-700"><i class="bi bi-shop me-2 text-danger"></i>Profil Utama Cabang</span>
                    @if($branch->status === 'active')
                        <span class="badge bg-success bg-opacity-10 text-success">Aktif</span>
                    @else
                        <span class="badge bg-danger bg-opacity-10 text-danger">Nonaktif</span>
                    @endif
                </div>
                <div class="p-4">
                    <table class="table table-borderless align-middle mb-0" style="font-size: 0.9rem;">
                        <tr>
                            <td class="text-muted font-weight-600 ps-0" style="width: 140px;">Nama Cabang</td>
                            <td class="text-dark font-weight-700">: {{ $branch->name }}</td>
                        </tr>

                        <tr>
                            <td class="text-muted font-weight-600 ps-0">Pengelola Cabang</td>
                            <td class="text-dark">: {{ $branch->pengelola_cabang ?: '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-600 ps-0">Tanggal Dibuka</td>
                            <td class="text-dark">: {{ $branch->opened_date ? date('d-m-Y', strtotime($branch->opened_date)) : '-' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted font-weight-600 ps-0">Alamat Lengkap</td>
                            <td class="text-dark" style="white-space: pre-line;">: {{ $branch->address }}</td>
                        </tr>
                    </table>

                    @if($branch->notes)
                        <div class="mt-4 pt-3 border-top">
                            <label class="text-muted font-weight-700 d-block mb-1" style="font-size: 0.8rem;">Catatan Cabang:</label>
                            <p class="text-dark bg-light p-3 rounded-3 m-0" style="font-size: 0.85rem; white-space: pre-line;">{{ $branch->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right Column: Riwayat Omset Cabang -->
        <div class="col-lg-7 mb-4">
            <div class="card-custom">
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <span class="text-dark font-weight-700"><i class="bi bi-cash-coin me-2 text-danger"></i>Riwayat Setoran Omset Harian</span>
                    <span class="badge bg-secondary bg-opacity-10 text-secondary">{{ $reports->total() }} Laporan</span>
                </div>
                <div class="p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.875rem;">
                            <thead>
                                <tr class="text-muted" style="font-size: 0.8rem;">
                                    <th>TANGGAL</th>
                                    <th class="text-end">TUNAI</th>
                                    <th class="text-end">QRIS</th>
                                    <th class="text-end">TOTAL OMSET</th>
                                    <th class="text-center">PORSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reports as $report)
                                    <tr>
                                        <td class="font-weight-700 text-dark">{{ date('d-m-Y', strtotime($report->report_date)) }}</td>
                                        <td class="text-end text-muted">Rp {{ number_format($report->cash_setoran, 0, ',', '.') }}</td>
                                        <td class="text-end text-muted">Rp {{ number_format($report->qris_setoran, 0, ',', '.') }}</td>
                                        <td class="text-end font-weight-700 text-dark">Rp {{ number_format($report->omset, 0, ',', '.') }}</td>
                                        <td class="text-center"><span class="badge bg-light text-dark border">{{ $report->portions_sold }}</span></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">Belum ada riwayat setoran omset untuk cabang ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $reports->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
