@extends('layouts.app')

@section('title', 'Tambah Bahan Baku')
@section('page_title', 'Tambah Bahan Baku')

@section('content')
<div class="container-fluid p-0" style="max-width: 800px; margin: 0 auto;">
    <div class="card-custom">
        <div class="card-header-custom">
            <span class="text-dark font-weight-700">Form Bahan Baku Baru</span>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('raw-materials.store') }}" method="POST">
                @csrf
                
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="sku" class="form-label font-weight-600">SKU / Kode Bahan</label>
                        <input type="text" class="form-control @error('sku') is-invalid @enderror" id="sku" name="sku" value="{{ old('sku') }}" placeholder="Contoh: BB001" required>
                        @error('sku')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-8 mb-3">
                        <label for="name" class="form-label font-weight-600">Nama Bahan Baku</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Tortilla Kebab Besar" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="stock" class="form-label font-weight-600">Stok Awal</label>
                        <input type="number" step="0.01" class="form-control @error('stock') is-invalid @enderror" id="stock" name="stock" value="{{ old('stock', 0) }}" required>
                        @error('stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="unit" class="form-label font-weight-600">Satuan</label>
                        <input type="text" class="form-control @error('unit') is-invalid @enderror" id="unit" name="unit" value="{{ old('unit') }}" placeholder="Contoh: pack, kg, tiang" required>
                        @error('unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="safety_stock" class="form-label font-weight-600">Safety Stock (Limit Alarm)</label>
                        <input type="number" step="0.01" class="form-control @error('safety_stock') is-invalid @enderror" id="safety_stock" name="safety_stock" value="{{ old('safety_stock', 10) }}" required>
                        <small class="text-muted" style="font-size: 0.7rem;">Alarm berbunyi jika stok <= batas ini.</small>
                        @error('safety_stock')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @if(Auth::user()->isOwner())
                    <div class="mb-4">
                        <label for="price" class="form-label font-weight-700 text-dark">Harga Jual ke Mitra (Rp)</label>
                        <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', 0) }}" placeholder="Contoh: 25000" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <div class="mb-4">
                    <label for="status" class="form-label font-weight-600">Status Aktif</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2 justify-content-end border-top pt-3">
                    <a href="{{ route('raw-materials.index') }}" class="btn btn-light font-weight-600 px-4 rounded-3">Batal</a>
                    <button type="submit" class="btn btn-accent px-4 rounded-3">Simpan Bahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
