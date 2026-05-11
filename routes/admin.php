<?php

use App\Http\Controllers\Admin\LocationManagementController;
use Illuminate\Support\Facades\Route;

// Location Management Routes
Route::get('/locations', [LocationManagementController::class, 'index'])->name('admin.locations.index');
Route::get('/locations/data', [LocationManagementController::class, 'getRequestsData'])->name('admin.locations.data');
Route::post('/locations/approve-municipality/{id}', [LocationManagementController::class, 'approve'])->name('admin.locations.approve.municipality');
Route::post('/locations/approve-barangay/{id}', [LocationManagementController::class, 'approve'])->name('admin.locations.approve.barangay');
Route::post('/locations/reject-municipality/{id}', [LocationManagementController::class, 'reject'])->name('admin.locations.reject.municipality');
Route::post('/locations/reject-barangay/{id}', [LocationManagementController::class, 'reject'])->name('admin.locations.reject.barangay');
Route::get('/locations/municipality/{id}', [LocationManagementController::class, 'viewDetails'])->name('admin.locations.municipality.show');
Route::get('/locations/barangay/{id}', [LocationManagementController::class, 'viewDetails'])->name('admin.locations.barangay.show');
