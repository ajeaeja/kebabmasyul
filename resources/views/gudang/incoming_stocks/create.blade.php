@extends('layouts.app')

@section('title', 'Pencatatan Stok Masuk')
@section('page_title', 'Catat Stok Masuk Baru')

@section('content')
<div class="container-fluid p-0" style="max-width: 800px; margin: 0 auto;">
    <div class="mb-3">
        <a href="{{ route('incoming-stocks.index') }}" class="btn btn-light rounded-3 font-weight-600">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>
    <div class="card-custom">
        <div class="card-header-custom">
            <span class="text-dark font-weight-700">Form Laporan Stok Masuk Supplier</span>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('incoming-stocks.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="raw_material_id" class="form-label font-weight-600">Pilih Bahan Baku</label>
                    <select class="form-select @error('raw_material_id') is-invalid @enderror" id="raw_material_id" name="raw_material_id" required>
                        <option value="" disabled selected>-- Pilih Bahan Baku Yang Datang --</option>
                        @foreach($rawMaterials as $material)
                            <option value="{{ $material->id }}" {{ old('raw_material_id') == $material->id ? 'selected' : '' }}>
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
                        <input type="number" step="0.01" class="form-control @error('quantity') is-invalid @enderror" id="quantity" name="quantity" value="{{ old('quantity') }}" placeholder="Contoh: 50.00" required>
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="incoming_date" class="form-label font-weight-600">Tanggal Penerimaan</label>
                        <input type="date" class="form-control @error('incoming_date') is-invalid @enderror" id="incoming_date" name="incoming_date" value="{{ old('incoming_date', date('Y-m-d')) }}" required>
                        @error('incoming_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-4">
                    <label for="notes" class="form-label font-weight-600">Catatan Penerimaan (Keterangan Supplier)</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Masukkan keterangan (contoh: Supplier UD Sapi Segar, Kondisi Baik, no PO dll)...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2 justify-content-end border-top pt-3">
                    <a href="{{ route('incoming-stocks.index') }}" class="btn btn-light font-weight-600 px-4 rounded-3">Batal</a>
                    <button type="submit" class="btn btn-accent px-4 rounded-3">Catat & Tambah Stok</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
