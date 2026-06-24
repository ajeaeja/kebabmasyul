<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Masyul Kebab') - Kemitraan Management</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Custom Style System -->
    <style>
        :root {
            --bg-primary: #f8f9fa;
            --sidebar-bg: #111318;
            --sidebar-color: #94a3b8;
            --accent-color: #ee4d2d;
            --accent-hover: #d23d1e;
            --text-dark: #1e293b;
            --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
            --transition-speed: 0.25s;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-dark);
            min-height: 100vh;
            overflow-x: hidden;
            zoom: 80%;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            height: 125vh;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1050;
            padding: 1.5rem 1rem;
            transition: all var(--transition-speed) ease;
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.05);
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .sidebar-brand {
            padding: 0.5rem 1rem 1.5rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            margin-bottom: 1.5rem;
        }

        .sidebar-brand h5 {
            font-weight: 800;
            color: #fff;
            letter-spacing: 0.5px;
            margin: 0;
        }

        .sidebar-brand span {
            color: var(--accent-color);
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: var(--sidebar-color);
            border-radius: 8px;
            margin-bottom: 0.4rem;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.925rem;
            transition: all var(--transition-speed) ease;
        }

        .nav-link-custom i {
            font-size: 1.2rem;
            margin-right: 0.85rem;
            transition: all var(--transition-speed) ease;
        }

        .nav-link-custom:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.05);
        }

        .nav-link-custom.active {
            color: #fff;
            background: linear-gradient(135deg, var(--accent-color), #ff763b);
            box-shadow: 0 4px 15px rgba(238, 77, 45, 0.35);
        }

        .nav-link-custom.active i {
            color: #fff;
        }

        .sidebar-section-title {
            color: rgba(255, 255, 255, 0.25);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: 1rem 1rem 0.5rem 1rem;
            margin-top: 1rem;
        }

        /* Main Content Styling */
        .main-content {
            margin-left: 260px;
            padding: 0 0 2rem 0;
            min-height: 100vh;
            transition: all var(--transition-speed) ease;
        }

        /* Top Navbar */
        .top-navbar {
            background-color: #fff;
            border-bottom: 1px solid #e2e8f0;
            padding: 1.25rem 2rem;
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 99;
        }

        /* Premium Card Design */
        .card-custom {
            background-color: #fff;
            border: none;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            transition: all 0.3s cubic-bezier(0.165, 0.84, 0.44, 1);
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.08);
        }

        .card-header-custom {
            background-color: transparent;
            border-bottom: 1px solid #f1f5f9;
            padding: 1.25rem 1.5rem;
            font-weight: 700;
            font-size: 1.1rem;
        }

        /* Badge Customization */
        .badge-pill-custom {
            padding: 0.35em 0.8em;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.8rem;
        }

        /* Button Customization */
        .btn-accent {
            background: linear-gradient(135deg, var(--accent-color), #ff763b);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1.25rem;
            font-weight: 600;
            box-shadow: 0 4px 10px rgba(238, 77, 45, 0.2);
            transition: all 0.2s ease;
        }

        .btn-accent:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(238, 77, 45, 0.3);
            color: white;
        }

        /* Safety Stock Pulse */
        .alert-pulse {
            animation: pulse-danger 2s infinite;
        }

        @keyframes pulse-danger {
            0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); }
            100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
        }

        /* Responsive */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .sidebar-toggle {
                display: block !important;
            }
        }

        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(2px);
            z-index: 1040;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        .sidebar-backdrop.active {
            opacity: 1;
            visibility: visible;
        }

        .sidebar-toggle {
            display: none;
        }
    </style>
    @yield('styles')
