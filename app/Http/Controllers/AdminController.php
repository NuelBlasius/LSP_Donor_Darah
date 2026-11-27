<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\DonorRegistration;
use App\Models\DonorSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    /**
     * Pastikan pengguna yang mengakses adalah admin.
     */
    private function authorizeAdmin(): void
    {
        abort_unless(
            Auth::user()?->role === 'admin',
            403,
            'Akses hanya untuk admin.'
        );
    }

    /**
     * Dashboard admin.
     */
    public function dashboard()
    {
        $this->authorizeAdmin();

        $stats = [
            'pending_accounts' => User::query()
                ->where('role', 'donor')
                ->where('account_status', 'pending')
                ->count(),

            'pending_registrations' => DonorRegistration::query()
                ->where('registration_status', 'pending')
                ->count(),

            'pending_payments' => DonorRegistration::query()
                ->whereNotNull('payment_proof')
                ->where('payment_status', 'pending')
                ->count(),

            'open_schedules' => DonorSchedule::query()
                ->where('status', 'open')
                ->whereDate('schedule_date', '>=', today())
                ->count(),

            'announcements' => Announcement::count(),
        ];

        $stats['pending_work'] =
            $stats['pending_registrations']
            + $stats['pending_payments'];

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Daftar akun pendonor.
     *
     * Filter:
     * - Nama pendonor
     * - Tanggal pembuatan akun
     */
    public function accounts(Request $request)
    {
        $this->authorizeAdmin();

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $query = User::query()
            ->where('role', 'donor');

        if (! empty($filters['search'])) {
            $search = trim($filters['search']);

            $query->where('name', 'like', "%{$search}%");
        }

        if (! empty($filters['date'])) {
            $query->whereDate(
                'created_at',
                $filters['date']
            );
        }

        $users = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.accounts.index',
            compact('users')
        );
    }

    /**
     * Setujui atau tolak akun pendonor.
     */
    public function updateAccountStatus(
        Request $request,
        User $user
    ) {
        $this->authorizeAdmin();

        abort_unless(
            $user->role === 'donor',
            404,
            'Akun pendonor tidak ditemukan.'
        );

        $data = $request->validate([
            'account_status' => [
                'required',
                'in:approved,rejected',
            ],
        ]);

        $user->update([
            'account_status' => $data['account_status'],
        ]);

        $message = $data['account_status'] === 'approved'
            ? 'Akun pendonor berhasil disetujui.'
            : 'Akun pendonor berhasil ditolak.';

        return back()->with('success', $message);
    }
}
