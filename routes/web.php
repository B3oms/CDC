<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CalamityController;
use App\Http\Controllers\Admin\ReliefMonitorController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\BeneficiaryController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboardController;
use App\Http\Controllers\Staff\BeneficiaryController as StaffBeneficiaryController;
use App\Http\Controllers\Staff\RecommendedController as StaffRecommendedController;
use App\Http\Controllers\Staff\LocationController;
use App\Http\Controllers\Staff\InventoryController as StaffInventoryController;
use App\Http\Controllers\Barangay\EvacuationController;
use App\Http\Controllers\Barangay\RecommendationController;
use App\Http\Controllers\Admin\LocationRequestController as AdminLocationRequestController;
use App\Http\Controllers\Admin\LocationManagementController;
use App\Http\Controllers\Admin\EventController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Staff\LocationRequestController as StaffLocationRequestController;

// ──────────────────────────────────────────────────────────
// Auth Routes
// ──────────────────────────────────────────────────────────
Route::get('/',        [LoginController::class, 'showLogin'])->name('login');
Route::post('/login',  [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/admin/beneficiaries/pdf', [BeneficiaryController::class, 'downloadPDF'])
    ->name('admin.beneficiaries.pdf');
// ──────────────────────────────────────────────────────────
// Admin Only
// ──────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['isAdmin'])->group(function () {

    // Location Requests Management
    Route::get('location-requests', [AdminLocationRequestController::class, 'index'])->name('location-requests.index');
    Route::get('location-requests/{id}', [AdminLocationRequestController::class, 'show'])->name('location-requests.show');
    Route::get('location-requests/{id}/edit', [AdminLocationRequestController::class, 'edit'])->name('location-requests.edit');
    Route::put('location-requests/{id}', [AdminLocationRequestController::class, 'update'])->name('location-requests.update');
    Route::post('location-requests/{id}/approve', [AdminLocationRequestController::class, 'approve'])->name('location-requests.approve');
    Route::post('location-requests/{id}/reject', [AdminLocationRequestController::class, 'reject'])->name('location-requests.reject');
    Route::delete('location-requests/{id}', [AdminLocationRequestController::class, 'destroy'])->name('location-requests.destroy');

    // Admin Dashboard
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
    Route::get('dashboard/notifications', [DashboardController::class, 'getNotifications'])->name('dashboard.notifications');
    Route::post('dashboard/notifications/{notificationId}/read', [DashboardController::class, 'markNotificationRead'])->name('dashboard.notifications.read');
    Route::post('dashboard/notifications/read-all', [DashboardController::class, 'markAllNotificationsRead'])->name('dashboard.notifications.readAll');
    
    // Profile
    Route::get('profile', [DashboardController::class, 'profile'])->name('profile');
    Route::put('profile', [DashboardController::class, 'updateProfile'])->name('profile.update');
    Route::put('password', [DashboardController::class, 'updatePassword'])->name('password.update');

    
    // Location Management
    Route::get('locations', [LocationManagementController::class, 'index'])->name('locations.index');
    Route::get('locations/create', [LocationManagementController::class, 'create'])->name('locations.create');
    Route::post('locations', [LocationManagementController::class, 'store'])->name('locations.store');
    Route::get('locations/{id}', [LocationManagementController::class, 'show'])->name('locations.show');
    Route::get('locations/{id}/edit', [LocationManagementController::class, 'edit'])->name('locations.edit');
    Route::put('locations/{id}', [LocationManagementController::class, 'update'])->name('locations.update');
    Route::post('locations/{id}/approve', [LocationManagementController::class, 'approve'])->name('locations.approve');
    Route::post('locations/{id}/reject', [LocationManagementController::class, 'reject'])->name('locations.reject');
    Route::post('locations/{id}/restore', [LocationManagementController::class, 'restore'])->name('locations.restore');
    Route::delete('locations/{id}', [LocationManagementController::class, 'destroy'])->name('locations.destroy');

    // Events Management
    Route::get('events', [EventController::class, 'index'])->name('events.index');
    Route::get('events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('events', [EventController::class, 'store'])->name('events.store');

    // AJAX Endpoints for Dynamic Data
    Route::get('barangays/by-municipality/{id}', [EventController::class, 'getBarangays'])->name('barangays.by-municipality');
    Route::post('beneficiaries/count', [EventController::class, 'getBeneficiaryCounts'])->name('beneficiaries.count');

    // Beneficiaries
    Route::get('beneficiaries',      [BeneficiaryController::class, 'index'])->name('beneficiaries.index');
    Route::get('beneficiaries/create', [BeneficiaryController::class, 'create'])->name('beneficiaries.create');
    Route::post('beneficiaries',     [BeneficiaryController::class, 'store'])->name('beneficiaries.store');
    Route::get('beneficiaries/{id}', [BeneficiaryController::class, 'show'])->name('beneficiaries.show');

    // Staff management
    Route::get('staff',                      [StaffController::class, 'index'])->name('staff.index');
    Route::get('staff/create',               [StaffController::class, 'create'])->name('staff.create');
    Route::post('staff',                     [StaffController::class, 'store'])->name('staff.store');
    Route::get('staff/{id}/edit',            [StaffController::class, 'edit'])->name('staff.edit');
    Route::put('staff/{id}',                 [StaffController::class, 'update'])->name('staff.update');
    Route::delete('staff/{id}',              [StaffController::class, 'destroy'])->name('staff.destroy');
    Route::post('staff/{id}/reset-password', [StaffController::class, 'resetPassword'])->name('staff.resetPassword');
});

// ──────────────────────────────────────────────────────────
// Shared: Admin + Staff
// ──────────────────────────────────────────────────────────
Route::middleware(['isAdminOrStaff'])->group(function () {

    
    // Calamity
    Route::get('admin/calamity',            [CalamityController::class, 'index'])->name('admin.calamity.index');
    Route::get('admin/calamity/create',      [CalamityController::class, 'create'])->name('admin.calamity.create');
    Route::post('admin/calamity',            [CalamityController::class, 'store'])->name('admin.calamity.store');
    Route::get('admin/calamity/{id}',        [CalamityController::class, 'show'])->name('admin.calamity.show');
    
    Route::post('admin/calamity/{id}/close', [CalamityController::class, 'close'])->name('admin.calamity.close');
    Route::get('admin/calamity/{id}/report', [CalamityController::class, 'report'])->name('admin.calamity.report');

    // Inventory
    Route::get('admin/inventory',                                  [InventoryController::class, 'index'])->name('admin.inventory.index');
    Route::get('admin/inventory/category/create',                  [InventoryController::class, 'createCategory'])->name('admin.inventory.category.create');
    Route::post('admin/inventory/category',                        [InventoryController::class, 'storeCategory'])->name('admin.inventory.category.store');
    Route::get('admin/inventory/category/{id}',                    [InventoryController::class, 'showCategory'])->name('admin.inventory.category.show');
    Route::get('admin/inventory/category/{id}/edit',               [InventoryController::class, 'editCategory'])->name('admin.inventory.category.edit');
    Route::put('admin/inventory/category/{id}',                    [InventoryController::class, 'updateCategory'])->name('admin.inventory.category.update');
    Route::delete('admin/inventory/category/{id}',                 [InventoryController::class, 'destroyCategory'])->name('admin.inventory.category.destroy');
    Route::get('admin/inventory/subcategory/create/{categoryId}',  [InventoryController::class, 'createSubcategory'])->name('admin.inventory.subcategory.create');
    Route::post('admin/inventory/subcategory/{categoryId}',        [InventoryController::class, 'storeSubcategory'])->name('admin.inventory.subcategory.store');
    Route::get('admin/inventory/subcategory/{id}',                 [InventoryController::class, 'showSubcategory'])->name('admin.inventory.subcategory');
    Route::get('admin/inventory/subcategory/{id}/edit',            [InventoryController::class, 'editSubcategory'])->name('admin.inventory.subcategory.edit');
    Route::put('admin/inventory/subcategory/{id}',                 [InventoryController::class, 'updateSubcategory'])->name('admin.inventory.subcategory.update');
    Route::delete('admin/inventory/subcategory/{id}',              [InventoryController::class, 'destroySubcategory'])->name('admin.inventory.subcategory.destroy');
    Route::get('admin/inventory/item/create/{subcategoryId}',      [InventoryController::class, 'createItem'])->name('admin.inventory.item.create');
    Route::post('admin/inventory/item/{subcategoryId}',            [InventoryController::class, 'storeItem'])->name('admin.inventory.item.store');
    Route::get('admin/inventory/item/{id}/edit',                   [InventoryController::class, 'editItem'])->name('admin.inventory.item.edit');
    Route::put('admin/inventory/item/{id}',                        [InventoryController::class, 'updateItem'])->name('admin.inventory.item.update');
    Route::delete('admin/inventory/item/{id}',                     [InventoryController::class, 'destroyItem'])->name('admin.inventory.item.destroy');

    // Relief Monitor
    Route::get('admin/relief',               [ReliefMonitorController::class, 'index'])->name('admin.relief.index');
    Route::get('admin/relief/create',        [ReliefMonitorController::class, 'create'])->name('admin.relief.create');
    Route::post('admin/relief',              [ReliefMonitorController::class, 'store'])->name('admin.relief.store');
    Route::get('admin/relief/{id}',          [ReliefMonitorController::class, 'show'])->name('admin.relief.show');
    Route::post('admin/relief/{id}/done',    [ReliefMonitorController::class, 'markDone'])->name('admin.relief.done');
    Route::post('admin/relief/{id}/ongoing', [ReliefMonitorController::class, 'markOngoing'])->name('admin.relief.ongoing');
    Route::post('admin/relief/{id}/status',  [ReliefMonitorController::class, 'updateStatus'])->name('admin.relief.status');
    Route::delete('admin/relief/{id}',       [ReliefMonitorController::class, 'destroy'])->name('admin.relief.destroy');
    Route::get('admin/relief/stats',         [ReliefMonitorController::class, 'getStats'])->name('admin.relief.stats');
    Route::get('admin/relief/report/pdf',   [ReliefMonitorController::class, 'downloadReport'])->name('admin.relief.report.pdf');
    Route::get('admin/relief/{id}/pdf',     [ReliefMonitorController::class, 'downloadEventReport'])->name('admin.relief.event.pdf');

    // Notifications
    Route::get('admin/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('admin/notifications/{notificationId}/read', [NotificationController::class, 'markAsRead'])->name('admin.notifications.read');
    Route::post('admin/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('admin.notifications.readAll');
});

// ──────────────────────────────────────────────────────────
// Staff Only
// ──────────────────────────────────────────────────────────
Route::prefix('staff')->name('staff.')->middleware(['isStaff'])->group(function () {

    // Dashboard
    Route::get('dashboard', [StaffDashboardController::class, 'index'])->name('dashboard');
    Route::get('dashboard/notifications', [StaffDashboardController::class, 'getNotifications'])->name('dashboard.notifications');
    Route::post('dashboard/notifications/{notificationId}/read', [StaffDashboardController::class, 'markNotificationRead'])->name('dashboard.notifications.read');
    Route::post('dashboard/notifications/read-all', [StaffDashboardController::class, 'markAllNotificationsRead'])->name('dashboard.notifications.readAll');

    // Beneficiaries
    Route::get('beneficiaries',           [StaffBeneficiaryController::class, 'index'])->name('beneficiaries.index');
    Route::get('beneficiaries/create',    [StaffBeneficiaryController::class, 'create'])->name('beneficiaries.create');
    Route::post('beneficiaries',          [StaffBeneficiaryController::class, 'store'])->name('beneficiaries.store');
    Route::get('beneficiaries/{id}',      [StaffBeneficiaryController::class, 'show'])->name('beneficiaries.show');
    Route::get('beneficiaries/pdf',       [StaffBeneficiaryController::class, 'pdf'])->name('beneficiaries.pdf');

    Route::get('locations', [\App\Http\Controllers\Staff\LocationController::class, 'index'])->name('staff.locations.index');
    Route::get('locations/create', [\App\Http\Controllers\Staff\LocationController::class, 'create'])->name('staff.locations.create');
    Route::post('locations/store-municipality', [\App\Http\Controllers\Staff\LocationController::class, 'storeMunicipality'])->name('staff.locations.store-municipality');
    Route::post('locations/store-barangay', [\App\Http\Controllers\Staff\LocationController::class, 'storeBarangay'])->name('staff.locations.store-barangay');
    Route::get('locations/edit-municipality/{id}', [\App\Http\Controllers\Staff\LocationController::class, 'updateMunicipality'])->name('staff.locations.edit-municipality');
    Route::get('locations/edit-barangay/{id}', [\App\Http\Controllers\Staff\LocationController::class, 'updateBarangay'])->name('staff.locations.edit-barangay');
    Route::put('locations/update-municipality/{id}', [\App\Http\Controllers\Staff\LocationController::class, 'updateMunicipality'])->name('staff.locations.update-municipality');
    Route::put('locations/update-barangay/{id}', [\App\Http\Controllers\Staff\LocationController::class, 'updateBarangay'])->name('staff.locations.update-barangay');
    Route::delete('locations/destroy-municipality/{id}', [\App\Http\Controllers\Staff\LocationController::class, 'destroyMunicipality'])->name('staff.locations.destroy-municipality');
    Route::delete('locations/destroy-barangay/{id}', [\App\Http\Controllers\Staff\LocationController::class, 'destroyBarangay'])->name('staff.locations.destroy-barangay');

    // Location Requests
    Route::get('location-requests', [StaffLocationRequestController::class, 'index'])->name('location-requests.index');
    Route::get('location-requests/create', [StaffLocationRequestController::class, 'create'])->name('location-requests.create');
    Route::post('location-requests', [StaffLocationRequestController::class, 'store'])->name('location-requests.store');
    Route::get('location-requests/{id}/edit', [StaffLocationRequestController::class, 'edit'])->name('location-requests.edit');
    Route::put('location-requests/{id}', [StaffLocationRequestController::class, 'update'])->name('location-requests.update');
    Route::delete('location-requests/{id}', [StaffLocationRequestController::class, 'destroy'])->name('location-requests.destroy');

    // Recommended Beneficiaries
    Route::get('recommended',              [StaffRecommendedController::class, 'index'])->name('recommended.index');
    Route::get('recommended/{id}/convert', [StaffRecommendedController::class, 'convert'])->name('recommended.convert');
    Route::post('recommended/{id}/reject', [StaffRecommendedController::class, 'reject'])->name('recommended.reject');

    // Relief Monitor (Staff View)
    Route::get('relief',                   [\App\Http\Controllers\Staff\ReliefController::class, 'index'])->name('relief.index');
    Route::get('relief/create',            [\App\Http\Controllers\Staff\ReliefController::class, 'create'])->name('relief.create');
    Route::post('relief',                  [\App\Http\Controllers\Staff\ReliefController::class, 'store'])->name('relief.store');
    Route::get('relief/{id}',              [\App\Http\Controllers\Staff\ReliefController::class, 'show'])->name('relief.show');
    Route::post('relief/{id}/status',    [\App\Http\Controllers\Staff\ReliefController::class, 'updateStatus'])->name('relief.updateStatus');
    Route::get('relief/stats',             [\App\Http\Controllers\Staff\ReliefController::class, 'stats'])->name('relief.stats');
    Route::delete('relief/{id}',           [\App\Http\Controllers\Staff\ReliefController::class, 'destroy'])->name('relief.destroy');
    
    // Inventory (Staff View)
    Route::get('inventory',               [\App\Http\Controllers\Staff\InventoryController::class, 'index'])->name('inventory.index');
    
    // Category CRUD - Specific routes first
    Route::get('inventory/category/create', [\App\Http\Controllers\Staff\InventoryController::class, 'createCategory'])->name('inventory.category.create');
    Route::post('inventory/category', [\App\Http\Controllers\Staff\InventoryController::class, 'storeCategory'])->name('inventory.category.store');
    Route::get('inventory/category/{id}/edit', [\App\Http\Controllers\Staff\InventoryController::class, 'editCategory'])->name('inventory.category.edit');
    Route::put('inventory/category/{id}', [\App\Http\Controllers\Staff\InventoryController::class, 'updateCategory'])->name('inventory.category.update');
    Route::delete('inventory/category/{id}', [\App\Http\Controllers\Staff\InventoryController::class, 'destroyCategory'])->name('inventory.category.destroy');
    
    // Category show route - parameterized routes last
    Route::get('inventory/category/{id}', [\App\Http\Controllers\Staff\InventoryController::class, 'showCategory'])->name('inventory.category.show');
    Route::get('inventory/subcategory/{id}', [\App\Http\Controllers\Staff\InventoryController::class, 'showSubcategory'])->name('inventory.subcategory.show');

    // Profile
    Route::get('profile', [\App\Http\Controllers\Staff\ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [\App\Http\Controllers\Staff\ProfileController::class, 'update'])->name('profile.update');
    Route::put('password', [\App\Http\Controllers\Staff\ProfileController::class, 'updatePassword'])->name('password.update');
    
    // Subcategory CRUD
    Route::get('inventory/subcategory/create/{categoryId}', [\App\Http\Controllers\Staff\InventoryController::class, 'createSubcategory'])->name('inventory.subcategory.create');
    Route::post('inventory/subcategory/{categoryId}', [\App\Http\Controllers\Staff\InventoryController::class, 'storeSubcategory'])->name('inventory.subcategory.store');
    Route::get('inventory/subcategory/{id}/edit', [\App\Http\Controllers\Staff\InventoryController::class, 'editSubcategory'])->name('inventory.subcategory.edit');
    Route::put('inventory/subcategory/{id}', [\App\Http\Controllers\Staff\InventoryController::class, 'updateSubcategory'])->name('inventory.subcategory.update');
    Route::delete('inventory/subcategory/{id}', [\App\Http\Controllers\Staff\InventoryController::class, 'destroySubcategory'])->name('inventory.subcategory.destroy');
    
    // Item CRUD
    Route::get('inventory/item/create/{subcategoryId}', [\App\Http\Controllers\Staff\InventoryController::class, 'createItem'])->name('inventory.item.create');
    Route::post('inventory/item/{subcategoryId}', [\App\Http\Controllers\Staff\InventoryController::class, 'storeItem'])->name('inventory.item.store');
    Route::get('inventory/item/{id}/edit', [\App\Http\Controllers\Staff\InventoryController::class, 'editItem'])->name('inventory.item.edit');
    Route::put('inventory/item/{id}', [\App\Http\Controllers\Staff\InventoryController::class, 'updateItem'])->name('inventory.item.update');
    Route::delete('inventory/item/{id}', [\App\Http\Controllers\Staff\InventoryController::class, 'destroyItem'])->name('inventory.item.destroy');

    // ── Location Management ────────────────────────────────
    // Municipalities
    Route::get('locations',                            [LocationController::class, 'index'])->name('locations.index');
    Route::post('locations/municipality',              [LocationController::class, 'storeMunicipality'])->name('locations.municipality.store');
    Route::put('locations/municipality/{id}',          [LocationController::class, 'updateMunicipality'])->name('locations.municipality.update');
    Route::delete('locations/municipality/{id}',       [LocationController::class, 'destroyMunicipality'])->name('locations.municipality.destroy');

    // Barangays
    Route::post('locations/barangay',                  [LocationController::class, 'storeBarangay'])->name('locations.barangay.store');
    Route::put('locations/barangay/{id}',              [LocationController::class, 'updateBarangay'])->name('locations.barangay.update');
    Route::delete('locations/barangay/{id}',           [LocationController::class, 'destroyBarangay'])->name('locations.barangay.destroy');

    // Approval actions (routes accessible here but methods
    // inside the controller restrict execution to Admin only)
    Route::post('locations/municipality/{id}/approve', [LocationController::class, 'approveMunicipality'])->name('locations.municipality.approve');
    Route::post('locations/municipality/{id}/reject',  [LocationController::class, 'rejectMunicipality'])->name('locations.municipality.reject');
    Route::post('locations/barangay/{id}/approve',     [LocationController::class, 'approveBarangay'])->name('locations.barangay.approve');
    Route::post('locations/barangay/{id}/reject',      [LocationController::class, 'rejectBarangay'])->name('locations.barangay.reject');
});

// ──────────────────────────────────────────────────────────
// Barangay Partner
// ──────────────────────────────────────────────────────────
Route::prefix('barangay')->name('barangay.')->middleware(['isBarangay'])->group(function () {
    Route::get('dashboard',        [EvacuationController::class, 'index'])->name('dashboard');
    Route::post('set-center',      [EvacuationController::class, 'setCenter'])->name('setCenter');
    Route::post('submit-report',   [EvacuationController::class, 'submitReport'])->name('submitReport');
    Route::post('feedback',        [EvacuationController::class, 'submitFeedback'])->name('feedback.submit');
    Route::get('recommendations',  [RecommendationController::class, 'index'])->name('recommendations.index');
    Route::post('recommendations', [RecommendationController::class, 'store'])->name('recommendations.store');
    Route::get('recommendations/{id}/edit', [RecommendationController::class, 'edit'])->name('recommendations.edit');
    Route::put('recommendations/{id}', [RecommendationController::class, 'update'])->name('recommendations.update');
    Route::delete('recommendations/{id}', [RecommendationController::class, 'destroy'])->name('recommendations.destroy');
    Route::get('profile', [\App\Http\Controllers\Staff\ProfileController::class, 'show'])->name('profile.show');
    Route::put('profile', [\App\Http\Controllers\Staff\ProfileController::class, 'update'])->name('profile.update');
    Route::put('password', [\App\Http\Controllers\Staff\ProfileController::class, 'updatePassword'])->name('password.update');
});