<?php

namespace App\Http\Controllers;

use App\Models\PartnerOrder;
use App\Models\BranchReport;
use App\Models\RawMaterial;
use App\Models\IncomingStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExportController extends Controller
{
    public function export($type, $format)
    {
        $format = strtolower($format);
        if (!in_array($format, ['xls', 'doc', 'pdf'])) {
            abort(404, 'Format tidak didukung.');
        }

        $title = '';
        $headers = [];
        $data = [];

        if ($type === 'orders') {
            $title = 'Laporan Pesanan Bahan Baku Mitra';
            $headers = ['ID Pesanan', 'Nama Mitra', 'Tanggal Pesan', 'Target Kirim', 'Total Item', 'Ongkos Kirim', 'Total Tagihan', 'Status Bayar', 'Metode Bayar', 'Status Pesanan', 'Ekspedisi'];
            
            $query = PartnerOrder::with(['partner', 'items']);
            
            if (request()->filled('search')) {
                $search = request()->search;
                $query->where(function($q) use ($search) {
                    $q->where('id', 'like', '%' . $search . '%')
                      ->orWhereHas('partner', function($pq) use ($search) {
                          $pq->where('name', 'like', '%' . $search . '%');
                      });
                });
            }
            if (request()->filled('status')) {
                $query->where('status', request()->status);
            }
            if (request()->filled('partner_id')) {
                $query->where('partner_id', request()->partner_id);
            }
            if (request()->filled('payment_status') && (!Auth::user() || !Auth::user()->isGudang())) {
                $query->where('payment_status', request()->payment_status);
            }
            if (request()->filled('date_filter')) {
                $filter = request()->date_filter;
                if ($filter === 'today') {
                    $query->whereDate('order_date', date('Y-m-d'));
                } elseif ($filter === 'yesterday') {
                    $query->whereDate('order_date', date('Y-m-d', strtotime('-1 day')));
                } elseif ($filter === 'last_7_days') {
                    $query->whereBetween('order_date', [date('Y-m-d', strtotime('-6 days')), date('Y-m-d')]);
                } elseif ($filter === 'last_30_days') {
                    $query->whereBetween('order_date', [date('Y-m-d', strtotime('-29 days')), date('Y-m-d')]);
                } elseif ($filter === 'custom' && request()->filled('start_date') && request()->filled('end_date')) {
                    $query->whereBetween('order_date', [request()->start_date, request()->end_date]);
                }
            }

            $orders = $query->orderBy('id', 'desc')->get();
            foreach ($orders as $o) {
                $data[] = [
                    '#' . $o->id,
                    $o->partner ? $o->partner->name : 'N/A',
                    date('d-m-Y', strtotime($o->order_date)),
                    $o->shipping_date ? date('d-m-Y', strtotime($o->shipping_date)) : '-',
                    $o->items->count() . ' Item',
                    'Rp ' . number_format($o->shipping_cost, 0, ',', '.'),
                    'Rp ' . number_format($o->total_price, 0, ',', '.'),
                    ucfirst($o->payment_status),
                    $o->payment_method ? ucfirst($o->payment_method) : '-',
                    ucfirst(str_replace('_', ' ', $o->status)),
                    $o->expedition_info ?: '-'
                ];
            }
        } elseif ($type === 'branch-reports') {
            $title = 'Laporan Omset Cabang';
            $headers = ['ID Laporan', 'Nama Cabang', 'Tanggal Laporan', 'Setoran Tunai', 'Setoran QRIS', 'Total Omset', 'Porsi Terjual', 'Catatan'];
            
            $query = BranchReport::with('branch');
            if (request()->filled('branch_id')) {
                $query->where('branch_id', request()->branch_id);
            }
            if (request()->filled('date_filter')) {
                $filter = request()->date_filter;
                if ($filter === 'today') {
                    $query->whereDate('report_date', date('Y-m-d'));
                } elseif ($filter === 'yesterday') {
                    $query->whereDate('report_date', date('Y-m-d', strtotime('-1 day')));
                } elseif ($filter === 'last_7_days') {
                    $query->whereBetween('report_date', [date('Y-m-d', strtotime('-6 days')), date('Y-m-d')]);
                } elseif ($filter === 'last_30_days') {
                    $query->whereBetween('report_date', [date('Y-m-d', strtotime('-29 days')), date('Y-m-d')]);
                } elseif ($filter === 'custom' && request()->filled('start_date') && request()->filled('end_date')) {
                    $query->whereBetween('report_date', [request()->start_date, request()->end_date]);
                }
            }

            $reports = $query->orderBy('report_date', 'desc')->orderBy('id', 'desc')->get();
            foreach ($reports as $r) {
                $data[] = [
                    '#' . $r->id,
                    $r->branch ? $r->branch->name : 'N/A',
                    date('d-m-Y', strtotime($r->report_date)),
                    'Rp ' . number_format($r->cash_setoran, 0, ',', '.'),
                    'Rp ' . number_format($r->qris_setoran, 0, ',', '.'),
                    'Rp ' . number_format($r->omset, 0, ',', '.'),
                    $r->portions_sold . ' Porsi',
                    $r->notes ?: '-'
                ];
            }
        } elseif ($type === 'raw-materials') {
            $title = 'Laporan Stok Bahan Baku Gudang';
            $isGudang = Auth::user() && Auth::user()->isGudang();
            $headers = $isGudang 
                ? ['SKU', 'Nama Bahan Baku', 'Stok Saat Ini', 'Stok Minimum', 'Satuan', 'Status']
                : ['SKU', 'Nama Bahan Baku', 'Stok Saat Ini', 'Stok Minimum', 'Satuan', 'Harga Satuan', 'Status'];

            $materials = RawMaterial::orderBy('name', 'asc')->get();
            foreach ($materials as $m) {
                $row = [
                    $m->sku,
                    $m->name,
                    (float)$m->stock,
                    (float)$m->safety_stock,
                    $m->unit
                ];
                if (!$isGudang) {
                    $row[] = 'Rp ' . number_format($m->price, 0, ',', '.');
                }
                $row[] = ucfirst($m->status);
                $data[] = $row;
            }
        } elseif ($type === 'incoming-stocks') {
            $title = 'Laporan Riwayat Stok Masuk Supplier';
            $headers = ['SKU', 'Nama Bahan Baku', 'Kuantitas Masuk', 'Satuan', 'Tanggal Masuk', 'Catatan'];
            $stocks = IncomingStock::with('rawMaterial')->orderBy('incoming_date', 'desc')->get();
            foreach ($stocks as $s) {
                $data[] = [
                    $s->rawMaterial ? $s->rawMaterial->sku : 'N/A',
                    $s->rawMaterial ? $s->rawMaterial->name : 'Deleted',
                    (float)$s->quantity,
                    $s->rawMaterial ? $s->rawMaterial->unit : '-',
                    date('d-m-Y', strtotime($s->incoming_date)),
                    $s->notes ?: '-'
                ];
            }
        } else {
            abort(404, 'Tipe laporan tidak ditemukan.');
        }

        $filename = str_replace(' ', '_', strtolower($title)) . '_' . date('Ymd_His');

        if ($format === 'xls') {
            return $this->exportHtmlTable($title, $headers, $data, 'application/vnd.ms-excel', $filename . '.xls');
        } elseif ($format === 'doc') {
            return $this->exportHtmlTable($title, $headers, $data, 'application/msword', $filename . '.doc');
        } elseif ($format === 'pdf') {
            return view('exports.print_pdf', compact('title', 'headers', 'data'));
        }
    }

    private function exportHtmlTable($title, $headers, $data, $contentType, $filename)
    {
        $html = '<!DOCTYPE html><html><head><meta charset="utf-8"><title>' . $title . '</title>';
        $html .= '<style>
            table { border-collapse: collapse; width: 100%; font-family: sans-serif; font-size: 12px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f2f2f2; font-weight: bold; }
            h2 { font-family: sans-serif; text-align: center; margin-bottom: 20px; }
        </style></head><body>';
        $html .= '<h2>' . $title . '</h2>';
        $html .= '<table><thead><tr>';
        foreach ($headers as $h) {
            $html .= '<th>' . htmlspecialchars($h) . '</th>';
        }
        $html .= '</tr></thead><tbody>';
        foreach ($data as $row) {
            $html .= '<tr>';
            foreach ($row as $val) {
                $html .= '<td>' . htmlspecialchars($val) . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table></body></html>';

        return response($html)
            ->header('Content-Type', $contentType)
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }
}
