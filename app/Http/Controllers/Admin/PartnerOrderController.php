<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\RawMaterial;
use App\Models\PartnerOrder;
use App\Models\PartnerOrderItem;
use App\Models\EditRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PartnerOrderController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'role:owner,admin,gudang',
            new Middleware('role:admin', only: ['create', 'store', 'edit', 'update', 'destroy']),
            new Middleware('role:admin,gudang', only: ['updateStatus']),
        ];
    }

    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $query = PartnerOrder::with(['partner', 'items.rawMaterial']);

        // Filter by search (partner name or order ID)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('id', 'like', '%' . $search . '%')
                  ->orWhereHas('partner', function($pq) use ($search) {
                      $pq->where('name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filter by process status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by partner_id
        if ($request->filled('partner_id')) {
            $query->where('partner_id', $request->partner_id);
        }

        // Filter by payment status (non-gudang only)
        if ($request->filled('payment_status') && !Auth::user()->isGudang()) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range preset / custom
        if ($request->filled('date_filter')) {
            $filter = $request->date_filter;
            if ($filter === 'today') {
                $query->whereDate('order_date', date('Y-m-d'));
            } elseif ($filter === 'last_7_days') {
                $query->whereBetween('order_date', [date('Y-m-d', strtotime('-6 days')), date('Y-m-d')]);
            } elseif ($filter === 'last_30_days') {
                $query->whereBetween('order_date', [date('Y-m-d', strtotime('-29 days')), date('Y-m-d')]);
            } elseif ($filter === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
                $query->whereBetween('order_date', [$request->start_date, $request->end_date]);
            }
        }

        $orders = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        // Calculate chart data for partner purchases (dynamic based on filter)
        $labels = [];
        $dateRanges = [];
        $filter = $request->input('date_filter');

        if ($filter === 'today') {
            $labels[] = date('d M Y');
            $dateRanges[] = [
                'start' => date('Y-m-d') . ' 00:00:00',
                'end' => date('Y-m-d') . ' 23:59:59',
            ];
        } elseif ($filter === 'last_7_days') {
            for ($i = 6; $i >= 0; $i--) {
                $d = date('Y-m-d', strtotime("-$i days"));
                $labels[] = date('d M', strtotime($d));
                $dateRanges[] = [
                    'start' => $d . ' 00:00:00',
                    'end' => $d . ' 23:59:59',
                ];
            }
        } elseif ($filter === 'last_30_days') {
            for ($i = 29; $i >= 0; $i--) {
                $d = date('Y-m-d', strtotime("-$i days"));
                $labels[] = date('d M', strtotime($d));
                $dateRanges[] = [
                    'start' => $d . ' 00:00:00',
                    'end' => $d . ' 23:59:59',
                ];
            }
        } elseif ($filter === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            $start = strtotime($request->start_date);
            $end = strtotime($request->end_date);
            $diff = ($end - $start) / 86400;
            if ($diff < 0) {
                $filter = 'monthly';
            } else {
                for ($i = 0; $i <= $diff; $i++) {
                    $d = date('Y-m-d', strtotime("+$i days", $start));
                    $labels[] = date('d M', strtotime($d));
                    $dateRanges[] = [
                        'start' => $d . ' 00:00:00',
                        'end' => $d . ' 23:59:59',
                    ];
                }
            }
        } else {
            $filter = 'monthly';
        }

        if ($filter === 'monthly') {
            for ($i = 5; $i >= 0; $i--) {
                $monthStr = date('Y-m', strtotime("-$i months"));
                $labels[] = date('M Y', strtotime($monthStr . '-01'));
                $dateRanges[] = [
                    'start' => $monthStr . '-01 00:00:00',
                    'end' => date('Y-m-t', strtotime($monthStr . '-01')) . ' 23:59:59',
                ];
            }
        }

        $chartPartnerPurchases = [];
        foreach ($dateRanges as $range) {
            $sumQuery = DB::table('partner_order_items')
                ->join('partner_orders', 'partner_orders.id', '=', 'partner_order_items.partner_order_id');

            if ($request->filled('search')) {
                $search = $request->search;
                $sumQuery->where(function($q) use ($search) {
                    $q->where('partner_orders.id', 'like', '%' . $search . '%')
                      ->orExists(function($sub) use ($search) {
                          $sub->select(DB::raw(1))
                              ->from('partners')
                              ->whereColumn('partners.id', '=', 'partner_orders.partner_id')
                              ->where('partners.name', 'like', '%' . $search . '%');
                      });
                });
            }

            if ($request->filled('status')) {
                $sumQuery->where('partner_orders.status', $request->status);
            }

            if ($request->filled('partner_id')) {
                $sumQuery->where('partner_orders.partner_id', $request->partner_id);
            }

            if ($request->filled('payment_status') && !Auth::user()->isGudang()) {
                $sumQuery->where('partner_orders.payment_status', $request->payment_status);
            }

            $sum = $sumQuery->whereBetween('partner_orders.order_date', [substr($range['start'], 0, 10), substr($range['end'], 0, 10)])
                ->sum(DB::raw('partner_order_items.quantity * partner_order_items.price'));
            $chartPartnerPurchases[] = (float) $sum;
        }

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

        $partners = Partner::where('status', 'active')->orderBy('name', 'asc')->get();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('admin.orders.index', compact('orders', 'partnerChart', 'partners'))->fragment('table-section'),
                'chart_data' => $chartPartnerPurchases,
                'chart_labels' => $labels
            ]);
        }

        return view('admin.orders.index', compact('orders', 'partnerChart', 'partners'));
    }

    /**
     * Display the specified order details.
     */
    public function show(PartnerOrder $order)
    {
        $order->load(['partner', 'items.rawMaterial']);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create()
    {
        $partners = Partner::where('status', 'active')->orderBy('name', 'asc')->get();
        $rawMaterials = RawMaterial::where('stock', '>', 0)
            ->orderBy('name', 'asc')
            ->get();
        return view('admin.orders.create', compact('partners', 'rawMaterials'));
    }

    /**
     * Store a newly created order.
     */
    public function store(Request $request)
    {
        $request->validate([
            'partner_id' => 'required|exists:partners,id',
            'order_date' => 'required|date',
            'shipping_date' => 'nullable|date',
            'expedition_info' => 'nullable|string',
            'shipping_cost' => 'nullable|numeric|min:0',
            'payment_status' => 'required|in:lunas,belum_lunas',
            'payment_method' => 'required_if:payment_status,lunas|nullable|in:transfer,qris,cash',
            'items' => 'required|array|min:1',
            'items.*.raw_material_id' => 'required|exists:raw_materials,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();

            $paymentStatus = $request->payment_status;
            $paymentMethod = $paymentStatus === 'lunas' ? $request->payment_method : null;

            $order = PartnerOrder::create([
                'partner_id' => $request->partner_id,
                'order_date' => $request->order_date,
                'shipping_date' => $request->shipping_date,
                'expedition_info' => $request->expedition_info,
                'shipping_cost' => $request->shipping_cost ?? 0.00,
                'payment_status' => $paymentStatus,
                'payment_method' => $paymentMethod,
                'status' => 'menunggu_dipacking',
                'total_price' => 0.00,
            ]);

            $totalPrice = 0.00;

            foreach ($request->items as $itemData) {
                $rawMaterial = RawMaterial::find($itemData['raw_material_id']);
                $itemPrice = $rawMaterial->price;
                $quantity = $itemData['quantity'];
                
                PartnerOrderItem::create([
                    'partner_order_id' => $order->id,
                    'raw_material_id' => $rawMaterial->id,
                    'quantity' => $quantity,
                    'price' => $itemPrice,
                ]);

                // Reduce stock immediately
                $rawMaterial->decrement('stock', $quantity);

                $totalPrice += ($itemPrice * $quantity);
            }

            $order->update(['total_price' => $totalPrice]);

            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Pesanan bahan baku mitra berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['message' => 'Gagal membuat pesanan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Update order status (Gudang or Owner).
     */
    public function updateStatus(Request $request, PartnerOrder $order)
    {
        $request->validate([
            'status' => 'required|in:menunggu_dipacking,dipacking,dikirim,selesai',
        ]);

        $order->update(['status' => $request->status]);

        return redirect()->route('orders.show', $order->id)->with('success', 'Status pesanan berhasil diubah menjadi: ' . strtoupper(str_replace('_', ' ', $request->status)));
    }

    /**
     * Request Edit for Partner Order (Admin only).
     */
    public function edit(PartnerOrder $order)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Hanya Admin yang dapat mengajukan edit pesanan.');
        }
        $order->load(['partner', 'items.rawMaterial']);
        $rawMaterials = RawMaterial::where('status', 'active')->orderBy('name', 'asc')->get();
        return view('admin.orders.edit', compact('order', 'rawMaterials'));
    }

    /**
     * Submit Edit Request for Partner Order (Admin only).
     */
    public function update(Request $request, PartnerOrder $order)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isOwner()) {
            abort(403, 'Hanya Admin atau Owner yang dapat mengedit pesanan.');
        }

        $isOlderThan24Hours = $order->created_at->diffInHours(now()) > 24;

        $rules = [
            'order_date' => 'required|date',
            'shipping_date' => 'nullable|date',
            'expedition_info' => 'nullable|string',
            'shipping_cost' => 'nullable|numeric|min:0',
            'payment_status' => 'required|in:lunas,belum_lunas',
            'payment_method' => 'required_if:payment_status,lunas|nullable|in:transfer,qris,cash',
            'items' => 'required|array|min:1',
            'items.*.raw_material_id' => 'required|exists:raw_materials,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ];

        if (!$user->isOwner() && $isOlderThan24Hours) {
            $rules['edit_reason'] = 'required|string|min:5';
        }

        $request->validate($rules, [
            'edit_reason.required' => 'Anda harus memasukkan alasan pengajuan edit data.',
            'edit_reason.min' => 'Alasan pengajuan edit data minimal 5 karakter.',
        ]);

        // Calculate requested total price
        $requestedItems = [];
        $totalPrice = 0.00;
        foreach ($request->items as $itemData) {
            $rawMaterial = RawMaterial::find($itemData['raw_material_id']);
            $price = $rawMaterial->price;
            $quantity = $itemData['quantity'];
            
            $requestedItems[] = [
                'raw_material_id' => $rawMaterial->id,
                'raw_material_name' => $rawMaterial->name,
                'quantity' => $quantity,
                'price' => $price,
            ];
            $totalPrice += ($price * $quantity);
        }

        if (!$user->isOwner() && $isOlderThan24Hours) {
            // EditRequest flow
            $originalItems = [];
            foreach ($order->items as $item) {
                $originalItems[] = [
                    'raw_material_id' => $item->raw_material_id,
                    'raw_material_name' => $item->rawMaterial ? $item->rawMaterial->name : 'Deleted',
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                ];
            }

            $originalData = [
                'order_date' => $order->order_date,
                'shipping_date' => $order->shipping_date,
                'expedition_info' => $order->expedition_info,
                'shipping_cost' => $order->shipping_cost,
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method,
                'total_price' => $order->total_price,
                'items' => $originalItems
            ];

            $requestedData = [
                'order_date' => $request->order_date,
                'shipping_date' => $request->shipping_date,
                'expedition_info' => $request->expedition_info,
                'shipping_cost' => $request->shipping_cost ?? 0.00,
                'payment_status' => $request->payment_status,
                'payment_method' => $request->payment_status === 'lunas' ? $request->payment_method : null,
                'total_price' => $totalPrice,
                'items' => $requestedItems
            ];

            // Create Edit Request
            EditRequest::create([
                'user_id' => Auth::id(),
                'model_type' => PartnerOrder::class,
                'model_id' => $order->id,
                'original_data' => $originalData,
                'requested_data' => $requestedData,
                'reason' => $request->edit_reason,
                'status' => 'pending',
            ]);

            return redirect()->route('orders.index')->with('success', 'Pengajuan edit pesanan berhasil dikirim ke Owner.');
        }

        // Direct update flow (since Owner or <= 24 hours)
        try {
            DB::beginTransaction();

            // 1. Revert stock based on old quantities first
            foreach ($order->items as $item) {
                $rawMat = RawMaterial::find($item->raw_material_id);
                if ($rawMat) {
                    $rawMat->increment('stock', $item->quantity);
                }
            }

            // 2. Delete old order items
            $order->items()->delete();

            // 3. Re-create new items and apply stock changes
            foreach ($request->items as $itemData) {
                $rawMaterial = RawMaterial::find($itemData['raw_material_id']);
                $price = $rawMaterial->price;
                $quantity = $itemData['quantity'];

                PartnerOrderItem::create([
                    'partner_order_id' => $order->id,
                    'raw_material_id' => $rawMaterial->id,
                    'quantity' => $quantity,
                    'price' => $price,
                ]);

                $rawMaterial->decrement('stock', $quantity);
            }

            // 4. Update parent order
            $order->update([
                'order_date' => $request->order_date,
                'shipping_date' => $request->shipping_date,
                'expedition_info' => $request->expedition_info,
                'shipping_cost' => $request->shipping_cost ?? 0.00,
                'payment_status' => $request->payment_status,
                'payment_method' => $request->payment_status === 'lunas' ? $request->payment_method : null,
                'total_price' => $totalPrice,
            ]);

            DB::commit();
            return redirect()->route('orders.index')->with('success', 'Data pesanan berhasil diperbarui langsung.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['message' => 'Gagal memperbarui pesanan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Delete Order (Owner only).
     */
    public function destroy(PartnerOrder $order)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Hanya Admin yang dapat menghapus pesanan.');
        }

        // Revert stock since we decrease stock immediately upon order store
        foreach ($order->items as $item) {
            if ($item->rawMaterial) {
                $item->rawMaterial->increment('stock', $item->quantity);
            }
        }

        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dihapus.');
    }

    /**
     * Update payment status directly (Owner & Admin).
     */
    public function updatePayment(Request $request, PartnerOrder $order)
    {
        $user = Auth::user();
        if (!$user->isAdmin() && !$user->isOwner()) {
            abort(403, 'Hanya Admin atau Owner yang dapat mengubah status pembayaran.');
        }

        if ($request->has('mark_unpaid')) {
            $order->update([
                'payment_status' => 'belum_lunas',
                'payment_method' => null
            ]);
            return redirect()->route('orders.show', $order->id)->with('success', 'Status pembayaran berhasil diubah menjadi Belum Lunas.');
        }

        $request->validate([
            'payment_method' => 'required|in:transfer,qris,cash',
        ]);

        $order->update([
            'payment_status' => 'lunas',
            'payment_method' => $request->payment_method
        ]);

        return redirect()->route('orders.show', $order->id)->with('success', 'Status pembayaran berhasil diubah menjadi Lunas.');
    }
}
