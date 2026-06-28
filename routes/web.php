<?php

use Illuminate\Support\Facades\Route;

// Temporary Migration & Seeder Route (will be deleted after running)
Route::get('/run-migration-temp-abc123xyz', function () {
    try {
        \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
        $migrateOutput = \Illuminate\Support\Facades\Artisan::output();
        
        \Illuminate\Support\Facades\Artisan::call('db:seed', ['--force' => true]);
        $seedOutput = \Illuminate\Support\Facades\Artisan::output();
        
        return 'Migrasi & Seeding sukses!<br><br><b>Output Migrasi:</b><pre>' . $migrateOutput . '</pre><br><b>Output Seeding:</b><pre>' . $seedOutput . '</pre>';
    } catch (\Exception $e) {
        return 'Gagal menjalankan migrasi/seeding: ' . $e->getMessage();
    }
});

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Admin\PartnerController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\PartnerOrderController;
use App\Http\Controllers\Admin\BranchReportController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Gudang\RawMaterialController;
use App\Http\Controllers\Gudang\IncomingStockController;
use App\Http\Controllers\Gudang\DashboardController as GudangDashboardController;
use App\Http\Controllers\Owner\EditRequestController;
use App\Http\Controllers\Owner\DashboardController as OwnerDashboardController;

// 1. Guest Routes (Login)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// 2. Authenticated Routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Home Redirects to Dashboard based on Role
    Route::get('/', [DashboardController::class, 'index'])->name('home');

    // Role-specific Dashboards
    Route::get('/dashboard/owner', [OwnerDashboardController::class, 'owner'])
        ->name('dashboard.owner')
        ->middleware('role:owner');

    Route::get('/dashboard/admin', [AdminDashboardController::class, 'admin'])
        ->name('dashboard.admin')
        ->middleware('role:admin');

    Route::get('/dashboard/gudang', [GudangDashboardController::class, 'gudang'])
        ->name('dashboard.gudang')
        ->middleware('role:gudang');

    // Partners & Branches CRUD (Owner & Admin)
    Route::resource('partners', PartnerController::class);
    Route::resource('branches', BranchController::class);

    // Raw Materials CRUD (Owner & Gudang/Supervisor)
    Route::resource('raw-materials', RawMaterialController::class)->names('raw-materials');

    // Incoming Stocks (Owner & Gudang/Supervisor)
    Route::get('/incoming-stocks', [IncomingStockController::class, 'index'])->name('incoming-stocks.index');
    Route::get('/incoming-stocks/create', [IncomingStockController::class, 'create'])->name('incoming-stocks.create');
    Route::post('/incoming-stocks', [IncomingStockController::class, 'store'])->name('incoming-stocks.store');
    Route::get('/incoming-stocks/{incomingStock}/edit', [IncomingStockController::class, 'edit'])->name('incoming-stocks.edit');
    Route::put('/incoming-stocks/{incomingStock}', [IncomingStockController::class, 'update'])->name('incoming-stocks.update');

    // Partner Orders (All authenticated roles can index & show, with different views)
    Route::get('/orders', [PartnerOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/create', [PartnerOrderController::class, 'create'])->name('orders.create');
    Route::post('/orders', [PartnerOrderController::class, 'store'])->name('orders.store');
    Route::get('/orders/{order}', [PartnerOrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/edit', [PartnerOrderController::class, 'edit'])->name('orders.edit');
    Route::put('/orders/{order}', [PartnerOrderController::class, 'update'])->name('orders.update');
    Route::delete('/orders/{order}', [PartnerOrderController::class, 'destroy'])->name('orders.destroy');
    
    // Status update (Gudang/Owner only)
    Route::patch('/orders/{order}/status', [PartnerOrderController::class, 'updateStatus'])
        ->name('orders.update-status');

    // Update payment status directly (Owner & Admin)
    Route::post('/orders/{order}/payment', [PartnerOrderController::class, 'updatePayment'])
        ->name('orders.payment')
        ->middleware('role:owner,admin');

    // Branch Reports CRUD (Owner & Admin)
    Route::resource('branch-reports', BranchReportController::class)->names('branch-reports');

    // Edit Requests (Owner & Admin)
    Route::get('/edit-requests', [EditRequestController::class, 'index'])->name('edit-requests.index');
    Route::get('/edit-requests/{editRequest}', [EditRequestController::class, 'show'])->name('edit-requests.show');
    Route::post('/edit-requests/{editRequest}/approve', [EditRequestController::class, 'approve'])->name('edit-requests.approve');
    Route::post('/edit-requests/{editRequest}/reject', [EditRequestController::class, 'reject'])->name('edit-requests.reject');

    // Export Routes
    Route::get('/export/omset', [OwnerDashboardController::class, 'exportOmset'])->name('export.omset')->middleware('role:owner');
    Route::get('/export/pembelian', [OwnerDashboardController::class, 'exportPembelian'])->name('export.pembelian')->middleware('role:owner,admin');
    Route::get('/export/stok', [OwnerDashboardController::class, 'exportStok'])->name('export.stok')->middleware('role:owner');
    Route::get('/export/mitra', [OwnerDashboardController::class, 'exportMitra'])->name('export.mitra')->middleware('role:owner,admin');
    Route::get('/export/{type}/{format}', [\App\Http\Controllers\ExportController::class, 'export'])->name('generic.export');

    // Notifications
    Route::get('/notifications/poll', [\App\Http\Controllers\NotificationController::class, 'pollNotifications'])->name('notifications.poll');
    Route::get('/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'readAndRedirect'])->name('notifications.read-redirect');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
});
