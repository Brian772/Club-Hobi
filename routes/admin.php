<?php

use App\Http\Controllers\Admin\AdminClubController;
use App\Http\Controllers\Admin\AdminDashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
  Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
  Route::delete('/clubs/{club}/kick/{userId}', [AdminClubController::class, 'kickMember'])->name('clubs.kick');
  Route::get('/clubs/{club}/edit', [AdminClubController::class, 'edit'])->name('clubs.edit');
  Route::put('/clubs/{club}', [AdminClubController::class, 'update'])->name('clubs.update');
});
