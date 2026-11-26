<?php

namespace App\Http\Controllers;

use App\Models\DonorSchedule;

class ServiceController extends Controller
{
    public function index()
    {
        if (auth()->check() && auth()->user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $schedules = DonorSchedule::where('status', 'open')
            ->whereDate('schedule_date', '>=', today())
            ->orderBy('schedule_date')
            ->get();

        return view('services.index', compact('schedules'));
    }
}
