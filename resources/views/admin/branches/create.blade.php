@extends('layouts.app')

@section('title', 'Tambah Cabang Baru')
@section('page_title', 'Tambah Cabang Baru')

@section('content')
<div class="container-fluid p-0" style="max-width: 800px; margin: 0 auto;">
    <div class="card-custom">
        <div class="card-header-custom">
            <span class="text-dark font-weight-700">Form Identitas Cabang Kedai</span>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('branches.store') }}" method="POST">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label font-weight-600">Nama Cabang / Kedai</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Cabang Utama Margonda" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="pengelola_cabang" class="form-label font-weight-600">Nama Pengelola Cabang (Opsional)</label>
                    <input type="text" class="form-control @error('pengelola_cabang') is-invalid @enderror" id="pengelola_cabang" name="pengelola_cabang" value="{{ old('pengelola_cabang') }}" placeholder="Contoh: Rian Apriyadi">
                    @error('pengelola_cabang')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="opened_date" class="form-label font-weight-600">Tanggal Dibuka</label>
                        <input type="date" class="form-control @error('opened_date') is-invalid @enderror" id="opened_date" name="opened_date" value="{{ old('opened_date') }}" required>
                        @error('opened_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label font-weight-600">Status Operasional</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label font-weight-600">Alamat Lengkap Cabang</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" placeholder="Masukkan alamat lengkap..." required>{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="notes" class="form-label font-weight-600">Catatan Cabang</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Masukkan catatan tambahan...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2 justify-content-end border-top pt-3">
                    <a href="{{ route('branches.index') }}" class="btn btn-light font-weight-600 px-4 rounded-3">Batal</a>
                    <button type="submit" class="btn btn-accent px-4 rounded-3">Simpan Cabang</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
