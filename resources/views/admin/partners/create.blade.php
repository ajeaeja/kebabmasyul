@extends('layouts.app')

@section('title', 'Tambah Mitra Baru')
@section('page_title', 'Tambah Mitra Baru')

@section('content')
<div class="container-fluid p-0" style="max-width: 800px; margin: 0 auto;">
    <div class="mb-3">
        <a href="{{ route('partners.index') }}" class="btn btn-light rounded-3 font-weight-600">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>
    <div class="card-custom">
        <div class="card-header-custom">
            <span class="text-dark font-weight-700">Form Profil Mitra Kemitraan</span>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('partners.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label font-weight-600">Nama Kemitraan / Outlet</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="Contoh: Masyul Kebab Margonda" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="owner_name" class="form-label font-weight-600">Nama Pemilik</label>
                        <input type="text" class="form-control @error('owner_name') is-invalid @enderror" id="owner_name" name="owner_name" value="{{ old('owner_name') }}" placeholder="Contoh: Budi Sudarsono" required>
                        @error('owner_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label font-weight-600">No. WhatsApp Bisnis</label>
                        <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" placeholder="Contoh: 081234567890" required>
                        <small class="text-muted" style="font-size: 0.75rem;">Masukkan format nomor HP aktif yang dapat dihubungi via WA.</small>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label font-weight-600">Alamat Lengkap Kedai / Outlet</label>
                    <textarea class="form-control @error('address') is-invalid @enderror" id="address" name="address" rows="3" placeholder="Masukkan alamat lengkap lokasi outlet kemitraan..." required>{{ old('address') }}</textarea>
                    @error('address')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="jenis_paket" class="form-label font-weight-600">Jenis Paket Usaha</label>
                        <select class="form-select @error('jenis_paket') is-invalid @enderror" id="jenis_paket" name="jenis_paket" required>
                            <option value="Silver" {{ old('jenis_paket') === 'Silver' ? 'selected' : '' }}>Silver</option>
                            <option value="Gold" {{ old('jenis_paket') === 'Gold' ? 'selected' : '' }}>Gold</option>
                            <option value="Platinum" {{ old('jenis_paket') === 'Platinum' ? 'selected' : '' }}>Platinum</option>
                        </select>
                        @error('jenis_paket')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="join_date" class="form-label font-weight-600">Tanggal Mulai Kerja Sama</label>
                        <input type="date" class="form-control @error('join_date') is-invalid @enderror" id="join_date" name="join_date" value="{{ old('join_date') }}" required>
                        @error('join_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="mou_end_date" class="form-label font-weight-600">Tanggal Akhir MOU</label>
                        <input type="date" class="form-control @error('mou_end_date') is-invalid @enderror" id="mou_end_date" name="mou_end_date" value="{{ old('mou_end_date') }}">
                        @error('mou_end_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="mou_document" class="form-label font-weight-600">Upload Dokumen MOU (PDF / Gambar)</label>
                    <input type="file" class="form-control @error('mou_document') is-invalid @enderror" id="mou_document" name="mou_document" accept=".pdf,image/*" required>
                    <small class="text-muted" style="font-size: 0.75rem;">Maksimal ukuran file: 5MB.</small>
                    @error('mou_document')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label font-weight-600">Status Kemitraan</label>
                    <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Aktif (Operasional Berjalan)</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Nonaktif (Tutup / Bermasalah)</option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="notes" class="form-label font-weight-600">Catatan Tambahan</label>
                    <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" rows="3" placeholder="Masukkan catatan tambahan jika diperlukan...">{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex gap-2 justify-content-end border-top pt-3">
                    <a href="{{ route('partners.index') }}" class="btn btn-light font-weight-600 px-4 rounded-3">Batal</a>
                    <button type="submit" class="btn btn-accent px-4 rounded-3">Simpan Data</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
