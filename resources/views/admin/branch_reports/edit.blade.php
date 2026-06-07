@extends('layouts.app')

@section('title', 'Edit Laporan Omset')
@section('page_title', 'Edit Laporan Omset')

@section('content')
<div class="container-fluid p-0" style="max-width: 800px; margin: 0 auto;">
    <div class="mb-3">
        <a href="{{ route('branch-reports.index') }}" class="btn btn-light rounded-3 font-weight-600">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>
    <div class="card-custom">
        <div class="card-header-custom">
            <span class="text-dark font-weight-700">Form Koreksi Omset Harian Cabang</span>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('branch-reports.update', $branchReport->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="branch_id" class="form-label font-weight-600">Cabang / Kedai</label>
                    <select class="form-select @error('branch_id') is-invalid @enderror" id="branch_id" name="branch_id" required>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', $branchReport->branch_id) == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="report_date" class="form-label font-weight-600">Tanggal Omset Penjualan</label>
                        <input type="date" class="form-control @error('report_date') is-invalid @enderror" id="report_date" name="report_date" value="{{ old('report_date', $branchReport->report_date) }}" required>
                        @error('report_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="portions_sold" class="form-label font-weight-600">Jumlah Porsi Terjual</label>
                        <input type="number" class="form-control @error('portions_sold') is-invalid @enderror" id="portions_sold" name="portions_sold" value="{{ old('portions_sold', $branchReport->portions_sold) }}" placeholder="Contoh: 75" required>
                        @error('portions_sold')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="cash_setoran" class="form-label font-weight-600">Setoran Tunai (Rp)</label>
                        <input type="number" step="0.01" class="form-control @error('cash_setoran') is-invalid @enderror" id="cash_setoran" name="cash_setoran" value="{{ old('cash_setoran', $branchReport->cash_setoran) }}" required>
                        @error('cash_setoran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="qris_setoran" class="form-label font-weight-600">Setoran QRIS (Rp)</label>
                        <input type="number" step="0.01" class="form-control @error('qris_setoran') is-invalid @enderror" id="qris_setoran" name="qris_setoran" value="{{ old('qris_setoran', $branchReport->qris_setoran) }}" required>
                        @error('qris_setoran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="card bg-light border-0 mb-3">
                    <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
                        <span class="text-muted font-weight-600" style="font-size: 0.9rem;">Estimasi Total Omset (Tunai + QRIS):</span>
                        <strong class="text-danger fs-5" id="total_omset_display">Rp 0</strong>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label font-weight-600">Catatan / Keterangan</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" required>{{ old('notes', $branchReport->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Admin Approval Requirement Fields -->
                @if(Auth::user()->isAdmin() && $branchReport->created_at->diffInHours(now()) > 24)
                    <div class="card bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-3 p-3 mb-4">
                        <h6 class="font-weight-700 text-dark mb-2"><i class="bi bi-shield-lock-fill text-warning me-1"></i>Persetujuan Owner Diperlukan</h6>
                        <p class="text-muted m-0 mb-3" style="font-size: 0.8rem;">Anda bertindak sebagai Admin. Koreksi data omset ini akan diajukan ke Owner untuk ditinjau dan disetujui karena data dibuat lebih dari 24 jam yang lalu.</p>
                        
                        <label for="edit_reason" class="form-label font-weight-700 text-dark">Alasan Pengajuan Koreksi Omset</label>
                        <textarea class="form-control bg-white @error('edit_reason') is-invalid @enderror" id="edit_reason" name="edit_reason" rows="2" placeholder="Contoh: Salah memindahkan nominal dari chat grup WA..." required>{{ old('edit_reason') }}</textarea>
                        @error('edit_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <div class="d-flex gap-2 justify-content-end border-top pt-3">
                    <a href="{{ route('branch-reports.index') }}" class="btn btn-light font-weight-600 px-4 rounded-3">Batal</a>
                    
                    @if(Auth::user()->isAdmin() && $branchReport->created_at->diffInHours(now()) > 24)
                        <button type="submit" class="btn btn-warning px-4 rounded-3 text-dark font-weight-600">Ajukan Edit ke Owner</button>
                    @else
                        <button type="submit" class="btn btn-accent px-4 rounded-3">Perbarui Laporan</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const cashInput = document.getElementById('cash_setoran');
    const qrisInput = document.getElementById('qris_setoran');
    const totalDisplay = document.getElementById('total_omset_display');

    function calculateTotal() {
        const cash = parseFloat(cashInput.value) || 0;
        const qris = parseFloat(qrisInput.value) || 0;
        const total = cash + qris;
        totalDisplay.innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(total);
    }

    cashInput.addEventListener('input', calculateTotal);
    qrisInput.addEventListener('input', calculateTotal);

    // Initial calculation on load
    calculateTotal();
</script>
@endsection
