<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ReservationController::class, 'index'])->name('reservations.index');
Route::get('/receipt/verify', [ReservationController::class, 'verify'])->name('reservations.receipt.verify');

Route::middleware('auth')->group(function () {
    Route::post('/reserve', [ReservationController::class, 'store'])->name('reservations.store');
    Route::post('/reservations/{reservation}/reschedule', [ReservationController::class, 'reschedule'])->name('reservations.reschedule');
    Route::get('/receipt/{reservation}', [ReservationController::class, 'receipt'])->name('reservations.receipt');
    Route::get('/dashboard', DashboardController::class)->name('dashboard');
    Route::get('/admin', [DashboardController::class, 'admin'])->name('admin');
    Route::get('/admin/dashboard', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::post('/admin/court-count', [DashboardController::class, 'updateCourtCount'])->name('admin.courts.update');
    Route::post('/admin/rates', [DashboardController::class, 'updateRates'])->name('admin.rates.update');
    Route::post('/admin/public-reservations/visibility', [DashboardController::class, 'updatePublicReservationVisibility'])->name('admin.public-reservations.visibility.update');
    Route::post('/admin/walk-in-reservations', [DashboardController::class, 'storeWalkIn'])->name('admin.walkins.store');
    Route::post('/admin/reservations/{reservation}/unlock-reschedule', [DashboardController::class, 'unlockReschedule'])->name('admin.reservations.unlock-reschedule');
    Route::post('/admin/reservations/{reservation}/lock-reschedule', [DashboardController::class, 'lockReschedule'])->name('admin.reservations.lock-reschedule');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
