<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\DonorRegistration;
use App\Models\DonorSchedule;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()->role === 'admin', 403, 'Akses hanya untuk admin.');
    }

    public function dashboard()
    {
        $this->authorizeAdmin();

        $stats = [
            'pending_accounts' => User::where('role', 'donor')->where('account_status', 'pending')->count(),
            'pending_registrations' => DonorRegistration::where('registration_status', 'pending')->count(),
            'pending_payments' => DonorRegistration::whereNotNull('payment_proof')->where('payment_status', 'pending')->count(),
            'pending_work' => DonorRegistration::where('registration_status', 'pending')->count() + DonorRegistration::whereNotNull('payment_proof')->where('payment_status', 'pending')->count(),
            'open_schedules' => DonorSchedule::where('status', 'open')->whereDate('schedule_date', '>=', today())->count(),
            'announcements' => Announcement::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }

    public function accounts()
    {
        $this->authorizeAdmin();

        $userQuery = User::where('role', 'donor');

        if (request()->filled('search')) {
            $userQuery->where('name', 'like', '%' . trim(request('search')) . '%');
        }

        if (request()->filled('date')) {
            $userQuery->whereDate('created_at', request('date'));
        }

        $users = $userQuery->latest()->paginate(10)->withQueryString();

        return view('admin.accounts.index', compact('users'));
    }

    public function updateAccountStatus(Request $request, User $user)
    {
        $this->authorizeAdmin();
        abort_unless($user->role === 'donor', 404);

        $data = $request->validate([
            'account_status' => ['required', 'in:approved,rejected'],
        ]);

        $user->account_status = $data['account_status'];
        $user->save();

        return back()->with('success', 'Status akun pendonor berhasil diperbarui.');
    }
}
