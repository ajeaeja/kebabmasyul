<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\EditRequest;
use App\Models\PartnerOrder;
use App\Models\PartnerOrderItem;
use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class EditRequestController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            'role:owner,admin',
            new Middleware('role:owner', only: ['approve', 'reject']),
        ];
    }

    /**
     * Display a listing of edit requests.
     */
    public function index()
    {
        $user = Auth::user();

        if ($user->isOwner()) {
            $requests = EditRequest::with(['user', 'reviewer'])
                ->orderBy('status', 'asc') // pending first
                ->orderBy('id', 'desc')
                ->get();
        } else {
            $requests = EditRequest::where('user_id', $user->id)
                ->with(['reviewer'])
                ->orderBy('id', 'desc')
                ->get();
        }

        return view('owner.edit_requests.index', compact('requests'));
    }

    /**
     * Display details of a specific edit request with comparative data.
     */
    public function show(EditRequest $editRequest)
    {
        // Admins can only view their own requests
        if (Auth::user()->isAdmin() && $editRequest->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk melihat pengajuan ini.');
        }

        $targetRecord = $editRequest->getTargetInstance();
        return view('owner.edit_requests.show', compact('editRequest', 'targetRecord'));
    }

    /**
     * Approve the request. Owner only.
     */
    public function approve(EditRequest $editRequest)
    {
        if ($editRequest->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        try {
            DB::beginTransaction();

            // Custom handling for complex PartnerOrder model
            if ($editRequest->model_type === PartnerOrder::class) {
                $order = PartnerOrder::findOrFail($editRequest->model_id);
                $reqData = $editRequest->requested_data;

                // 1. Revert stock based on old quantities first (since stock was decremented immediately)
                foreach ($order->items as $item) {
                    $rawMat = RawMaterial::find($item->raw_material_id);
                    if ($rawMat) {
                        $rawMat->increment('stock', $item->quantity);
                    }
                }

                // 2. Delete old order items
                $order->items()->delete();

                // 3. Re-create new items and apply stock changes
                foreach ($reqData['items'] as $itemData) {
                    PartnerOrderItem::create([
                        'partner_order_id' => $order->id,
                        'raw_material_id' => $itemData['raw_material_id'],
                        'quantity' => $itemData['quantity'],
                        'price' => $itemData['price'],
                    ]);

                    $rawMat = RawMaterial::find($itemData['raw_material_id']);
                    if ($rawMat) {
                        $rawMat->decrement('stock', $itemData['quantity']);
                    }
                }

                // 4. Update parent order with new fields
                $order->update([
                    'order_date' => $reqData['order_date'],
                    'shipping_date' => $reqData['shipping_date'] ?? null,
                    'expedition_info' => $reqData['expedition_info'] ?? null,
                    'shipping_cost' => $reqData['shipping_cost'] ?? 0.00,
                    'payment_status' => $reqData['payment_status'],
                    'payment_method' => $reqData['payment_method'] ?? null,
                    'total_price' => $reqData['total_price'],
                ]);

                // 5. Update edit request status
                $editRequest->update([
                    'status' => 'approved',
                    'reviewer_id' => Auth::id(),
                    'reviewed_at' => now(),
                ]);

            } else {
                $reqData = $editRequest->requested_data;
                if (isset($reqData['mou_path']) && str_starts_with($reqData['mou_path'], 'uploads/mou/temp/')) {
                    $tempPath = public_path($reqData['mou_path']);
                    if (file_exists($tempPath)) {
                        $filename = basename($reqData['mou_path']);
                        $newPath = 'uploads/mou/' . $filename;
                        if (!file_exists(public_path('uploads/mou'))) {
                            mkdir(public_path('uploads/mou'), 0777, true);
                        }
                        rename($tempPath, public_path($newPath));
                        $reqData['mou_path'] = $newPath;
                    }
                }

                $model = $editRequest->getTargetInstance();
                if (!$model) {
                    throw new \Exception('Data asli yang ingin diedit sudah tidak ditemukan.');
                }

                $model->update($reqData);

                $editRequest->update([
                    'status' => 'approved',
                    'reviewer_id' => Auth::id(),
                    'reviewed_at' => now(),
                ]);
            }

            DB::commit();
            return redirect()->route('edit-requests.index')->with('success', 'Pengajuan edit data disetujui dan perubahan telah diterapkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan saat menyetujui pengajuan: ' . $e->getMessage());
        }
    }

    /**
     * Reject the request. Owner only.
     */
    public function reject(EditRequest $editRequest)
    {
        if ($editRequest->status !== 'pending') {
            return back()->with('error', 'Pengajuan ini sudah diproses sebelumnya.');
        }

        $editRequest->reject(Auth::id());

        return redirect()->route('edit-requests.index')->with('success', 'Pengajuan edit data telah ditolak.');
    }
}
