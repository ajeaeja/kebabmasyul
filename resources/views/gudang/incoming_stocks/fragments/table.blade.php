<div class="p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr class="text-muted" style="font-size: 0.8rem;">
                    <th>SKU</th>
                    <th>NAMA BAHAN BAKU</th>
                    <th class="text-center">KUANTITAS MASUK</th>
                    <th>SATUAN</th>
                    <th>TANGGAL PENERIMAAN</th>
                    <th>CATATAN / SUPPLIER</th>
                    @if(Auth::user()->isGudang())
                        <th class="text-center" style="width: 120px;">AKSI</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($incomingStocks as $stock)
                    <tr style="font-size: 0.875rem;">
                        <td><code>{{ $stock->rawMaterial ? $stock->rawMaterial->sku : 'N/A' }}</code></td>
                        <td class="font-weight-700 text-dark">
                            {{ $stock->rawMaterial ? $stock->rawMaterial->name : 'Bahan Baku Dihapus' }}
                        </td>
                        <td class="text-center font-weight-800 text-dark">
                            +{{ (float)$stock->quantity }}
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $stock->rawMaterial ? $stock->rawMaterial->unit : '-' }}</span></td>
                        <td>{{ date('d-m-Y', strtotime($stock->incoming_date)) }}</td>
                        <td class="text-muted" style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $stock->notes }}">{{ $stock->notes ?: '-' }}</td>
                        @if(Auth::user()->isGudang())
                            <td class="text-center">
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="{{ route('incoming-stocks.edit', $stock->id) }}" class="btn btn-sm btn-outline-primary border-0 rounded-circle" title="Koreksi Data">
                                        <i class="bi bi-pencil-square fs-6"></i>
                                    </a>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Belum ada riwayat stok masuk tercatat.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-3">
        {{ $incomingStocks->links() }}
    </div>
</div>
