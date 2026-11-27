<?php

namespace App\Http\Controllers;

use App\Models\DonorSchedule;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function index()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $schedules = DonorSchedule::query()
            ->withCount([
                'registrations as reserved_count' => fn ($query) => $query
                    ->whereIn('registration_status', ['pending', 'approved']),
            ])
            ->where('status', 'open')
            ->whereDate('schedule_date', '>=', today())
            ->orderBy('schedule_date')
            ->get();

        $registeredScheduleIds = Auth::check()
            ? Auth::user()->donorRegistrations()
                ->whereIn('registration_status', ['pending', 'approved'])
                ->pluck('donor_schedule_id')
                ->all()
            : [];

        return view('services.index', compact('registeredScheduleIds', 'schedules'));
    }
}
