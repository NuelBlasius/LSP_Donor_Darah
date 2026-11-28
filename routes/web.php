
<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DonorChatController;
use App\Http\Controllers\DonorRegistrationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ScheduleAdminController;
use App\Http\Controllers\ServiceController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Halaman publik
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    if (! Auth::check()) {
        return view('home');
    }

    return Auth::user()->role === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('dashboard');
})->name('home');

Route::get('/layanan', [ServiceController::class, 'index'])
    ->name('services');

/*
|--------------------------------------------------------------------------
| Autentikasi
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.store');

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Fitur pendonor
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profil', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profil', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::get('/dashboard', [AuthController::class, 'dashboard'])
        ->name('dashboard');

    Route::get('/riwayat-pendaftaran', [
        DonorRegistrationController::class,
        'index',
    ])->name('donor.registrations.index');

    Route::get('/bantuan-donor', [DonorChatController::class, 'index'])
        ->name('donor.chat.index');

    Route::post('/bantuan-donor', [DonorChatController::class, 'message'])
        ->middleware('throttle:20,1')
        ->name('donor.chat.message');

    Route::get('/pendaftaran-donor/{schedule}/form', [
        DonorRegistrationController::class,
        'create',
    ])->name('donor.registrations.create');

    Route::post('/pendaftaran-donor', [
        DonorRegistrationController::class,
        'store',
    ])->name('donor.registrations.store');

    Route::get('/pendaftaran-donor/{registration}/pembayaran', [
        DonorRegistrationController::class,
        'paymentForm',
    ])->name('donor.registrations.payment.form');

    Route::post('/pendaftaran-donor/{registration}/pembayaran', [
        DonorRegistrationController::class,
        'paymentStore',
    ])->name('donor.registrations.payment.store');
});

/*
|--------------------------------------------------------------------------
| Admin
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->name('admin.')
    ->middleware('auth')
    ->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('dashboard');

        /*
        | Verifikasi akun
        */

        Route::get('/akun', [AdminController::class, 'accounts'])
            ->name('accounts.index');

        Route::patch('/akun/{user}/status', [
            AdminController::class,
            'updateAccountStatus',
        ])->name('accounts.status');

        /*
        | Pendaftaran dan pembayaran
        |
        | Pendaftaran dan pembayaran dikelola dari detail jadwal donor.
        */

        Route::get('/pendaftaran', function () {
            return redirect()->route('admin.schedules.index');
        })->name('registrations.index');

        Route::patch('/pendaftaran/{registration}/status', [
            DonorRegistrationController::class,
            'adminUpdateStatus',
        ])->name('registrations.status');

        Route::patch('/pendaftaran/{registration}/pembayaran', [
            DonorRegistrationController::class,
            'adminUpdatePayment',
        ])->name('payments.status');

        /*
        | Kompatibilitas route lama
        |
        | Route lama diarahkan ke daftar jadwal donor.
        */

        Route::get('/payments', function () {
            return redirect()->route('admin.schedules.index');
        })->name('payments.index');

        /*
        | Jadwal donor
        */

        Route::get('/jadwal', [ScheduleAdminController::class, 'index'])
            ->name('schedules.index');

        Route::get('/jadwal/{schedule}/pendaftar/{registration}', [
            DonorRegistrationController::class,
            'adminShow',
        ])->scopeBindings()->name('schedules.registrations.show');

        Route::get('/jadwal/create', [ScheduleAdminController::class, 'create'])
            ->name('schedules.create');

        Route::get('/jadwal/{schedule}', [ScheduleAdminController::class, 'show'])
            ->name('schedules.show');

        Route::post('/jadwal', [ScheduleAdminController::class, 'store'])
            ->name('schedules.store');

        Route::get('/jadwal/{schedule}/edit', [
            ScheduleAdminController::class,
            'edit',
        ])->name('schedules.edit');

        Route::put('/jadwal/{schedule}', [
            ScheduleAdminController::class,
            'update',
        ])->name('schedules.update');

        Route::delete('/jadwal/{schedule}', [
            ScheduleAdminController::class,
            'destroy',
        ])->name('schedules.destroy');

        /*
        | Pengumuman
        */

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
