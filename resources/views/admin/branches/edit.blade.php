@extends('layouts.app')

@section('title', 'Edit Data Cabang')
@section('page_title', 'Edit Data Cabang')

@section('content')
<div class="container-fluid p-0" style="max-width: 800px; margin: 0 auto;">
    <div class="card-custom">
        <div class="card-header-custom">
            <span class="text-dark font-weight-700">Form Koreksi Cabang Kedai</span>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('branches.update', $branch->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="name" class="form-label font-weight-600">Nama Cabang / Kedai</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $branch->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="pengelola_cabang" class="form-label font-weight-600">Nama Pengelola Cabang (Opsional)</label>
                    <input type="text" class="form-control @error('pengelola_cabang') is-invalid @enderror" id="pengelola_cabang" name="pengelola_cabang" value="{{ old('pengelola_cabang', $branch->pengelola_cabang) }}" placeholder="Contoh: Rian Apriyadi">
                    @error('pengelola_cabang')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="opened_date" class="form-label font-weight-600">Tanggal Dibuka</label>
                        <input type="date" class="form-control @error('opened_date') is-invalid @enderror" id="opened_date" name="opened_date" value="{{ old('opened_date', $branch->opened_date) }}" required>
                        @error('opened_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label font-weight-600">Status Operasional</label>
                        <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                            <option value="active" {{ old('status', $branch->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                            <option value="inactive" {{ old('status', $branch->status) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label font-weight-600">Alamat Lengkap Cabang</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" required>{{ old('address', $branch->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="notes" class="form-label font-weight-600">Catatan Cabang</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Catatan tambahan...">{{ old('notes', $branch->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Admin Approval Requirement Fields -->
                @if(Auth::user()->isAdmin())
                    <div class="card bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-3 p-3 mb-4">
                        <h6 class="font-weight-700 text-dark mb-2"><i class="bi bi-shield-lock-fill text-warning me-1"></i>Persetujuan Owner Diperlukan</h6>
                        <p class="text-muted m-0 mb-3" style="font-size: 0.8rem;">Anda bertindak sebagai Admin. Perubahan data cabang ini harus diajukan terlebih dahulu ke Owner pusat untuk mendapatkan persetujuan.</p>
                        
                        <label for="edit_reason" class="form-label font-weight-700 text-dark">Alasan Pengajuan Koreksi Data Cabang</label>
                        <textarea class="form-control bg-white @error('edit_reason') is-invalid @enderror" id="edit_reason" name="edit_reason" rows="2" placeholder="Contoh: Koreksi penulisan nama pengelola cabang yang salah dari WA..." required>{{ old('edit_reason') }}</textarea>
                        @error('edit_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <div class="d-flex gap-2 justify-content-end border-top pt-3">
                    <a href="{{ route('branches.index') }}" class="btn btn-light font-weight-600 px-4 rounded-3">Batal</a>
                    
                    @if(Auth::user()->isAdmin())
                        <button type="submit" class="btn btn-warning px-4 rounded-3 text-dark font-weight-600">Ajukan Edit ke Owner</button>
                    @else
                        <button type="submit" class="btn btn-accent px-4 rounded-3">Perbarui Data Sekarang</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
