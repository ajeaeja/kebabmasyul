@extends('layouts.app')

@section('title', 'Log Riwayat Stok Masuk')
@section('page_title', 'Riwayat Stok Masuk Supplier')

@section('content')
<div class="container-fluid p-0">
    <div class="card-custom p-0">
        <div class="card-header-custom d-flex justify-content-between align-items-center">
            <div>
                <span class="text-dark font-weight-700">Daftar Pengadaan Bahan Baku</span>
                <p class="m-0 text-muted d-none d-md-block" style="font-size: 0.8rem; font-weight: 400;">Log transaksi penerimaan pasokan bahan baku yang masuk ke dalam gudang.</p>
            </div>
            
            <div class="d-flex gap-2 align-items-center">
                @if(Auth::user()->isGudang())
                    <a href="{{ route('incoming-stocks.create') }}" class="btn btn-accent rounded-3 font-weight-700 px-3 py-2 d-flex align-items-center justify-content-center" title="Catat Stok Masuk">
                        <i class="bi bi-plus-lg me-md-1"></i> <span class="d-none d-md-inline">Catat Stok Masuk</span>
                    </a>
                @endif
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle font-weight-600 px-3 py-2 rounded-3 d-flex align-items-center justify-content-center" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false" title="Export Data">
                        <i class="bi bi-download me-md-1"></i> <span class="d-none d-md-inline">Export Data</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                        <li><a class="dropdown-item" href="{{ route('generic.export', ['type' => 'incoming-stocks', 'format' => 'xls']) }}"><i class="bi bi-file-earmark-excel-fill text-success me-2"></i> Export Excel (.xls)</a></li>
                        <li><a class="dropdown-item" href="{{ route('generic.export', ['type' => 'incoming-stocks', 'format' => 'doc']) }}"><i class="bi bi-file-earmark-word-fill text-primary me-2"></i> Export Word (.doc)</a></li>
                        <li><a class="dropdown-item" href="{{ route('generic.export', ['type' => 'incoming-stocks', 'format' => 'pdf']) }}" target="_blank"><i class="bi bi-file-earmark-pdf-fill text-danger me-2"></i> Export PDF</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div id="table-container">
            @include('gudang.incoming_stocks.fragments.table')
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const container = document.getElementById('table-container');
                function fetchPage(url) {
                    fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        container.innerHTML = data.html;
                        window.history.pushState(null, '', url);
                        attachPaginationLinks();
                    })
                    .catch(console.error);
                }
                function attachPaginationLinks() {
                    const links = container.querySelectorAll('a.page-link');
                    links.forEach(link => {
                        link.addEventListener('click', function (e) {
                            e.preventDefault();
                            const url = this.getAttribute('href');
                            if (url) fetchPage(url);
                        });
                    });
                }
                attachPaginationLinks();
            });
        </script>
    </div>
</div>
@endsection
