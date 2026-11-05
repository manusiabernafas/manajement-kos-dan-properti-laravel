<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\BuildingController;
use App\Http\Controllers\CheckInController;
use App\Http\Controllers\CheckOutController;
use App\Http\Controllers\ContractController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AuditLogController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DepositController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MaintenanceController;
use App\Http\Controllers\MeterController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PenaltyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\FloorController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomTypeController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\Tenant\TenantAnnouncementController;
use App\Http\Controllers\Tenant\TenantComplaintController;
use App\Http\Controllers\Tenant\TenantDashboardController;
use App\Http\Controllers\Tenant\TenantInvoiceController;
use App\Http\Controllers\Tenant\TenantMaintenanceController;
use App\Http\Controllers\Tenant\TenantPaymentController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\TenantDocumentController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login');

Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('profile/password', [ProfileController::class, 'editPassword'])->name('profile.password');
    Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::post('profile/theme', [ProfileController::class, 'updateTheme'])->name('profile.theme');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('search', [SearchController::class, 'index'])->name('search');

    Route::get('notifications/{id}', [NotificationController::class, 'show'])->name('notifications.show');
    Route::post('notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');

    Route::resource('properties', PropertyController::class);
    Route::resource('buildings', BuildingController::class);
    Route::resource('floors', FloorController::class);
    Route::resource('room-types', RoomTypeController::class);
    Route::resource('facilities', FacilityController::class)->except(['create', 'edit', 'show']);
    Route::resource('rooms', RoomController::class);

    Route::resource('tenants', TenantController::class);
    Route::post('tenants/{tenant}/documents', [TenantDocumentController::class, 'store'])->name('tenants.documents.store');
    Route::delete('tenants/{tenant}/documents/{document}', [TenantDocumentController::class, 'destroy'])->name('tenants.documents.destroy');

    Route::resource('bookings', BookingController::class);
    Route::post('bookings/{booking}/confirm', [BookingController::class, 'confirm'])->name('bookings.confirm');
    Route::post('bookings/{booking}/cancel', [BookingController::class, 'cancel'])->name('bookings.cancel');

    Route::resource('check-ins', CheckInController::class)->only(['index', 'create', 'store', 'show']);
    Route::resource('check-outs', CheckOutController::class)->only(['index', 'create', 'store', 'show']);

    Route::resource('contracts', ContractController::class)->only(['index', 'show', 'edit', 'update']);
    Route::get('contracts/{contract}/print', [ContractController::class, 'print'])->name('contracts.print');

    Route::resource('invoices', InvoiceController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('invoices/{invoice}/cancel', [InvoiceController::class, 'cancel'])->name('invoices.cancel');
    Route::get('invoices/{invoice}/print', [InvoiceController::class, 'print'])->name('invoices.print');

    Route::resource('payments', PaymentController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
    Route::post('payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');

    Route::resource('deposits', DepositController::class)->only(['index', 'show', 'update']);

    Route::get('penalties', [PenaltyController::class, 'index'])->name('penalties.index');
    Route::post('penalty-configs', [PenaltyController::class, 'storeConfig'])->name('penalty-configs.store');
    Route::put('penalty-configs/{config}', [PenaltyController::class, 'updateConfig'])->name('penalty-configs.update');
    Route::delete('penalty-configs/{config}', [PenaltyController::class, 'destroyConfig'])->name('penalty-configs.destroy');

    Route::resource('meters', MeterController::class)->only(['index', 'store', 'show', 'destroy']);
    Route::post('meters/{meter}/readings', [MeterController::class, 'storeReading'])->name('meters.readings.store');

    Route::resource('expenses', ExpenseController::class);

    Route::resource('maintenance', MaintenanceController::class)->only(['index', 'create', 'store', 'show', 'update']);
    Route::post('maintenance/{maintenance}/comment', [MaintenanceController::class, 'comment'])->name('maintenance.comment');

    Route::resource('complaints', ComplaintController::class)->only(['index', 'create', 'store', 'show', 'update']);
    Route::post('complaints/{complaint}/comment', [ComplaintController::class, 'comment'])->name('complaints.comment');

    Route::resource('inventories', InventoryController::class);

    Route::resource('announcements', AnnouncementController::class);

    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/export/{format}', [ReportController::class, 'export'])->name('reports.export');

    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    Route::resource('users', UserController::class)->except(['show']);
    Route::resource('roles', RoleController::class)->except(['show']);

    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
});

Route::middleware(['auth', 'role:Tenant'])->prefix('portal')->name('tenant.')->group(function () {
    Route::get('dashboard', [TenantDashboardController::class, 'index'])->name('dashboard');
    Route::get('room', [TenantDashboardController::class, 'room'])->name('room');
    Route::get('contract', [TenantDashboardController::class, 'contract'])->name('contract');

    Route::resource('invoices', TenantInvoiceController::class)->only(['index', 'show']);

    Route::resource('payments', TenantPaymentController::class)->only(['index', 'create', 'store', 'show']);

    Route::resource('maintenance', TenantMaintenanceController::class)->only(['index', 'create', 'store', 'show']);

    Route::resource('complaints', TenantComplaintController::class)->only(['index', 'create', 'store', 'show']);

    Route::get('announcements', [TenantAnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('announcements/{announcement}', [TenantAnnouncementController::class, 'show'])->name('announcements.show');
});
