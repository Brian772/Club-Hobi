<?php

use App\Http\Controllers\Admin\AdminClubController;
use App\Http\Controllers\Admin\AdminOverviewController;
use App\Http\Controllers\Admin\AdminUserManagementController;
use App\Http\Controllers\Admin\AdminClubManagementController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
  Route::delete('/clubs/{club}/kick/{userId}', [AdminClubController::class, 'kickMember'])->name('clubs.kick');
  Route::get('/clubs/{club}/edit', [AdminClubController::class, 'edit'])->name('clubs.edit');
  Route::put('/clubs/{club}', [AdminClubController::class, 'update'])->name('clubs.update');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
  Route::get('/overview', [AdminOverviewController::class, 'index'])->name('overview');
  Route::get('/user-management', [AdminUserManagementController::class, 'index'])->name('user-management');
  Route::get('/club-management', [AdminClubManagementController::class, 'index'])->name('club-management');
});
