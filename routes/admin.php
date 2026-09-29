<?php

use App\Http\Controllers\Admin\AdminOverviewController;
use App\Http\Controllers\Admin\AuditController;
use App\Http\Controllers\Admin\AdminUserManagementController;
use App\Http\Controllers\Admin\AdminClubManagementController;
use App\Http\Controllers\Admin\AdminClubRequestController;
use App\Http\Controllers\Admin\ModerationController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
  Route::get('/club/request', [AdminClubRequestController::class, 'index'])->name('clubs.request');
  Route::get('/club/request/{clubRequest}', [AdminClubRequestController::class, 'show'])->name('clubs.request.show');
  Route::patch('/club/request/{clubRequest}/accept', [AdminClubRequestController::class, 'accept'])->name('clubs.request.accept');
  Route::patch('/club/request/{clubRequest}/reject', [AdminClubRequestController::class, 'reject'])->name('clubs.request.reject');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
  Route::get('/overview', [AdminOverviewController::class, 'index'])->name('overview');
  Route::get('/user-management', [AdminUserManagementController::class, 'index'])->name('user-management');
  Route::get('/club-management', [AdminClubManagementController::class, 'index'])->name('club-management');
  Route::get('/club-management/{club}', [AdminClubManagementController::class, 'show'])->name('club-management.show');
  Route::get('/moderation', [ModerationController::class, 'index'])->name('moderation');
  Route::get('/moderation/{report}', [ModerationController::class, 'show'])->name('moderation.report.show');
  Route::patch('/moderation/{report}/resolved', [ModerationController::class, 'resolved'])->name('moderation.report.resolved');
  Route::patch('/moderation/{report}/ignored', [ModerationController::class, 'ignored'])->name('moderation.report.ignored');
  Route::get('/moderation/appeals/{appeal}', [ModerationController::class, 'appeal'])->name('moderation.appeal.show');
  Route::patch('/moderation/appeals/{appeal}/approve', [ModerationController::class, 'appealApprove'])->name('moderation.appeal.approve');
  Route::patch('/moderation/appeals/{appeal}/reject', [ModerationController::class, 'appealReject'])->name('moderation.appeal.reject');
  Route::get('/audit-logs', [AuditController::class, 'index'])->name('audit-logs');
  Route::get('/audit-logs/{auditLog}', [AuditController::class, 'show'])->name('audit-logs.show');
  Route::put('/hobbies/store', [AdminOverviewController::class, 'storeHobby'])->name('hobbies.store');
  Route::delete('/hobbies/delete/{hobby}', [AdminOverviewController::class, 'deleteHobby'])->name('hobbies.delete');
});
