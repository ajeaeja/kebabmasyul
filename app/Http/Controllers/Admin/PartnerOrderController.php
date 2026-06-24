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
        $startDate = null;
        $endDate = null;
        $filter = $request->input('date_filter');

        if ($filter === 'today') {
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d');
        } elseif ($filter === 'last_7_days') {
            $startDate = date('Y-m-d', strtotime('-6 days'));
            $endDate = date('Y-m-d');
        } elseif ($filter === 'last_30_days') {
            $startDate = date('Y-m-d', strtotime('-29 days'));
            $endDate = date('Y-m-d');
        } elseif ($filter === 'custom' && $request->filled('start_date') && $request->filled('end_date')) {
            $startDate = $request->start_date;
            $endDate = $request->end_date;
        } else {
            $minDate = PartnerOrder::min('order_date');
            $startDate = $minDate ?: date('Y-m-d', strtotime('-29 days'));
            $endDate = date('Y-m-d');
        }

        // Apply date filter to orders query
        $query->whereBetween('order_date', [$startDate, $endDate]);

        $orders = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

        // Generate chronological dates range
        $dates = [];
        $current = strtotime($endDate);
        $start = strtotime($startDate);
        while ($current >= $start) {
            $dates[] = date('Y-m-d', $current);
            $current = strtotime('-1 day', $current);
        }
        $chronologicalDates = array_reverse($dates);

        $activePartners = Partner::where('status', 'active')->orderBy('name', 'asc')->get();

        // Load all orders for these partners within range
        $rangeOrders = PartnerOrder::whereIn('partner_id', $activePartners->pluck('id'))
            ->whereBetween('order_date', [$startDate, $endDate])
            ->get()
            ->groupBy(function($item) {
                return $item->partner_id . '_' . $item->order_date;
            });

        $partnerSummaries = [];
        foreach ($activePartners as $partner) {
            $totalPurchases = 0.0;
            $dailyTrend = [];
            
            foreach ($chronologicalDates as $d) {
                $key = $partner->id . '_' . $d;
                $dayOrders = isset($rangeOrders[$key]) ? $rangeOrders[$key] : collect();
                $dayTotal = 0.0;
                foreach ($dayOrders as $ord) {
                    $dayTotal += (float)$ord->total_price + (float)$ord->shipping_cost;
                }
                $dailyTrend[] = $dayTotal;
                $totalPurchases += $dayTotal;
            }

            $partnerSummaries[] = (object) [
                'partner' => $partner,
                'total_purchases' => $totalPurchases,
                'daily_trend' => $dailyTrend,
            ];
        }

        $partners = Partner::where('status', 'active')->orderBy('name', 'asc')->get();

        return view('admin.orders.index', compact('orders', 'partners', 'partnerSummaries'));
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
        $isWithin24Hours = $order->order_date && \Carbon\Carbon::parse($order->order_date)->diffInHours(now()) < 24;

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

        if (!$user->isOwner() && !$isWithin24Hours) {
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

        if (!$user->isOwner() && !$isWithin24Hours) {
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
