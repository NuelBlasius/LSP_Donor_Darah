<?php

namespace App\Http\Controllers;

use App\Models\DonorNotification;
use App\Models\DonorRegistration;
use App\Models\DonorSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DonorRegistrationController extends Controller
{
    /**
     * Hak akses admin.
     */
    private function authorizeAdmin(): void
    {
        abort_unless(
            Auth::user()->role === 'admin',
            403,
            'Akses hanya untuk admin.'
        );
    }

    /**
     * Hak akses pendonor.
     */
    private function authorizeDonor(): void
    {
        abort_unless(
            Auth::user()->role === 'donor',
            403,
            'Fitur ini hanya untuk pendonor.'
        );
    }

    /**
     * Riwayat pendaftaran pendonor.
     */
    public function index()
    {
        $this->authorizeDonor();

        $filters = request()->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $query = DonorRegistration::query()
            ->with('schedule')
            ->where('user_id', Auth::id());

        if (! empty($filters['search'])) {
            $query->whereHas('schedule', function ($scheduleQuery) use ($filters) {
                $scheduleQuery->where('title', 'like', '%'.trim($filters['search']).'%');
            });
        }

        if (! empty($filters['date'])) {
            $query->whereHas('schedule', function ($scheduleQuery) use ($filters) {
                $scheduleQuery->whereDate('schedule_date', $filters['date']);
            });
        }

        $registrations = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'donor.registrations.index',
            compact('registrations')
        );
    }

    /**
     * Form pendaftaran jadwal donor.
     */
    public function create(DonorSchedule $schedule)
    {
        $this->authorizeDonor();

        $user = Auth::user();

        if ($user->account_status !== 'approved') {
            return redirect()
                ->route('services')
                ->with(
                    'error',
                    'Akun harus diverifikasi admin sebelum mendaftar jadwal donor.'
                );
        }

        if (
            ! $user->nik ||
            ! $user->jenis_kelamin ||
            ! $user->golongan_darah ||
            ! $user->tanggal_lahir ||
            ! $user->alamat ||
            ! $user->nomor_telepon
        ) {
            return redirect()
                ->route('profile.edit')
                ->with(
                    'error',
                    'Lengkapi data profil pendonor terlebih dahulu.'
                );
        }

        if (
            $schedule->status !== 'open' ||
            $schedule->schedule_date->lt(today())
        ) {
            return redirect()
                ->route('services')
                ->with(
                    'error',
                    'Jadwal ini sudah tidak tersedia.'
                );
        }

        $registeredCount = DonorRegistration::query()
            ->where('donor_schedule_id', $schedule->id)
            ->whereIn('registration_status', [
                'pending',
                'approved',
            ])
            ->count();

        if ($registeredCount >= $schedule->quota) {
            return redirect()
                ->route('services')
                ->with(
                    'error',
                    'Kuota jadwal sudah penuh.'
                );
        }

        $alreadyRegistered = DonorRegistration::query()
            ->where('user_id', $user->id)
            ->where('donor_schedule_id', $schedule->id)
            ->whereIn('registration_status', [
                'pending',
                'approved',
            ])
            ->exists();

        if ($alreadyRegistered) {
            return redirect()
                ->route('donor.registrations.index')
                ->with(
                    'error',
                    'Anda sudah mendaftar pada jadwal ini.'
                );
        }

        $scheduleStatusText = $schedule->status === 'open'
            ? 'Jadwal dibuka'
            : 'Jadwal ditutup';

        return view(
            'donor.registrations.create',
            compact('schedule', 'scheduleStatusText', 'user')
        );
    }

    /**
     * Simpan pendaftaran donor.
     */
    public function store(Request $request)
    {
        $this->authorizeDonor();

        $user = Auth::user();

        if ($user->account_status !== 'approved') {
            return back()->with(
                'error',
                'Akun harus diverifikasi admin sebelum mendaftar jadwal donor.'
            );
        }

        if (
            ! $user->nik ||
            ! $user->jenis_kelamin ||
            ! $user->golongan_darah ||
            ! $user->tanggal_lahir ||
            ! $user->alamat ||
            ! $user->nomor_telepon
        ) {
            return redirect()
                ->route('profile.edit')
                ->with(
                    'error',
                    'Lengkapi data profil pendonor terlebih dahulu.'
                );
        }

        $data = $request->validate([
            'donor_schedule_id' => [
                'required',
                'exists:donor_schedules,id',
            ],
        ]);

        $schedule = DonorSchedule::findOrFail(
            $data['donor_schedule_id']
        );

        if (
            $schedule->status !== 'open' ||
            $schedule->schedule_date->lt(today())
        ) {
            return back()->with(
                'error',
                'Jadwal ini sudah tidak tersedia.'
            );
        }

        $registeredCount = DonorRegistration::query()
            ->where('donor_schedule_id', $schedule->id)
            ->whereIn('registration_status', [
                'pending',
                'approved',
            ])
            ->count();

        if ($registeredCount >= $schedule->quota) {
            return back()->with(
                'error',
                'Kuota jadwal sudah penuh.'
            );
        }

        $alreadyRegistered = DonorRegistration::query()
            ->where('user_id', Auth::id())
            ->where('donor_schedule_id', $schedule->id)
            ->whereIn('registration_status', [
                'pending',
                'approved',
            ])
            ->exists();

        if ($alreadyRegistered) {
            return back()->with(
                'error',
                'Anda sudah mendaftar pada jadwal ini.'
            );
        }

        DonorRegistration::create([
            'user_id' => Auth::id(),
            'donor_schedule_id' => $schedule->id,
            'registration_status' => 'pending',
            'payment_status' => 'pending',
        ]);

        return redirect()
            ->route('donor.registrations.index')
            ->with(
                'success',
                'Pendaftaran jadwal berhasil dikirim dan menunggu verifikasi admin.'
            );
    }

    /**
     * Halaman pembayaran pendonor.
     */
    public function paymentForm(
        DonorRegistration $registration
    ) {
        $this->authorizeDonor();

        abort_unless(
            $registration->user_id === Auth::id(),
            403
        );

        if (
            $registration->registration_status !== 'approved'
        ) {
            return redirect()
                ->route('donor.registrations.index')
                ->with(
                    'error',
                    'Pembayaran dapat dikonfirmasi setelah pendaftaran disetujui admin.'
                );
        }

        if ($registration->payment_status === 'approved') {
            return redirect()
                ->route('donor.registrations.index')
                ->with(
                    'success',
                    'Pembayaran Anda sudah diverifikasi admin.'
                );
        }

        if (
            $registration->payment_proof &&
            $registration->payment_status === 'pending'
        ) {
            return redirect()
                ->route('donor.registrations.index')
                ->with(
                    'success',
                    'Bukti pembayaran sedang menunggu verifikasi admin.'
                );
        }

        if (
            ! $registration->payment_expires_at ||
            $registration->payment_expires_at->isPast()
        ) {
            $registration->payment_reference =
                '880'.str_pad(
                    (string) $registration->id,
                    10,
                    '0',
                    STR_PAD_LEFT
                );

            $registration->payment_expires_at =
                now()->addMinutes(15);

            $registration->save();
        }

        return view(
            'donor.registrations.payment',
            compact('registration')
        );
    }

    /**
     * Simpan bukti pembayaran pendonor.
     */
    public function paymentStore(
        Request $request,
        DonorRegistration $registration
    ) {
        $this->authorizeDonor();

        abort_unless(
            $registration->user_id === Auth::id(),
            403
        );

        if (
            $registration->registration_status !== 'approved'
        ) {
            return back()->with(
                'error',
                'Pendaftaran belum disetujui admin.'
            );
        }

        if ($registration->payment_status === 'approved') {
            return back()->with(
                'error',
                'Pembayaran sudah diverifikasi admin.'
            );
        }

        if (
            $registration->payment_proof &&
            $registration->payment_status === 'pending'
        ) {
            return back()->with(
                'error',
                'Bukti pembayaran sedang menunggu verifikasi admin.'
            );
        }

        $data = $request->validate([
            'payment_method' => [
                'required',
                'in:virtual_account,qris',
            ],

            'payment_proof' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
        ]);

        if (
            ! $registration->payment_expires_at ||
            $registration->payment_expires_at->isPast()
        ) {
            return back()->with(
                'error',
                'Kode pembayaran telah kedaluwarsa. Muat ulang halaman untuk membuat kode baru.'
            );
        }

        $oldProof = $registration->payment_proof;

        $newProof = $data['payment_proof']->store(
            'payment-proofs',
            'public'
        );

        if (! $newProof) {
            return back()->with(
                'error',
                'Gagal menyimpan bukti pembayaran.'
            );
        }

        $registration->update([
            'payment_proof' => $newProof,
            'payment_method' => $data['payment_method'],
            'payment_status' => 'pending',
        ]);

        if ($oldProof) {
            Storage::disk('public')->delete($oldProof);
        }

        return redirect()
            ->route('donor.registrations.index')
            ->with(
                'success',
                'Bukti pembayaran berhasil dikirim untuk diperiksa admin.'
            );
    }

    /**
     * Halaman verifikasi pendaftaran admin.
     */
    public function adminIndex(Request $request)
    {
        $this->authorizeAdmin();

        $filters = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:100',
            ],

            'date' => [
                'nullable',
                'date_format:Y-m-d',
            ],

            'tab' => [
                'nullable',
                'in:registrations,payments',
            ],
        ]);

        $activeTab = $filters['tab'] ?? 'registrations';

        $query = DonorRegistration::query()
            ->with(['user', 'schedule']);

        if ($activeTab === 'payments') {
            $query
                ->whereNotNull('payment_proof')
                ->where('payment_status', 'pending');
        } else {
            $query->where('registration_status', 'pending');
        }

        if (! empty($filters['search'])) {
            $search = trim($filters['search']);

            $query->whereHas('user', function ($userQuery) use ($search) {
                $userQuery->where(
                    'name',
                    'like',
                    "%{$search}%"
                );
            });
        }

        /*
         * Tanggal yang digunakan adalah tanggal
         * pembuatan akun pendonor.
         */
        if (! empty($filters['date'])) {
            $query->whereHas('schedule', function ($scheduleQuery) use ($filters) {
                $scheduleQuery->whereDate(
                    'schedule_date',
                    $filters['date']
                );
            });
        }

        $registrations = $query
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view(
            'admin.registrations.index',
            compact('activeTab', 'registrations')
        );
    }

    /**
     * Setujui atau tolak pendaftaran donor.
     */
    public function adminUpdateStatus(
        Request $request,
        DonorRegistration $registration
    ) {
        $this->authorizeAdmin();

        $data = $request->validate([
            'registration_status' => [
                'required',
                'in:approved,rejected',
            ],
        ]);

        DB::transaction(function () use (
            $registration,
            $data
        ) {
            $registration->update([
                'registration_status' => $data['registration_status'],
            ]);

            $approved =
                $data['registration_status'] === 'approved';

            DonorNotification::create([
                'user_id' => $registration->user_id,

                'title' => $approved
                    ? 'Pendaftaran disetujui'
                    : 'Pendaftaran ditolak',

                'message' => $approved
                    ? 'Pendaftaran jadwal '
                        .($registration->schedule?->title ?? '')
                        .' disetujui. Silakan lanjutkan pembayaran.'
                    : 'Pendaftaran jadwal '
                        .($registration->schedule?->title ?? '')
                        .' ditolak.',
            ]);
        });

        return back()->with(
            'success',
            'Status pendaftaran berhasil diperbarui.'
        );
    }

    /**
     * Setujui atau tolak bukti pembayaran.
     */
    public function adminUpdatePayment(
        Request $request,
        DonorRegistration $registration
    ) {
        $this->authorizeAdmin();

        abort_unless(
            $registration->payment_proof,
            404,
            'Bukti pembayaran tidak ditemukan.'
        );

        $data = $request->validate([
            'payment_status' => [
                'required',
                'in:approved,rejected',
            ],
        ]);

        DB::transaction(function () use (
            $registration,
            $data
        ) {
            $approved =
                $data['payment_status'] === 'approved';

            $registration->update([
                'payment_status' => $data['payment_status'],
            ]);

            DonorNotification::create([
                'user_id' => $registration->user_id,

                'title' => $approved
                    ? 'Pendaftaran donor telah dikonfirmasi'
                    : 'Status pembayaran diperbarui',

                'message' => $approved
                    ? 'Pembayaran Anda telah diverifikasi. Pendaftaran donor '
                        .($registration->schedule?->title ?? '')
                        .' resmi terdaftar.'
                    : 'Pembayaran pendaftaran '
                        .($registration->schedule?->title ?? '')
                        .' ditolak. Silakan periksa bukti pembayaran dan kirim ulang.',
            ]);
        });

        return back()->with(
            'success',
            'Status pembayaran berhasil diperbarui.'
        );
    }
}
