<div class="d-flex justify-content-between align-items-center pb-2 border-bottom mb-2">
    <h6 class="m-0 font-weight-700 text-dark">Notifikasi</h6>
    @if($notificationsCount > 0)
        <form action="{{ route('notifications.mark-all-read') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn btn-link p-0 text-decoration-none text-accent font-weight-600" style="font-size: 0.75rem;">Tandai semua dibaca</button>
        </form>
    @endif
</div>
<div style="max-height: 300px; overflow-y: auto;">
    <!-- 1. Custom Notifications -->
    @if($notificationsCount > 0)
        @foreach($notifications as $notif)
            <a href="{{ route('notifications.read-redirect', $notif->id) }}" class="dropdown-item d-flex align-items-start gap-2 py-2 px-1 border-bottom" style="white-space: normal;">
                <div class="bg-primary-subtle text-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; flex-shrink: 0;">
                    <i class="bi bi-info-circle-fill"></i>
                </div>
                <div>
                    <p class="m-0 font-weight-700 text-dark" style="font-size: 0.85rem;">{{ $notif->title }}</p>
                    <p class="m-0 text-muted" style="font-size: 0.75rem; line-height: 1.2;">{{ $notif->message }}</p>
                    <span class="text-muted" style="font-size: 0.65rem;">{{ $notif->created_at->diffForHumans() }}</span>
                </div>
            </a>
        @endforeach
    @endif

    <!-- 2. Safety Stock Alerts -->
    @if($safetyStockCount > 0)
        @foreach($safetyStockItems as $item)
            <a href="{{ route('raw-materials.index') }}" class="dropdown-item d-flex align-items-start gap-2 py-2 px-1 border-bottom" style="white-space: normal;">
                <div class="bg-danger-subtle text-danger rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; flex-shrink: 0;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <div>
                    <p class="m-0 font-weight-700 text-danger" style="font-size: 0.85rem;">Safety Stock Alert</p>
                    <p class="m-0 text-dark font-weight-600" style="font-size: 0.8rem;">{{ $item->name }}</p>
                    <span class="text-muted d-block" style="font-size: 0.75rem;">Stok kritis: {{ (float)$item->stock }} {{ $item->unit }} (Min: {{ (float)$item->safety_stock }})</span>
                </div>
            </a>
        @endforeach
    @endif

    <!-- Empty State -->
    @if($totalBadgeCount === 0)
        <div class="text-center py-4 text-muted">
            <i class="bi bi-bell-slash text-muted fs-2 d-block mb-2"></i>
            <span style="font-size: 0.85rem;">Tidak ada notifikasi baru.</span>
        </div>
    @endif
</div>
