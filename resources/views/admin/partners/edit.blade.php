@extends('layouts.app')

@section('title', 'Edit Data Mitra')
@section('page_title', 'Edit Data Mitra')

@section('content')
<div class="container-fluid p-0" style="max-width: 800px; margin: 0 auto;">
    <div class="mb-3">
        <a href="{{ route('partners.index') }}" class="btn btn-light rounded-3 font-weight-600">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>
    <div class="card-custom">
        <div class="card-header-custom">
            <span class="text-dark font-weight-700">Form Koreksi Profil Mitra</span>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('partners.update', $partner->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="mb-3">
                    <label for="name" class="form-label font-weight-600">Nama Kemitraan / Outlet</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $partner->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="owner_name" class="form-label font-weight-600">Nama Pemilik</label>
                        <input type="text" class="form-control @error('owner_name') is-invalid @enderror" id="owner_name" name="owner_name" value="{{ old('owner_name', $partner->owner_name) }}" required>
                        @error('owner_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label font-weight-600">No. WhatsApp Bisnis</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $partner->phone) }}" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label font-weight-600">Alamat Lengkap Kedai / Outlet</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" required>{{ old('address', $partner->address) }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="jenis_paket" class="form-label font-weight-600">Jenis Paket Usaha</label>
                        <select class="form-select @error('jenis_paket') is-invalid @enderror" id="jenis_paket" name="jenis_paket" required>
                            <option value="Silver" {{ old('jenis_paket', $partner->jenis_paket) === 'Silver' ? 'selected' : '' }}>Silver</option>
                            <option value="Gold" {{ old('jenis_paket', $partner->jenis_paket) === 'Gold' ? 'selected' : '' }}>Gold</option>
                            <option value="Platinum" {{ old('jenis_paket', $partner->jenis_paket) === 'Platinum' ? 'selected' : '' }}>Platinum</option>
                        </select>
                        @error('jenis_paket')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="join_date" class="form-label font-weight-600">Tanggal Mulai Kerja Sama</label>
                        <input type="date" class="form-control @error('join_date') is-invalid @enderror" id="join_date" name="join_date" value="{{ old('join_date', $partner->join_date) }}" required>
                        @error('join_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="mou_end_date" class="form-label font-weight-600">Tanggal Akhir MOU</label>
                        <input type="date" class="form-control @error('mou_end_date') is-invalid @enderror" id="mou_end_date" name="mou_end_date" value="{{ old('mou_end_date', $partner->mou_end_date) }}">
                        @error('mou_end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="mou_document" class="form-label font-weight-600">Dokumen MOU Baru (Kosongkan jika tidak diubah)</label>
                    <input type="file" class="form-control @error('mou_document') is-invalid @enderror" id="mou_document" name="mou_document" accept=".pdf,image/*">
                    <small class="text-muted" style="font-size: 0.75rem;">Maksimal ukuran file: 5MB.</small>
                    @if($partner->mou_path)
                        <div class="mt-2">
                            <span class="text-success"><i class="bi bi-file-earmark-check-fill me-1"></i>Dokumen terunggah saat ini:</span>
                            <a href="{{ asset($partner->mou_path) }}" target="_blank" class="btn btn-sm btn-link text-decoration-none p-0 ms-1 font-weight-700">Lihat Dokumen</a>
                        </div>
                    @endif
                    @error('mou_document')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label font-weight-600">Status Kemitraan</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="active" {{ old('status', $partner->status) === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status', $partner->status) === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="notes" class="form-label font-weight-600">Catatan Tambahan</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Catatan tambahan...">{{ old('notes', $partner->notes) }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Admin Approval Requirement Fields -->
                @if(Auth::user()->isAdmin() && $partner->created_at->diffInHours(now()) > 24)
                    <div class="card bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-3 p-3 mb-4">
                        <h6 class="font-weight-700 text-dark mb-2"><i class="bi bi-shield-lock-fill text-warning me-1"></i>Persetujuan Owner Diperlukan</h6>
                        <p class="text-muted m-0 mb-3" style="font-size: 0.8rem;">Anda bertindak sebagai Admin. Perubahan data ini harus diajukan terlebih dahulu ke Owner pusat untuk mendapatkan persetujuan karena data dibuat lebih dari 24 jam yang lalu.</p>
                        
                        <label for="edit_reason" class="form-label font-weight-700 text-dark">Alasan Pengajuan Koreksi Data</label>
                        <textarea class="form-control bg-white @error('edit_reason') is-invalid @enderror" id="edit_reason" name="edit_reason" rows="2" placeholder="Contoh: Koreksi nomor telpon salah input dari WA..." required>{{ old('edit_reason') }}</textarea>
                        @error('edit_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <div class="d-flex gap-2 justify-content-end border-top pt-3">
                    <a href="{{ route('partners.index') }}" class="btn btn-light font-weight-600 px-4 rounded-3">Batal</a>
                    
                    @if(Auth::user()->isAdmin() && $partner->created_at->diffInHours(now()) > 24)
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
