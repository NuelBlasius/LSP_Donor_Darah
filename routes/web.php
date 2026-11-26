<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');

    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

Route::get('/dashboard', function () {
    $userId = auth()->id();

    $baseQuery = DB::table('donor_registrations')
        ->where('user_id', $userId);

    $totalRegistrations = (clone $baseQuery)->count();
    $pendingRegistrations = (clone $baseQuery)
        ->where('registration_status', 'pending')
        ->count();
    $completedDonations = (clone $baseQuery)
        ->where('registration_status', 'completed')
        ->count();

    $registrations = DB::table('donor_registrations')
        ->join('donor_schedules', 'donor_registrations.donor_schedule_id', '=', 'donor_schedules.id')
        ->where('donor_registrations.user_id', $userId)
        ->select(
            'donor_registrations.registration_status',
            'donor_registrations.created_at',
            'donor_schedules.title as schedule_title',
            'donor_schedules.location',
            'donor_schedules.schedule_date'
        )
        ->orderByDesc('donor_registrations.created_at')
        ->limit(5)
        ->get();

    return view('dashboard', compact(
        'totalRegistrations',
        'pendingRegistrations',
        'completedDonations',
        'registrations'
    ));
})->middleware('auth')->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
