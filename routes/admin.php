<?php

use App\Http\Controllers\Admin\AdminClubController;
use App\Http\Controllers\Admin\AdminOverviewController;
use App\Http\Controllers\Admin\AdminUserManagementController;
use App\Http\Controllers\Admin\AdminClubManagementController;
use App\Http\Controllers\Admin\AdminClubRequestController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
  Route::delete('/clubs/{club}/kick/{userId}', [AdminClubController::class, 'kickMember'])->name('clubs.kick');
  Route::get('/clubs/{club}/edit', [AdminClubController::class, 'edit'])->name('clubs.edit');
  Route::put('/clubs/{club}', [AdminClubController::class, 'update'])->name('clubs.update');
  Route::get('/club/request', [AdminClubRequestController::class, 'index'])->name('clubs.request');
  Route::get('/club/request/{clubRequest}', [AdminClubRequestController::class, 'show'])->name('clubs.request.show');
  Route::patch('/club/request/{clubRequest}/accept', [AdminClubRequestController::class, 'accept'])->name('clubs.request.accept');
  Route::patch('/club/request/{clubRequest}/reject', [AdminClubRequestController::class, 'reject'])->name('clubs.request.reject');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
  Route::get('/overview', [AdminOverviewController::class, 'index'])->name('overview');
  Route::get('/user-management', [AdminUserManagementController::class, 'index'])->name('user-management');
  Route::get('/club-management', [AdminClubManagementController::class, 'index'])->name('club-management');
});
