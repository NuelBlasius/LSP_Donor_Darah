<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DonorRegistrationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ScheduleAdminController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    }

    return view('home');
})->name('home');
Route::get('/layanan', [ServiceController::class, 'index'])->name('services');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');

    Route::get('/riwayat-pendaftaran', [DonorRegistrationController::class, 'index'])
        ->name('donor.registrations.index');
    Route::get('/pendaftaran-donor/{schedule}/form', [DonorRegistrationController::class, 'create'])
        ->name('donor.registrations.create');
    Route::post('/pendaftaran-donor', [DonorRegistrationController::class, 'store'])
        ->name('donor.registrations.store');
    Route::get('/pendaftaran-donor/{registration}/pembayaran', [DonorRegistrationController::class, 'paymentForm'])
        ->name('donor.registrations.payment.form');
    Route::post('/pendaftaran-donor/{registration}/pembayaran', [DonorRegistrationController::class, 'paymentStore'])
        ->name('donor.registrations.payment.store');
});

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/akun', [AdminController::class, 'accounts'])->name('accounts.index');
    Route::patch('/akun/{user}/status', [AdminController::class, 'updateAccountStatus'])->name('accounts.status');

    Route::get('/pendaftaran', [DonorRegistrationController::class, 'adminIndex'])->name('registrations.index');
    // Backward-compatible redirect for stale links; payments are handled in registrations.
    Route::get('/payments', function () { return redirect()->route('admin.registrations.index'); })->name('payments.index');
    Route::patch('/pendaftaran/{registration}/status', [DonorRegistrationController::class, 'adminUpdateStatus'])->name('registrations.status');

    Route::patch('/pendaftaran/{registration}/pembayaran', [DonorRegistrationController::class, 'adminUpdatePayment'])->name('payments.status');

    Route::get('/jadwal', [ScheduleAdminController::class, 'index'])->name('schedules.index');
    Route::get('/jadwal/create', [ScheduleAdminController::class, 'create'])->name('schedules.create');
    Route::post('/jadwal', [ScheduleAdminController::class, 'store'])->name('schedules.store');
    Route::get('/jadwal/{schedule}/edit', [ScheduleAdminController::class, 'edit'])->name('schedules.edit');
    Route::put('/jadwal/{schedule}', [ScheduleAdminController::class, 'update'])->name('schedules.update');
    Route::delete('/jadwal/{schedule}', [ScheduleAdminController::class, 'destroy'])->name('schedules.destroy');

    Route::resource('pengumuman', AnnouncementController::class)
        ->except(['show'])
        ->names([
            'index' => 'announcements.index',
            'create' => 'announcements.create',
            'store' => 'announcements.store',
            'edit' => 'announcements.edit',
            'update' => 'announcements.update',
            'destroy' => 'announcements.destroy',
        ]);
});
