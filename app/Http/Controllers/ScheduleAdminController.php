<?php

namespace App\Http\Controllers;

use App\Models\DonorSchedule;
use GuzzleHttp\TransferStats;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ScheduleAdminController extends Controller
{
    /**
     * Pastikan pengguna adalah admin.
     */
    private function authorizeAdmin(): void
    {
        abort_unless(
            auth()->user()->role === 'admin',
            403,
            'Akses hanya untuk admin.'
        );
    }

    /**
     * Menampilkan seluruh jadwal.
     */
    public function index(Request $request): View
    {
        $this->authorizeAdmin();

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'queue' => ['nullable', 'in:registrations,payments'],
        ]);

        $query = DonorSchedule::query()
            ->withCount([
                'registrations as registered_count' => function ($registrationQuery) {
                    $registrationQuery->whereIn('registration_status', [
                        'pending',
                        'approved',
                    ]);
                },
            ]);

        if (! empty($filters['search'])) {
            $query->where('title', 'like', '%'.trim($filters['search']).'%');
        }

        if (! empty($filters['date'])) {
            $query->whereDate('schedule_date', $filters['date']);
        }

        if (($filters['queue'] ?? null) === 'registrations') {
            $query->whereHas('registrations', function ($registrationQuery) {
                $registrationQuery->where('registration_status', 'pending');
            });
        }

        if (($filters['queue'] ?? null) === 'payments') {
            $query->whereHas('registrations', function ($registrationQuery) {
                $registrationQuery
                    ->whereNotNull('payment_proof')
                    ->where('payment_status', 'pending');
            });
        }

        $schedules = $query
            ->latest('schedule_date')
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.schedules.index',
            compact('schedules')
        );
    }

    /**
     * Menampilkan detail jadwal dan daftar pendonor yang mendaftar.
     */
    public function show(Request $request, DonorSchedule $schedule): View
    {
        $this->authorizeAdmin();

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $registrationsQuery = $schedule->registrations()->with('user');

        if (! empty($filters['search'])) {
            $search = trim($filters['search']);

            $registrationsQuery->whereHas('user', function ($userQuery) use ($search) {
                $userQuery->where('name', 'like', "%{$search}%");
            });
        }

        if (! empty($filters['date'])) {
            $registrationsQuery->whereDate('created_at', $filters['date']);
        }

        $registrations = $registrationsQuery
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $schedule->loadCount([
            'registrations as registered_count' => function ($registrationQuery) {
                $registrationQuery->whereIn('registration_status', [
                    'pending',
                    'approved',
                ]);
            },
        ]);

        return view('admin.schedules.show', compact('schedule', 'registrations'));
    }

    /**
     * Form tambah jadwal.
     */
    public function create(): View
    {
        $this->authorizeAdmin();

        $schedule = new DonorSchedule;

        return view(
            'admin.schedules.form',
            compact('schedule')
        );
    }

    /**
     * Simpan jadwal baru.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorizeAdmin();

        DonorSchedule::create(
            $this->validated($request)
        );

        return redirect()
            ->route('admin.schedules.index')
            ->with(
                'success',
                'Jadwal berhasil ditambahkan.'
            );
    }

    /**
     * Form edit jadwal.
     */
    public function edit(DonorSchedule $schedule): View
    {
        $this->authorizeAdmin();

        return view(
            'admin.schedules.form',
            compact('schedule')
        );
    }

    /**
     * Perbarui jadwal.
     */
    public function update(
        Request $request,
        DonorSchedule $schedule
    ): RedirectResponse {
        $this->authorizeAdmin();

        $schedule->update(
            $this->validated($request)
        );

        return redirect()
            ->route('admin.schedules.index')
            ->with(
                'success',
                'Jadwal berhasil diperbarui.'
            );
    }

    /**
     * Hapus jadwal.
     */
    public function destroy(DonorSchedule $schedule): RedirectResponse
    {
        $this->authorizeAdmin();

        $schedule->delete();

        return back()->with(
            'success',
            'Jadwal berhasil dihapus.'
        );
    }

    /**
     * Validasi dan normalisasi data jadwal.
     */
    private function validated(Request $request): array
    {
        /*
         * Hilangkan karakter selain angka.
         *
         * Contoh:
         * 150.000 -> 150000
         * Rp 150.000 -> 150000
         */
        $amount = preg_replace(
            '/[^0-9]/',
            '',
            (string) $request->input('amount', '0')
        );

        $request->merge([
            'amount' => $amount === ''
                ? 0
                : (int) $amount,
        ]);

        $data = $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'location' => [
                'required',
                'url',
                'max:2048',
                function ($attribute, $value, $fail) {
                    if (! $this->isGoogleMapsUrl($value)) {
                        $fail('Masukkan tautan Google Maps yang valid.');
                    }
                },
            ],

            'schedule_date' => [
                'required',
                'date',
                'after_or_equal:today',
            ],

            'start_time' => [
                'required',
                'date_format:H:i',
            ],

            'end_time' => [
                'required',
                'date_format:H:i',
                'after:start_time',
            ],

            'quota' => [
                'required',
                'integer',
                'min:1',
            ],

            'amount' => [
                'required',
                'integer',
                'min:0',
            ],

            'status' => [
                'required',
                'in:open,closed',
            ],
        ]);

        $data['location'] = $this->expandShortGoogleMapsUrl($data['location']);

        return $data;
    }

    private function isGoogleMapsUrl(string $url): bool
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));
        $path = strtolower((string) parse_url($url, PHP_URL_PATH));
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
        $googleDomain = preg_match('/(^|\.)google\.[a-z.]+$/i', $host) === 1;
        $hasMapsPath = str_contains($path, '/maps')
            || str_starts_with($host, 'maps.google.');

        return $scheme === 'https'
            && ($host === 'maps.app.goo.gl'
                || ($host === 'goo.gl' && str_starts_with($path, '/maps/'))
                || ($googleDomain && $hasMapsPath));
    }

    private function expandShortGoogleMapsUrl(string $url): string
    {
        $host = strtolower((string) parse_url($url, PHP_URL_HOST));

        if (! in_array($host, ['maps.app.goo.gl', 'goo.gl'], true)) {
            return $url;
        }

        $expandedUrl = null;

        try {
            Http::timeout(4)
                ->withOptions([
                    'on_stats' => function (TransferStats $stats) use (&$expandedUrl) {
                        $expandedUrl = (string) $stats->getEffectiveUri();
                    },
                ])
                ->get($url);
        } catch (\Throwable) {
            return $url;
        }

        return $expandedUrl && $this->isGoogleMapsUrl($expandedUrl)
            ? $expandedUrl
            : $url;
    }
}
