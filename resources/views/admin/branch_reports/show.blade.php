@extends('layouts.app')

@section('title', 'Detail Laporan Omset')
@section('page_title', 'Detail Laporan Omset')

@section('content')
<div class="container-fluid p-0" style="max-width: 800px; margin: 0 auto;">
    <div class="mb-3">
        <a href="{{ route('branch-reports.index') }}" class="btn btn-light rounded-3 font-weight-600">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Laporan
        </a>
    </div>

    <div class="card-custom">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <span class="text-dark font-weight-700">
                <i class="bi bi-file-earmark-bar-graph me-2 text-danger"></i>Laporan Omset Harian
            </span>
            <span class="badge bg-light text-dark border">{{ date('d F Y', strtotime($branchReport->report_date)) }}</span>
        </div>
        <div class="card-body p-4">
            <table class="table table-borderless align-middle mb-0" style="font-size: 0.9rem;">
                <tr>
                    <td class="text-muted font-weight-600 ps-0" style="width: 200px;">Nama Cabang</td>
                    <td class="text-dark font-weight-700">: {{ $branchReport->branch ? $branchReport->branch->name : 'N/A' }}</td>
                </tr>
                <tr>
                    <td class="text-muted font-weight-600 ps-0">Tanggal Laporan</td>
                    <td class="text-dark">: {{ date('d-m-Y', strtotime($branchReport->report_date)) }}</td>
                </tr>
                <tr>
                    <td class="text-muted font-weight-600 ps-0">Setoran Tunai</td>
                    <td class="text-dark font-weight-600">: Rp {{ number_format($branchReport->cash_setoran, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-muted font-weight-600 ps-0">Transaksi QRIS</td>
                    <td class="text-dark font-weight-600">: Rp {{ number_format($branchReport->qris_setoran, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-muted font-weight-600 ps-0">Total Omset Harian</td>
                    <td class="text-danger font-weight-800">: Rp {{ number_format($branchReport->omset, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td class="text-muted font-weight-600 ps-0">Jumlah Porsi Terjual</td>
                    <td class="text-dark">: {{ $branchReport->portions_sold }} porsi</td>
                </tr>
            </table>

            @if($branchReport->notes)
                <div class="mt-4 pt-3 border-top">
                    <label class="text-muted font-weight-700 d-block mb-1" style="font-size: 0.8rem;">Catatan Tambahan (Pengirim WhatsApp):</label>
                    <p class="text-dark bg-light p-3 rounded-3 m-0" style="font-size: 0.85rem; white-space: pre-line;">{{ $branchReport->notes }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