</head>
<body>

    @php
        $user = Auth::user();
        // Dynamic safety stock check for layout alerts (only shown to Owner and Gudang)
        $safetyStockCount = 0;
        $safetyStockItems = [];
        if ($user && ($user->isOwner() || $user->isGudang())) {
            $safetyStockItems = \App\Models\RawMaterial::whereColumn('stock', '<=', 'safety_stock')->get();
            $safetyStockCount = $safetyStockItems->count();
        }
        
        $pendingEditsCount = 0;
        if ($user && $user->isOwner()) {
            $pendingEditsCount = \App\Models\EditRequest::where('status', 'pending')->count();
        }

        $notifications = [];
        $notificationsCount = 0;
        if ($user) {
            $notifications = \App\Models\Notification::where('user_id', $user->id)
                ->where('is_read', false)
                ->orderBy('created_at', 'desc')
                ->get();
            $notificationsCount = $notifications->count();
        }
    @endphp

    <!-- Sidebar Backdrop for Mobile -->
    <div class="sidebar-backdrop d-lg-none" id="sidebar-backdrop" onclick="toggleSidebar()"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <div>
            <div class="sidebar-brand d-flex justify-content-between align-items-center">
                <h5>Masyul <span>Kebab</span></h5>
                <button class="btn p-0 text-white border-0 d-lg-none" onclick="toggleSidebar()" aria-label="Close Sidebar" style="font-size: 1.5rem; line-height: 1;">
                    <i class="bi bi-arrow-left"></i>
                </button>
            </div>

            <div class="nav flex-column">
                <a href="{{ route('home') }}" class="nav-link-custom {{ Request::is('dashboard*') || Request::is('/') ? 'active' : '' }}">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>

                @if($user && ($user->isOwner() || $user->isAdmin()))
                    <div class="sidebar-section-title">Kemitraan & Cabang</div>
                    <a href="{{ route('partners.index') }}" class="nav-link-custom {{ Request::is('partners*') ? 'active' : '' }}">
                        <i class="bi bi-people-fill"></i> Data Mitra
                    </a>
                    <a href="{{ route('branches.index') }}" class="nav-link-custom {{ Request::is('branches*') ? 'active' : '' }}">
                        <i class="bi bi-shop-window"></i> Data Cabang
                    </a>
                @endif

                <div class="sidebar-section-title">Operasional</div>

                @if($user && ($user->isOwner() || $user->isGudang() || $user->isAdmin()))
                    <a href="{{ route('raw-materials.index') }}" class="nav-link-custom {{ Request::is('raw-materials*') ? 'active' : '' }}">
                        <i class="bi bi-box-seam-fill"></i> Stok Bahan Baku
                    </a>
                @endif

                @if($user && ($user->isOwner() || $user->isGudang()))
                    <a href="{{ route('incoming-stocks.index') }}" class="nav-link-custom {{ Request::is('incoming-stocks*') ? 'active' : '' }}">
                        <i class="bi bi-arrow-down-left-square-fill"></i> Stok Masuk
                    </a>
                @endif

                <a href="{{ route('orders.index') }}" class="nav-link-custom {{ Request::is('orders*') ? 'active' : '' }}">
                    <i class="bi bi-receipt-cutoff"></i> Pesanan Mitra
                </a>

                @if($user && ($user->isOwner() || $user->isAdmin()))
                    <a href="{{ route('branch-reports.index') }}" class="nav-link-custom {{ Request::is('branch-reports*') ? 'active' : '' }}">
                        <i class="bi bi-cash-stack"></i> Pendapatan Penjualan
                    </a>
                @endif

                @if($user && ($user->isOwner() || $user->isAdmin() || $user->isGudang()))
                    <div class="sidebar-section-title">Persetujuan</div>
                    <a href="{{ route('edit-requests.index') }}" class="nav-link-custom {{ Request::is('edit-requests*') ? 'active' : '' }}">
                        <i class="bi bi-shield-check-fill"></i> Edit Approval
                        @if($user->isOwner() && $pendingEditsCount > 0)
                            <span class="badge bg-danger ms-auto">{{ $pendingEditsCount }}</span>
                        @endif
                    </a>
                @endif
            </div>
        </div>

        <div class="mt-auto pt-3 border-top border-secondary border-opacity-10">
            <form action="{{ route('logout') }}" method="POST" id="sidebar-logout-form">
                @csrf
                <button type="submit" class="nav-link-custom w-100 border-0 bg-transparent text-start text-danger py-2" style="font-weight: 600;">
                    <i class="bi bi-box-arrow-right me-2 text-danger"></i> Keluar / Logout
                </button>
            </form>
        </div>
    </div>

    <!-- Main Content Area -->
    <div class="main-content">
        <!-- Top Navbar -->
        <div class="top-navbar">
            <div class="d-flex align-items-center">
                <button class="btn btn-light sidebar-toggle me-2" onclick="toggleSidebar()">
                    <i class="bi bi-list"></i>
                </button>
                @if(!Request::is('/') && !Request::is('dashboard*'))
                    <a href="javascript:history.back()" class="btn btn-light rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; border: 1px solid #e2e8f0;" title="Kembali">
                        <i class="bi bi-arrow-left fs-5"></i>
                    </a>
                @endif
                <h4 class="m-0 font-weight-700">@yield('page_title', 'Halaman Utama')</h4>
            </div>

            <div class="d-flex align-items-center gap-3">
                <!-- Notifications Dropdown -->
                @if($user)
                    @php
                        $totalBadgeCount = $notificationsCount + $safetyStockCount;
                    @endphp
                    <div class="dropdown">
                        <button class="btn btn-light position-relative rounded-circle {{ $totalBadgeCount > 0 ? 'alert-pulse border-danger text-danger' : '' }}" type="button" id="notificationDropdownButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-bell-fill"></i>
                            <span id="notification-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $totalBadgeCount > 0 ? '' : 'd-none' }}" style="font-size: 0.65rem;">
                                {{ $totalBadgeCount }}
                            </span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end p-3 shadow-lg border-0" id="notification-dropdown-menu" aria-labelledby="notificationDropdownButton" style="width: 340px; border-radius: 12px;">
                            @include('layouts._notification_items')
                        </div>
                    </div>
                @endif

                <!-- User Profile & Sign Out -->
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2" type="button" id="userMenuButton" data-bs-toggle="dropdown" aria-expanded="false" style="border-radius: 50px; padding: 0.4rem 1.2rem;">
                        <div class="bg-dark text-white rounded-circle d-flex align-items-center justify-content-center font-weight-700" style="width: 28px; height: 28px; font-size: 0.85rem;">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                        <span class="d-none d-md-inline" style="font-size: 0.85rem; font-weight: 600;">{{ $user->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-2" aria-labelledby="userMenuButton" style="border-radius: 12px;">
                        <li class="px-3 py-2 border-bottom">
                            <span class="text-muted d-block" style="font-size: 0.75rem;">Role Akses</span>
                            <strong class="text-uppercase text-danger" style="font-size: 0.85rem;">
                                @if($user->isOwner())
                                    Owner
                                @elseif($user->isGudang())
                                    Tim Gudang / SPV
                                @else
                                    Admin Utama
                                @endif
                            </strong>
                        </li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST" id="logout-form">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger d-flex align-items-center gap-2 py-2">
                                    <i class="bi bi-box-arrow-right"></i> Keluar / Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Page Content Wrapper -->
        <div class="container-fluid px-4">
            <!-- Alert Notification System -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px;">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px;">
                    <i class="bi bi-exclamation-octagon-fill me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert" style="border-radius: 12px;">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i><strong>Kesalahan Pengisian:</strong>
                    <ul class="mb-0 mt-1" style="font-size: 0.9rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Main Yield -->
            @yield('content')
        </div>
    </div>

    <!-- Toast Container for Real-time Notifications -->
    <div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
        <!-- Toasts will be appended here dynamically -->
    </div>

    <!-- Bootstrap 5 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('active');
            const backdrop = document.getElementById('sidebar-backdrop');
            if (backdrop) {
                backdrop.classList.toggle('active');
            }
        }

        window.addEventListener('resize', function() {
            if (window.innerWidth > 991.98) {
                const sidebar = document.getElementById('sidebar');
                const backdrop = document.getElementById('sidebar-backdrop');
                if (sidebar) sidebar.classList.remove('active');
                if (backdrop) backdrop.classList.remove('active');
            }
        });

        @if(Auth::check())
        // Keep track of notification IDs that we have seen/loaded
        let seenNotificationIds = new Set();
        let isFirstPoll = true;

        function showNotificationToast(notification) {
            const toastContainer = document.querySelector('.toast-container');
            if (!toastContainer) return;

            const toastId = 'toast-' + notification.id;
            const toastHtml = `
                <div id="${toastId}" class="toast align-items-center text-dark bg-white border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" style="border-radius: 12px; border-left: 4px solid var(--accent-color) !important;">
                    <div class="d-flex">
                        <div class="toast-body p-3">
                            <div class="d-flex align-items-start gap-2">
                                <div class="bg-danger-subtle text-danger rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; flex-shrink: 0;">
                                    <i class="bi bi-bell-fill"></i>
                                </div>
                                <div>
                                    <strong class="d-block text-dark" style="font-size: 0.9rem;">${notification.title}</strong>
                                    <span class="text-muted d-block mt-1" style="font-size: 0.8rem; line-height: 1.3;">${notification.message}</span>
                                    ${notification.link ? `<a href="${notification.link}" class="btn btn-sm btn-accent mt-2 py-1 px-3" style="font-size: 0.75rem;">Lihat Detail</a>` : ''}
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;
            
            toastContainer.insertAdjacentHTML('beforeend', toastHtml);
            const toastEl = document.getElementById(toastId);
            const bsToast = new bootstrap.Toast(toastEl, { delay: 10000 });
            bsToast.show();

            // Remove element from DOM after it's hidden
            toastEl.addEventListener('hidden.bs.toast', function () {
                toastEl.remove();
            });
        }

        function pollNotifications(forceUpdateDropdown = false) {
            fetch("{{ route('notifications.poll') }}")
                .then(response => response.json())
                .then(data => {
                    // Update badge
                    const badge = document.getElementById('notification-badge');
                    const button = document.getElementById('notificationDropdownButton');
                    if (data.totalBadgeCount > 0) {
                        badge.innerText = data.totalBadgeCount;
                        badge.classList.remove('d-none');
                        button.classList.add('alert-pulse', 'border-danger', 'text-danger');
                    } else {
                        badge.classList.add('d-none');
                        button.classList.remove('alert-pulse', 'border-danger', 'text-danger');
                    }
                    
                    // Update dropdown menu items if it's not currently open or if forced
                    const dropdownMenu = document.getElementById('notification-dropdown-menu');
                    if (dropdownMenu && (forceUpdateDropdown || !dropdownMenu.classList.contains('show'))) {
                        dropdownMenu.innerHTML = data.html;
                    }

                    // Check for new notifications to toast
                    if (data.notifications && data.notifications.length > 0) {
                        data.notifications.forEach(notif => {
                            if (!seenNotificationIds.has(notif.id)) {
                                seenNotificationIds.add(notif.id);
                                // Don't show toast on the very first page load/poll
                                if (!isFirstPoll) {
                                    showNotificationToast(notif);
                                }
                            }
                        });
                    }
                    isFirstPoll = false;
                })
                .catch(err => console.error("Error polling notifications:", err));
        }

        // Run poll on load and every 5 seconds for snappy updates
        pollNotifications();
        setInterval(function() {
            pollNotifications(false);
        }, 5000);

        // Also refresh notifications immediately when dropdown is opening so user always sees fresh items
        const dropdownButton = document.getElementById('notificationDropdownButton');
        if (dropdownButton) {
            dropdownButton.addEventListener('show.bs.dropdown', function () {
                pollNotifications(true);
            });
        }
        @endif
    </script>
    @yield('scripts')
</body>
</html>
