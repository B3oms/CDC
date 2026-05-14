<?php

use App\Http\Controllers\Admin\LocationManagementController;
use Illuminate\Support\Facades\Route;

// Location Management Routes
Route::get('/locations', [LocationManagementController::class, 'index'])->name('admin.locations.index');
Route::get('/locations/data', [LocationManagementController::class, 'getRequestsData'])->name('admin.locations.data');
Route::get('/locations/create', [LocationManagementController::class, 'create'])->name('admin.locations.create');
Route::post('/locations', [LocationManagementController::class, 'store'])->name('admin.locations.store');
Route::get('/locations/{id}', [LocationManagementController::class, 'show'])->name('admin.locations.show');
Route::get('/locations/{id}/edit', [LocationManagementController::class, 'edit'])->name('admin.locations.edit');
Route::put('/locations/{id}', [LocationManagementController::class, 'update'])->name('admin.locations.update');
Route::delete('/locations/{id}', [LocationManagementController::class, 'destroy'])->name('admin.locations.destroy');

// Approval/Rejection Routes
Route::post('/locations/{id}/approve', [LocationManagementController::class, 'approve'])->name('admin.locations.approve');
Route::post('/locations/{id}/reject', [LocationManagementController::class, 'reject'])->name('admin.locations.reject');
Route::post('/locations/{id}/restore', [LocationManagementController::class, 'restore'])->name('admin.locations.restore');
