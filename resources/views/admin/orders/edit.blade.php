@extends('layouts.app')

@section('title', 'Ajukan Edit Pesanan #' . $order->id)
@section('page_title', 'Ajukan Edit Pesanan #' . $order->id)

@section('content')
<div class="container-fluid p-0" style="max-width: 900px; margin: 0 auto;">
    <div class="mb-3">
        <a href="{{ route('orders.index') }}" class="btn btn-light rounded-3 font-weight-600">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>
    <div class="card-custom">
        <div class="card-header-custom">
            <span class="text-dark font-weight-700">Form Koreksi Pesanan Bahan Baku</span>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('orders.update', $order->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label font-weight-600">Mitra Kemitraan (Tidak dapat diubah)</label>
                        <input type="text" class="form-control bg-light" value="{{ $order->partner ? $order->partner->name : 'N/A' }}" disabled>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="order_date" class="form-label font-weight-600">Tanggal Pesanan</label>
                        <input type="date" class="form-control @error('order_date') is-invalid @enderror" id="order_date" name="order_date" value="{{ old('order_date', $order->order_date) }}" required>
                        @error('order_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="shipping_date" class="form-label font-weight-600">Target Tanggal Pengiriman</label>
                        <input type="date" class="form-control @error('shipping_date') is-invalid @enderror" id="shipping_date" name="shipping_date" value="{{ old('shipping_date', $order->shipping_date) }}">
                        @error('shipping_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="payment_status" class="form-label font-weight-600">Status Pembayaran</label>
                        <select class="form-select @error('payment_status') is-invalid @enderror" id="payment_status" name="payment_status" required>
                            <option value="belum_lunas" {{ old('payment_status', $order->payment_status) === 'belum_lunas' ? 'selected' : '' }}>Belum Lunas</option>
                            <option value="lunas" {{ old('payment_status', $order->payment_status) === 'lunas' ? 'selected' : '' }}>Lunas</option>
                        </select>
                        @error('payment_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="payment_method" class="form-label font-weight-600">Metode Pembayaran</label>
                        <select class="form-select @error('payment_method') is-invalid @enderror" id="payment_method" name="payment_method">
                            <option value="" selected>-- Pilih Metode Pembayaran --</option>
                            <option value="transfer" {{ old('payment_method', $order->payment_method) === 'transfer' ? 'selected' : '' }}>Transfer Bank</option>
                            <option value="qris" {{ old('payment_method', $order->payment_method) === 'qris' ? 'selected' : '' }}>QRIS</option>
                            <option value="cash" {{ old('payment_method', $order->payment_method) === 'cash' ? 'selected' : '' }}>Tunai / Cash</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="shipping_cost" class="form-label font-weight-600">Ongkos Kirim (Rp)</label>
                        <input type="number" step="0.01" class="form-control @error('shipping_cost') is-invalid @enderror" id="shipping_cost" name="shipping_cost" value="{{ old('shipping_cost', $order->shipping_cost) }}" required>
                        @error('shipping_cost')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label for="expedition_info" class="form-label font-weight-600">Keterangan Ekspedisi (Bus/Travel/Resi)</label>
                        <input type="text" class="form-control @error('expedition_info') is-invalid @enderror" id="expedition_info" name="expedition_info" value="{{ old('expedition_info', $order->expedition_info) }}">
                        @error('expedition_info')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Dynamic Item List Header -->
                <div class="d-flex justify-content-between align-items-center mt-4 mb-2 pb-2 border-bottom">
                    <h6 class="font-weight-700 text-dark m-0"><i class="bi bi-list-check me-2"></i>Koreksi Daftar Item Bahan Baku</h6>
                    <button type="button" class="btn btn-sm btn-outline-danger font-weight-600 px-3 py-1 rounded-pill" onclick="addRow()">
                        <i class="bi bi-plus-lg me-1"></i> Tambah Baris Bahan Baku
                    </button>
                </div>

                <div class="table-responsive mb-4">
                    <table class="table table-bordered align-middle" id="items-table">
                        <thead class="table-light" style="font-size: 0.8rem;">
                            <tr>
                                <th style="width: 60%;">Nama Bahan Baku</th>
                                <th style="width: 30%;">Kuantitas (Jumlah)</th>
                                <th class="text-center" style="width: 10%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="items-container">
                            <!-- Existing rows will be loaded by PHP/JS -->
                        </tbody>
                    </table>
                </div>

                <!-- Admin edit reason required -->
                @if($order->created_at->diffInHours(now()) > 24)
                    <div class="card bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-3 p-3 mb-4">
                        <h6 class="font-weight-700 text-dark mb-2"><i class="bi bi-shield-lock-fill text-warning me-1"></i>Persetujuan Owner Diperlukan</h6>
                        <p class="text-muted m-0 mb-3" style="font-size: 0.8rem;">Anda bertindak sebagai Admin Utama. Koreksi pesanan ini akan diajukan ke Owner untuk ditinjau dan disetujui karena data dibuat lebih dari 24 jam yang lalu.</p>
                        
                        <label for="edit_reason" class="form-label font-weight-700 text-dark">Alasan Pengajuan Koreksi Pesanan</label>
                        <textarea class="form-control bg-white @error('edit_reason') is-invalid @enderror" id="edit_reason" name="edit_reason" rows="2" placeholder="Contoh: Mitra merevisi pesanan roti kebab via WhatsApp chat..." required>{{ old('edit_reason') }}</textarea>
                        @error('edit_reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <div class="d-flex gap-2 justify-content-end border-top pt-3">
                    <a href="{{ route('orders.index') }}" class="btn btn-light font-weight-600 px-4 rounded-3">Batal</a>
                    @if($order->created_at->diffInHours(now()) > 24)
                        <button type="submit" class="btn btn-warning px-4 rounded-3 text-dark font-weight-600">Ajukan Koreksi Pesanan</button>
                    @else
                        <button type="submit" class="btn btn-accent px-4 rounded-3">Simpan Perubahan</button>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const rawMaterials = @json($rawMaterials);
    const existingItems = @json($order->items);
    let rowIdx = 0;

    // Load existing items on page load
    document.addEventListener("DOMContentLoaded", function() {
        if (existingItems && existingItems.length > 0) {
            existingItems.forEach(item => {
                addRow(item.raw_material_id, item.quantity);
            });
        } else {
            addRow();
        }
    });

    function addRow(selectedMatId = null, quantity = '') {
        const container = document.getElementById("items-container");
        
        let selectOptions = `<option value="" disabled selected>-- Pilih Bahan Baku --</option>`;
        rawMaterials.forEach(mat => {
            const selected = (selectedMatId == mat.id) ? 'selected' : '';
            selectOptions += `<option value="${mat.id}" ${selected}>${mat.name} (SKU: ${mat.sku} | Unit: ${mat.unit} | Harga: Rp ${new Intl.NumberFormat('id-ID').format(mat.price)})</option>`;
        });

        const newRow = document.createElement("tr");
        newRow.id = `row-${rowIdx}`;
        newRow.innerHTML = `
            <td>
                <select class="form-select" name="items[${rowIdx}][raw_material_id]" required onchange="updateRowUnit(${rowIdx})">
                    ${selectOptions}
                </select>
            </td>
            <td>
                <div class="input-group">
                    <input type="number" step="0.01" min="0.01" class="form-control" name="items[${rowIdx}][quantity]" value="${quantity}" placeholder="0.00" required>
                    <span class="input-group-text text-muted" id="unit-label-${rowIdx}">satuan</span>
                </div>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-outline-danger border-0 rounded-circle" onclick="removeRow(${rowIdx})">
                    <i class="bi bi-trash-fill"></i>
                </button>
            </td>
        `;
        
        container.appendChild(newRow);
        updateRowUnit(rowIdx);
        rowIdx++;
    }

    function removeRow(index) {
        const row = document.getElementById(`row-${index}`);
        const totalRows = document.querySelectorAll("#items-container tr").length;
        
        if (totalRows <= 1) {
            alert("Pesanan minimal harus memiliki satu jenis bahan baku.");
            return;
        }
        
        row.remove();
    }

    function updateRowUnit(index) {
        const select = document.querySelector(`select[name="items[${index}][raw_material_id]"]`);
        const val = select.value;
        const selectedMaterial = rawMaterials.find(m => m.id == val);
        
        const label = document.getElementById(`unit-label-${index}`);
        if (selectedMaterial) {
            label.textContent = selectedMaterial.unit;
        } else {
            label.textContent = "satuan";
        }
    }

    // Toggle payment method based on payment status
    const paymentStatusEl = document.getElementById('payment_status');
    const paymentMethodEl = document.getElementById('payment_method');

    function togglePaymentMethod() {
        if (paymentStatusEl.value === 'lunas') {
            paymentMethodEl.removeAttribute('disabled');
            paymentMethodEl.setAttribute('required', 'required');
        } else {
            paymentMethodEl.value = '';
            paymentMethodEl.setAttribute('disabled', 'disabled');
            paymentMethodEl.removeAttribute('required');
        }
    }

    if (paymentStatusEl && paymentMethodEl) {
        paymentStatusEl.addEventListener('change', togglePaymentMethod);
        togglePaymentMethod();
    }
</script>
@endsection
