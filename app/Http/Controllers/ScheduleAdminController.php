<?php

namespace App\Http\Controllers;

use App\Models\DonorSchedule;
use Illuminate\Http\Request;

class ScheduleAdminController extends Controller
{
    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()->role === 'admin', 403, 'Akses hanya untuk admin.');
    }

    public function index()
    {
        $this->authorizeAdmin();
        $schedules = DonorSchedule::latest('schedule_date')->paginate(10);

        return view('admin.schedules.index', compact('schedules'));
    }

    public function create()
    {
        $this->authorizeAdmin();
        return view('admin.schedules.form', ['schedule' => new DonorSchedule()]);
    }

    public function store(Request $request)
    {
        $this->authorizeAdmin();
        DonorSchedule::create($this->validated($request));

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil ditambahkan.');
    }

    public function edit(DonorSchedule $schedule)
    {
        $this->authorizeAdmin();
        return view('admin.schedules.form', compact('schedule'));
    }

    public function update(Request $request, DonorSchedule $schedule)
    {
        $this->authorizeAdmin();
        $schedule->update($this->validated($request));

        return redirect()->route('admin.schedules.index')->with('success', 'Jadwal berhasil diperbarui.');
    }

    public function destroy(DonorSchedule $schedule)
    {
        $this->authorizeAdmin();
        $schedule->delete();

        return back()->with('success', 'Jadwal berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $amount = preg_replace('/[^0-9]/', '', (string) $request->input('amount', '0'));
        $request->merge(['amount' => $amount === '' ? 0 : (int) $amount]);

        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'location' => ['required', 'string', 'max:255'],
            'schedule_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'quota' => ['required', 'integer', 'min:1'],
            'amount' => ['required', 'integer', 'min:0'],
            'status' => ['required', 'in:open,closed'],
        ]);
    }
}
