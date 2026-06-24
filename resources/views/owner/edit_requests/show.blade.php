@extends('layouts.app')

@section('title', 'Detail Tinjauan Pengajuan #' . $editRequest->id)
@section('page_title', 'Tinjauan Pengajuan Koreksi Data')

@section('content')
<div class="container-fluid p-0" style="max-width: 900px; margin: 0 auto;">
    
    <!-- Meta Info Card -->
    <div class="card-custom p-4 mb-4">
        <div class="d-flex justify-content-between align-items-start mb-3 pb-2 border-bottom">
            <div>
                <h5 class="font-weight-800 text-dark m-0">Pengajuan Koreksi #{{ $editRequest->id }}</h5>
                <span class="text-muted" style="font-size: 0.85rem;">Diajukan oleh: <strong>{{ $editRequest->user ? $editRequest->user->name : 'N/A' }}</strong> pada {{ $editRequest->created_at->format('d F Y H:i') }}</span>
            </div>
            <div>
                @if($editRequest->status === 'pending')
                    <span class="badge bg-warning text-dark badge-pill-custom">Menunggu Persetujuan</span>
                @elseif($editRequest->status === 'approved')
                    <span class="badge bg-success text-white badge-pill-custom">Disetujui</span>
                @else
                    <span class="badge bg-danger text-white badge-pill-custom">Ditolak</span>
                @endif
            </div>
        </div>

        <div class="mb-2">
            <span class="text-muted text-uppercase font-weight-700 d-block" style="font-size: 0.75rem;">Alasan Pengajuan Koreksi Data:</span>
            <blockquote class="bg-light p-3 rounded-3 text-dark font-weight-600 m-0 mt-1" style="font-size: 0.9rem; border-left: 4px solid var(--accent-color);">
                "{{ $editRequest->reason }}"
            </blockquote>
        </div>

        @if($editRequest->status !== 'pending')
            <div class="mt-3 bg-light rounded-3 p-3" style="font-size: 0.85rem;">
                <span class="d-block text-muted"><strong>Detail Peninjauan:</strong></span>
                <span class="d-block mt-1">Ditinjau oleh: <strong>{{ $editRequest->reviewer ? $editRequest->reviewer->name : 'N/A' }}</strong></span>
                <span class="d-block">Tanggal diproses: {{ $editRequest->reviewed_at ? $editRequest->reviewed_at->format('d F Y H:i') : '-' }}</span>
            </div>
        @endif
    </div>

    <!-- Comparative Table Card -->
    <div class="card-custom p-4 mb-4">
        <h6 class="font-weight-700 text-dark border-bottom pb-2 mb-4"><i class="bi bi-arrow-left-right me-2 text-danger"></i>Perbandingan Data (Lama vs Baru)</h6>

        @if($editRequest->model_type === 'App\\Models\\PartnerOrder' || $editRequest->model_type === 'App\Models\PartnerOrder')
            <!-- Comparison for Complex PartnerOrder -->
            @php
                $orig = $editRequest->original_data;
                $req = $editRequest->requested_data;
            @endphp
            
            <div class="row mb-3">
                <div class="col-md-6 border-end">
                    <h6 class="font-weight-700 text-secondary border-bottom pb-1">Data Lama (Saat Ini)</h6>
                    <p class="m-0 mb-1" style="font-size: 0.85rem;">Tanggal Pesanan: <strong>{{ date('d-m-Y', strtotime($orig['order_date'])) }}</strong></p>
                    <p class="m-0 mb-1" style="font-size: 0.85rem;">Target Tanggal Kirim: <strong>{{ isset($orig['shipping_date']) ? date('d-m-Y', strtotime($orig['shipping_date'])) : '-' }}</strong></p>
                    <p class="m-0 mb-1" style="font-size: 0.85rem;">Status Bayar: <strong>{{ isset($orig['payment_status']) ? ucfirst($orig['payment_status']) : '-' }}</strong></p>
                    <p class="m-0 mb-1" style="font-size: 0.85rem;">Metode Bayar: <strong>{{ isset($orig['payment_method']) ? ucfirst($orig['payment_method']) : '-' }}</strong></p>
                    <p class="m-0 mb-1" style="font-size: 0.85rem;">Ongkos Kirim: <strong>Rp {{ number_format(isset($orig['shipping_cost']) ? $orig['shipping_cost'] : 0, 0, ',', '.') }}</strong></p>
                    <p class="m-0 mb-1" style="font-size: 0.85rem;">Ekspedisi: <strong>{{ isset($orig['expedition_info']) ? $orig['expedition_info'] : '-' }}</strong></p>
                    <p class="m-0 mb-3" style="font-size: 0.85rem;">Total Tagihan: <strong class="text-danger">Rp {{ number_format($orig['total_price'], 0, ',', '.') }}</strong></p>
                    
                    <strong class="d-block mb-1" style="font-size: 0.8rem;">Daftar Item:</strong>
                    <ul class="list-group list-group-flush" style="font-size: 0.8rem;">
                        @foreach($orig['items'] as $item)
                            <li class="list-group-item px-0 py-1 bg-transparent d-flex justify-content-between">
                                <span>{{ $item['raw_material_name'] }}</span>
                                <strong>{{ (float)$item['quantity'] }} unit</strong>
                            </li>
                        @endforeach
                    </ul>
                </div>
                <div class="col-md-6 bg-warning bg-opacity-10 rounded-3 p-3">
                    <h6 class="font-weight-700 text-warning border-bottom pb-1">Data Usulan Baru (Revisi)</h6>
                    <p class="m-0 mb-1" style="font-size: 0.85rem;">Tanggal Pesanan: <strong>{{ date('d-m-Y', strtotime($req['order_date'])) }}</strong></p>
                    <p class="m-0 mb-1" style="font-size: 0.85rem;">Target Tanggal Kirim: <strong>{{ isset($req['shipping_date']) ? date('d-m-Y', strtotime($req['shipping_date'])) : '-' }}</strong></p>
                    <p class="m-0 mb-1" style="font-size: 0.85rem;">Status Bayar: <strong>{{ isset($req['payment_status']) ? ucfirst($req['payment_status']) : '-' }}</strong></p>
                    <p class="m-0 mb-1" style="font-size: 0.85rem;">Metode Bayar: <strong class="text-danger">{{ isset($req['payment_method']) ? ucfirst($req['payment_method']) : '-' }}</strong></p>
                    <p class="m-0 mb-1" style="font-size: 0.85rem;">Ongkos Kirim: <strong class="text-danger">Rp {{ number_format(isset($req['shipping_cost']) ? $req['shipping_cost'] : 0, 0, ',', '.') }}</strong></p>
                    <p class="m-0 mb-1" style="font-size: 0.85rem;">Ekspedisi: <strong class="text-danger">{{ isset($req['expedition_info']) ? $req['expedition_info'] : '-' }}</strong></p>
                    <p class="m-0 mb-3" style="font-size: 0.85rem;">Total Tagihan: <strong class="text-danger">Rp {{ number_format($req['total_price'], 0, ',', '.') }}</strong></p>
                    
                    <strong class="d-block mb-1" style="font-size: 0.8rem;">Daftar Item Usulan:</strong>
                    <ul class="list-group list-group-flush" style="font-size: 0.8rem;">
                        @foreach($req['items'] as $item)
                            <li class="list-group-item px-0 py-1 bg-transparent d-flex justify-content-between">
                                <span>{{ $item['raw_material_name'] }}</span>
                                <strong class="text-danger">{{ (float)$item['quantity'] }} unit</strong>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

        @else
            <!-- Comparison for standard flat models (Partner, Branch, BranchReport) -->
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light" style="font-size: 0.8rem;">
                        <tr>
                            <th>NAMA FIELD / KOLOM</th>
                            <th>DATA SAAT INI (ASLI)</th>
                            <th class="table-warning">DATA USULAN BARU (KOREKSI)</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.875rem;">
                        @foreach($editRequest->requested_data as $field => $newValue)
                            @php
                                $oldValue = isset($editRequest->original_data[$field]) ? $editRequest->original_data[$field] : '-';
                                
                                // Format money if field is omset, cash_setoran, qris_setoran
                                if (in_array($field, ['omset', 'cash_setoran', 'qris_setoran'])) {
                                    $oldValue = 'Rp ' . number_format((float)$oldValue, 0, ',', '.');
                                    $newValue = 'Rp ' . number_format((float)$newValue, 0, ',', '.');
                                }
                                
                                // Resolve branch name
                                if ($field === 'branch_id') {
                                    $branchOld = \App\Models\Branch::find($oldValue);
                                    $branchNew = \App\Models\Branch::find($newValue);
                                    $oldValue = $branchOld ? $branchOld->name : $oldValue;
                                    $newValue = $branchNew ? $branchNew->name : $newValue;
                                }

                                // Resolve raw material name
                                if ($field === 'raw_material_id') {
                                    $oldMat = \App\Models\RawMaterial::find($oldValue);
                                    $newMat = \App\Models\RawMaterial::find($newValue);
                                    $oldValue = $oldMat ? $oldMat->name : $oldValue;
                                    $newValue = $newMat ? $newMat->name : $newValue;
                                }
                            @endphp
                            <tr>
                                <td class="font-weight-600 text-muted">{{ strtoupper(str_replace('_', ' ', $field)) }}</td>
                                <td class="text-dark">{{ $oldValue }}</td>
                                <td class="table-warning text-danger font-weight-700">{{ $newValue }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Actions Panel (Owner only, pending requests only) -->
    @if(Auth::user()->isOwner() && $editRequest->status === 'pending')
        <div class="card-custom p-4 border border-warning">
            <h6 class="font-weight-700 text-dark mb-3"><i class="bi bi-shield-fill-check me-2 text-warning"></i>Keputusan Persetujuan Owner</h6>
            <p class="text-muted mb-4" style="font-size: 0.85rem;">Menyetujui pengajuan ini akan otomatis menerapkan data koreksi ke tabel database operasional terkait.</p>
            
            <div class="d-flex gap-3 justify-content-end">
                <form action="{{ route('edit-requests.reject', $editRequest->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger font-weight-600 px-4 py-2 rounded-3">Tolak Pengajuan</button>
                </form>
                
                <form action="{{ route('edit-requests.approve', $editRequest->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success font-weight-700 px-4 py-2 rounded-3">Setujui & Terapkan Perubahan</button>
                </form>
            </div>
        </div>
    @endif

</div>
@endsection
