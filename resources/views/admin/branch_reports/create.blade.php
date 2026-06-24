@extends('layouts.app')

@section('title', 'Input Omset Harian Cabang')
@section('page_title', 'Input Omset Harian Cabang')

@section('content')
<div class="container-fluid p-0" style="max-width: 800px; margin: 0 auto;">
    <div class="card-custom">
        <div class="card-header-custom">
            <span class="text-dark font-weight-700">Form Laporan Omset Harian</span>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('branch-reports.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="branch_id" class="form-label font-weight-600">Pilih Cabang / Kedai</label>
                    <select class="form-select @error('branch_id') is-invalid @enderror" id="branch_id" name="branch_id" required>
                        <option value="" disabled {{ !old('branch_id') && !request('branch_id') ? 'selected' : '' }}>-- Pilih Cabang Kedai --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', request('branch_id')) == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }} (Pengelola: {{ $branch->pengelola_cabang }})
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
                        <input type="date" class="form-control @error('report_date') is-invalid @enderror" id="report_date" name="report_date" value="{{ old('report_date', request('report_date', date('Y-m-d'))) }}" required>
                        @error('report_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="portions_sold" class="form-label font-weight-600">Jumlah Porsi Terjual</label>
                        <input type="number" class="form-control @error('portions_sold') is-invalid @enderror" id="portions_sold" name="portions_sold" value="{{ old('portions_sold') }}" placeholder="Contoh: 75" required>
                        @error('portions_sold')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="cash_setoran" class="form-label font-weight-600">Setoran Tunai (Rp)</label>
                        <input type="number" step="0.01" class="form-control @error('cash_setoran') is-invalid @enderror" id="cash_setoran" name="cash_setoran" value="{{ old('cash_setoran') }}" placeholder="Contoh: 600000" required>
                        @error('cash_setoran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="qris_setoran" class="form-label font-weight-600">Setoran QRIS (Rp)</label>
                        <input type="number" step="0.01" class="form-control @error('qris_setoran') is-invalid @enderror" id="qris_setoran" name="qris_setoran" value="{{ old('qris_setoran') }}" placeholder="Contoh: 900000" required>
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

                <div class="mb-4">
                    <label for="notes" class="form-label font-weight-600">Catatan Tambahan (Pengirim WhatsApp)</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Masukkan keterangan (contoh: Laporan disalin dari chat WhatsApp Budi Margonda)...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2 justify-content-end border-top pt-3">
                    <a href="{{ route('branch-reports.index') }}" class="btn btn-light font-weight-600 px-4 rounded-3">Batal</a>
                    <button type="submit" class="btn btn-accent px-4 rounded-3">Simpan Laporan</button>
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
</script>
@endsection
