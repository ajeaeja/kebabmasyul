@extends('layouts.app')

@section('title', 'Edit Bahan Baku')
@section('page_title', 'Edit Bahan Baku')

@section('content')
<div class="container-fluid p-0" style="max-width: 800px; margin: 0 auto;">
    <div class="card-custom">
        <div class="card-header-custom">
            <span class="text-dark font-weight-700">Form Koreksi Detail Bahan Baku</span>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('raw-materials.update', $rawMaterial->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="sku" class="form-label font-weight-600">SKU / Kode Bahan</label>
                        <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" name="sku" value="{{ old('sku', $rawMaterial->sku) }}" required>
                        @error('sku')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-8 mb-3">
                        <label for="name" class="form-label font-weight-600">Nama Bahan Baku</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $rawMaterial->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="stock" class="form-label font-weight-600">Stok Fisik</label>
                        <input type="number" step="0.01" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', $rawMaterial->stock) }}" readonly>
                        <small class="text-muted" style="font-size: 0.725rem;">Stok hanya dapat diupdate melalui fitur <strong>Stok Masuk</strong> atau transaksi order.</small>
                        @error('stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="unit" class="form-label font-weight-600">Satuan</label>
                        <input type="text" class="form-control @error('unit') is-invalid @enderror" id="unit" name="unit" value="{{ old('unit', $rawMaterial->unit) }}" required>
                        @error('unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="safety_stock" class="form-label font-weight-600">Safety Stock (Limit Alarm)</label>
                        <input type="number" step="0.01" class="form-control @error('safety_stock') is-invalid @enderror" id="safety_stock" name="safety_stock" value="{{ old('safety_stock', $rawMaterial->safety_stock) }}" required>
                        @error('safety_stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @if(Auth::user()->isOwner())
                    <div class="mb-4">
                        <label for="price" class="form-label font-weight-700 text-dark">Harga Jual ke Mitra (Rp)</label>
                        <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', $rawMaterial->price) }}" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <div class="mb-4">
                    <label for="status" class="form-label font-weight-600">Status Aktif</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="active" {{ old('status', $rawMaterial->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status', $rawMaterial->status) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                @if(!Auth::user()->isOwner())
                    <div class="card bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-3 p-3 mb-4">
                        <h6 class="font-weight-700 text-dark mb-2"><i class="bi bi-shield-lock-fill text-warning me-1"></i>Persetujuan Owner Diperlukan</h6>
                        <p class="text-muted m-0 mb-3" style="font-size: 0.8rem;">Anda bertindak sebagai Tim Gudang. Perubahan data bahan baku ini harus diajukan terlebih dahulu ke Owner pusat untuk mendapatkan persetujuan.</p>
                        
                        <label for="edit_reason" class="form-label font-weight-700 text-dark">Alasan Pengajuan Koreksi Data</label>
                        <textarea class="form-control bg-white @error('edit_reason') is-invalid @enderror" id="edit_reason" name="edit_reason" rows="2" placeholder="Contoh: Koreksi stok fisik setelah opname..." required>{{ old('edit_reason') }}</textarea>
                        @error('edit_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <div class="d-flex gap-2 justify-content-end border-top pt-3">
                    <a href="{{ route('raw-materials.index') }}" class="btn btn-light font-weight-600 px-4 rounded-3">Batal</a>
                    @if(!Auth::user()->isOwner())
                        <button type="submit" class="btn btn-warning px-4 rounded-3 text-dark font-weight-600">Ajukan Edit ke Owner</button>
                    @else
                        <button type="submit" class="btn btn-accent px-4 rounded-3">Perbarui Bahan</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
