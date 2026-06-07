@extends('layouts.app')

@section('title', 'Koreksi Data Stok Masuk')
@section('page_title', 'Koreksi Data Stok Masuk')

@section('content')
<div class="container-fluid p-0" style="max-width: 800px; margin: 0 auto;">
    <div class="mb-3">
        <a href="{{ route('incoming-stocks.index') }}" class="btn btn-light rounded-3 font-weight-600">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>
    <div class="card-custom">
        <div class="card-header-custom">
            <span class="text-dark font-weight-700">Form Koreksi Laporan Stok Masuk</span>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('incoming-stocks.update', $incomingStock->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="raw_material_id" class="form-label font-weight-600">Pilih Bahan Baku</label>
                    <select class="form-select @error('raw_material_id') is-invalid @enderror" id="raw_material_id" name="raw_material_id" required>
                        @foreach($rawMaterials as $material)
                            <option value="{{ $material->id }}" {{ old('raw_material_id', $incomingStock->raw_material_id) == $material->id ? 'selected' : '' }}>
                                {{ $material->name }} (SKU: {{ $material->sku }} - Stok saat ini: {{ (float)$material->stock }} {{ $material->unit }})
                            </option>
                        @endforeach
                    </select>
                    @error('raw_material_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="quantity" class="form-label font-weight-600">Jumlah / Kuantitas Masuk</label>
                        <input type="number" step="0.01" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity', (float)$incomingStock->quantity) }}" placeholder="Contoh: 50.00" required>
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="incoming_date" class="form-label font-weight-600">Tanggal Penerimaan</label>
                        <input type="date" class="form-control @error('incoming_date') is-invalid @enderror" id="incoming_date" name="incoming_date" value="{{ old('incoming_date', $incomingStock->incoming_date) }}" required>
                        @error('incoming_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="notes" class="form-label font-weight-600">Catatan Penerimaan (Keterangan Supplier)</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Masukkan keterangan (contoh: Supplier UD Sapi Segar, Kondisi Baik, no PO dll)...">{{ old('notes', $incomingStock->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if(!Auth::user()->isOwner() && $incomingStock->created_at->diffInHours(now()) > 24)
                    <div class="card bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-3 p-3 mb-4">
                        <label for="edit_reason" class="form-label font-weight-700 text-dark">Alasan Pengajuan Koreksi Stok Masuk</label>
                        <textarea class="form-control bg-white @error('edit_reason') is-invalid @enderror" id="edit_reason" name="edit_reason" rows="2" placeholder="Contoh: Koreksi nominal salah input melebihi 24 jam dari WA..." required>{{ old('edit_reason') }}</textarea>
                        @error('edit_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted d-block mt-1">Pengeditan data di atas 24 jam memerlukan persetujuan Owner. Data asli tidak akan berubah sebelum disetujui.</small>
                    </div>
                @endif

                <div class="d-flex gap-2 justify-content-end border-top pt-3">
                    <a href="{{ route('incoming-stocks.index') }}" class="btn btn-light font-weight-600 px-4 rounded-3">Batal</a>
                    <button type="submit" class="btn btn-accent px-4 rounded-3">
                        @if(!Auth::user()->isOwner() && $incomingStock->created_at->diffInHours(now()) > 24)
                            Ajukan Persetujuan
                        @else
                            Simpan Perubahan
                        @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
