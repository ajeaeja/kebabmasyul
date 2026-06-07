<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\Branch;
use App\Models\RawMaterial;
use App\Models\PartnerOrder;
use App\Models\BranchReport;
use App\Models\EditRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DashboardController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'role:owner,admin',
            new Middleware('role:owner', only: ['owner', 'exportOmset', 'exportStok']),
        ];
    }

    /**
     * Executive Dashboard for Owner.
     */
    public function owner(Request $request)
    {
        // 1. Dropdown Filters
        $periode = $request->input('periode', 'bulanan');
        $partnerId = $request->input('partner_id');
        $rawMaterialId = $request->input('raw_material_id');

        $allPartners = Partner::where('status', 'active')->orderBy('name', 'asc')->get();
        $allRawMaterials = RawMaterial::orderBy('name', 'asc')->get();

        // 2. Core aggregates
        $totalPartners = Partner::count();
        $activePartners = Partner::where('status', 'active')->count();
        $totalBranches = Branch::count();
        $internalBranchesCount = Branch::count();
        
        // Financial aggregates (Owner only)
        $totalInternalOmset = BranchReport::sum('omset');

        // Total orders quantity
        $totalOrderQty = (float) DB::table('partner_order_items')
            ->join('partner_orders', 'partner_orders.id', '=', 'partner_order_items.partner_order_id')
            ->when($partnerId, function ($q) use ($partnerId) {
                return $q->where('partner_orders.partner_id', $partnerId);
            })
            ->when($rawMaterialId, function ($q) use ($rawMaterialId) {
                return $q->where('partner_order_items.raw_material_id', $rawMaterialId);
            })
            ->sum('quantity');

        // Safety Stock Alerts
        $safetyStockAlerts = RawMaterial::whereColumn('stock', '<=', 'safety_stock')->get();
        $safetyAlertCount = $safetyStockAlerts->count();

        // Pending Edit Requests
        $pendingEditRequestsCount = EditRequest::where('status', 'pending')->count();

        // Red alert for missing daily reports for active internal branches
        $today = date('Y-m-d');
        $activeInternalBranches = Branch::where('status', 'active')->get();
        $missingReportBranches = [];
        foreach ($activeInternalBranches as $b) {
            $hasReport = BranchReport::where('branch_id', $b->id)->where('report_date', $today)->exists();
            if (!$hasReport) {
                $missingReportBranches[] = $b;
            }
        }

        // 3. Chart.js Data
        $labels = [];
        $dateRanges = [];

        if ($periode === 'today') {
            $dateStr = date('Y-m-d');
            $labels[] = date('d M Y');
            $dateRanges[] = [
                'start' => $dateStr . ' 00:00:00',
                'end' => $dateStr . ' 23:59:59',
                'date_only' => $dateStr,
                'label' => date('d M Y')
            ];
        } elseif ($periode === 'yesterday') {
            $dateStr = date('Y-m-d', strtotime('-1 day'));
            $labels[] = date('d M Y', strtotime('-1 day'));
            $dateRanges[] = [
                'start' => $dateStr . ' 00:00:00',
                'end' => $dateStr . ' 23:59:59',
                'date_only' => $dateStr,
                'label' => date('d M Y', strtotime('-1 day'))
            ];
        } elseif ($periode === 'last_7_days' || $periode === 'harian') {
            $periode = 'last_7_days';
            for ($i = 6; $i >= 0; $i--) {
                $dateStr = date('Y-m-d', strtotime("-$i days"));
                $labels[] = date('d M', strtotime($dateStr));
                $dateRanges[] = [
                    'start' => $dateStr . ' 00:00:00',
                    'end' => $dateStr . ' 23:59:59',
                    'date_only' => $dateStr,
                    'label' => date('d M', strtotime($dateStr))
                ];
            }
        } elseif ($periode === 'last_30_days') {
            for ($i = 29; $i >= 0; $i--) {
                $dateStr = date('Y-m-d', strtotime("-$i days"));
                $labels[] = date('d M', strtotime($dateStr));
                $dateRanges[] = [
                    'start' => $dateStr . ' 00:00:00',
                    'end' => $dateStr . ' 23:59:59',
                    'date_only' => $dateStr,
                    'label' => date('d M', strtotime($dateStr))
                ];
            }
        } elseif ($periode === 'mingguan') {
            for ($i = 3; $i >= 0; $i--) {
                $start = date('Y-m-d', strtotime("-$i weeks sunday"));
                $end = date('Y-m-d', strtotime("$start +6 days"));
                $labels[] = "W" . (4 - $i) . " (" . date('d M', strtotime($start)) . ")";
                $dateRanges[] = [
                    'start' => $start . ' 00:00:00',
                    'end' => $end . ' 23:59:59',
                    'label' => "Minggu ke-" . (4 - $i) . " (" . date('d M', strtotime($start)) . " - " . date('d M', strtotime($end)) . ")"
                ];
            }
        } else {
            $periode = 'bulanan';
            for ($i = 5; $i >= 0; $i--) {
                $monthStr = date('Y-m', strtotime("-$i months"));
                $labels[] = date('M Y', strtotime($monthStr . '-01'));
                $dateRanges[] = [
                    'start' => $monthStr . '-01 00:00:00',
                    'end' => date('Y-m-t', strtotime($monthStr . '-01')) . ' 23:59:59',
                    'month_only' => $monthStr,
                    'label' => date('F Y', strtotime($monthStr . '-01'))
                ];
            }
        }

        $chartBranchRevenue = [];
        foreach ($dateRanges as $range) {
            $sum = BranchReport::whereHas('branch')
                ->whereBetween('report_date', [substr($range['start'], 0, 10), substr($range['end'], 0, 10)])
                ->sum('omset');
            $chartBranchRevenue[] = (float) $sum;
        }

        $chartPartnerPurchases = [];
        foreach ($dateRanges as $range) {
            $sum = DB::table('partner_order_items')
                ->join('partner_orders', 'partner_orders.id', '=', 'partner_order_items.partner_order_id')
                ->whereBetween('partner_orders.order_date', [substr($range['start'], 0, 10), substr($range['end'], 0, 10)])
                ->when($partnerId, function ($q) use ($partnerId) {
                    return $q->where('partner_orders.partner_id', $partnerId);
                })
                ->when($rawMaterialId, function ($q) use ($rawMaterialId) {
                    return $q->where('partner_order_items.raw_material_id', $rawMaterialId);
                })
                ->sum(DB::raw('partner_order_items.quantity * partner_order_items.price'));
            $chartPartnerPurchases[] = (float) $sum;
        }

        // 4. Drill down lists payload format
        $drillDownData = [];
        foreach ($dateRanges as $idx => $range) {
            $revenueDetails = [];
            $reportsList = BranchReport::with('branch')
                ->whereHas('branch')
                ->whereBetween('report_date', [substr($range['start'], 0, 10), substr($range['end'], 0, 10)])
                ->get();
            foreach ($reportsList as $rep) {
                $revenueDetails[] = [
                    'branch_name' => $rep->branch ? $rep->branch->name : 'N/A',
                    'date' => date('d-m-Y', strtotime($rep->report_date)),
                    'cash' => number_format($rep->cash_setoran, 0, ',', '.'),
                    'qris' => number_format($rep->qris_setoran, 0, ',', '.'),
                    'total' => number_format($rep->omset, 0, ',', '.')
                ];
            }

            $purchaseDetails = [];
            $ordersList = DB::table('partner_order_items')
                ->join('partner_orders', 'partner_orders.id', '=', 'partner_order_items.partner_order_id')
                ->join('partners', 'partners.id', '=', 'partner_orders.partner_id')
                ->join('raw_materials', 'raw_materials.id', '=', 'partner_order_items.raw_material_id')
                ->select(
                    'partners.name as partner_name',
                    'raw_materials.name as material_name',
                    'partner_order_items.quantity',
                    'raw_materials.unit',
                    'partner_orders.order_date'
                )
                ->whereBetween('partner_orders.order_date', [substr($range['start'], 0, 10), substr($range['end'], 0, 10)])
                ->when($partnerId, function ($q) use ($partnerId) {
                    return $q->where('partner_orders.partner_id', $partnerId);
                })
                ->when($rawMaterialId, function ($q) use ($rawMaterialId) {
                    return $q->where('partner_order_items.raw_material_id', $rawMaterialId);
                })
                ->get();
            foreach ($ordersList as $ord) {
                $purchaseDetails[] = [
                    'partner_name' => $ord->partner_name,
                    'material_name' => $ord->material_name,
                    'qty' => (float)$ord->quantity,
                    'unit' => $ord->unit,
                    'date' => date('d-m-Y', strtotime($ord->order_date))
                ];
            }

            $drillDownData[$idx] = [
                'label' => $range['label'],
                'revenues' => $revenueDetails,
                'purchases' => $purchaseDetails
            ];
        }

        // 4. Chart.js Structured Payload
        $chartData = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Omset Cabang',
                    'data' => $chartBranchRevenue,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.3,
                    'fill' => true
                ]
            ]
        ];

        $partnerChart = [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Nominal Pembelian Mitra',
                    'data' => $chartPartnerPurchases,
                    'borderColor' => '#f59e0b',
                    'backgroundColor' => 'rgba(245, 158, 11, 0.1)',
                    'tension' => 0.3,
                    'fill' => true
                ]
            ]
        ];

        return view('owner.dashboard', compact(
            'periode', 'partnerId', 'rawMaterialId', 'allPartners', 'allRawMaterials',
            'totalPartners', 'activePartners', 'totalBranches', 'internalBranchesCount',
            'totalInternalOmset', 'totalOrderQty', 'safetyStockAlerts', 'safetyAlertCount',
            'pendingEditRequestsCount', 'missingReportBranches', 'labels', 
            'chartBranchRevenue', 'chartPartnerPurchases', 'drillDownData',
            'chartData', 'partnerChart'
        ));
    }

    /**
     * Export Rekap Omset Cabang
     */
    public function exportOmset()
    {
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=rekap_omset_cabang.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $reports = BranchReport::with('branch')->orderBy('report_date', 'desc')->get();

        $callback = function() use($reports) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID Laporan', 'Nama Cabang', 'Tipe Cabang', 'Tanggal Laporan', 'Setoran Tunai (IDR)', 'Transaksi QRIS (IDR)', 'Total Omset (IDR)', 'Porsi Terjual', 'Catatan']);

            foreach ($reports as $report) {
                fputcsv($file, [
                    $report->id,
                    $report->branch ? $report->branch->name : 'N/A',
                    'Internal',
                    $report->report_date,
                    $report->cash_setoran,
                    $report->qris_setoran,
                    $report->omset,
                    $report->portions_sold,
                    $report->notes
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Laporan Pembelian Mitra
     */
    public function exportPembelian()
    {
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=total_pembelian_mitra.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $orders = PartnerOrder::with(['partner', 'items'])->orderBy('order_date', 'desc')->get();

        $callback = function() use($orders) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID Pesanan', 'Nama Mitra', 'Tanggal Pesan', 'Target Tanggal Kirim', 'Total Item', 'Ongkos Kirim (IDR)', 'Total Tagihan (IDR)', 'Status Pembayaran', 'Metode Pembayaran', 'Status Pesanan', 'Info Ekspedisi']);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->partner ? $order->partner->name : 'N/A',
                    $order->order_date,
                    $order->shipping_date,
                    $order->items->count(),
                    $order->shipping_cost,
                    $order->total_price,
                    ucfirst($order->payment_status),
                    $order->payment_method ? ucfirst($order->payment_method) : '-',
                    ucfirst(str_replace('_', ' ', $order->status)),
                    $order->expedition_info
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Laporan Sisa Stok Gudang
     */
    public function exportStok()
    {
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=sisa_stok_gudang.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $materials = RawMaterial::orderBy('name', 'asc')->get();

        $callback = function() use($materials) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['SKU', 'Nama Bahan Baku', 'Stok Saat Ini', 'Stok Minimum', 'Satuan', 'Harga Satuan (IDR)', 'Status Aktif', 'Kondisi']);

            foreach ($materials as $material) {
                $kondisi = ($material->stock <= 0) ? 'Habis' : (($material->stock <= $material->safety_stock) ? 'Kritis' : 'Aman');
                fputcsv($file, [
                    $material->sku,
                    $material->name,
                    $material->stock,
                    $material->safety_stock,
                    $material->unit,
                    $material->price,
                    ucfirst($material->status),
                    $kondisi
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Export Laporan Data Mitra Aktif & MOU
     */
    public function exportMitra()
    {
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=data_mitra_aktif_mou.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $partners = Partner::orderBy('name', 'asc')->get();

        $callback = function() use($partners) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID Mitra', 'Nama Kemitraan', 'Nama Pemilik', 'No WhatsApp', 'Paket Usaha', 'Alamat', 'Tanggal Gabung', 'Tanggal Berakhir MOU', 'Status Aktif']);

            foreach ($partners as $partner) {
                fputcsv($file, [
                    $partner->id,
                    $partner->name,
                    $partner->owner_name,
                    $partner->phone,
                    $partner->jenis_paket,
                    $partner->address,
                    $partner->join_date,
                    $partner->mou_end_date,
                    ucfirst($partner->status)
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
