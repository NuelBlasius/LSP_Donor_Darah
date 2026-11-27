<?php

namespace App\Http\Controllers;

use App\Models\DonorRegistration;
use App\Models\DonorSchedule;
use App\Models\DonorNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DonorRegistrationController extends Controller
{
    private function authorizeAdmin(): void
    {
        abort_unless(auth()->user()->role === 'admin', 403, 'Akses hanya untuk admin.');
    }

    private function authorizeDonor(): void
    {
        abort_unless(auth()->user()->role === 'donor', 403, 'Fitur ini hanya untuk pendonor.');
    }

    public function index()
    {
        $this->authorizeDonor();

        $registrations = DonorRegistration::with('schedule')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('donor.registrations.index', compact('registrations'));
    }

    public function create(DonorSchedule $schedule)
    {
        $this->authorizeDonor();
        $user = auth()->user();

        if ($user->account_status !== 'approved') {
            return redirect()->route('services')->with('error', 'Akun harus diverifikasi admin sebelum mendaftar jadwal donor.');
        }

        if (!$user->nik || !$user->jenis_kelamin || !$user->golongan_darah || !$user->tanggal_lahir || !$user->alamat || !$user->nomor_telepon) {
            return redirect()->route('profile.edit')->with('error', 'Lengkapi data profil pendonor terlebih dahulu.');
        }

        if ($schedule->status !== 'open' || $schedule->schedule_date->lt(today())) {
            return redirect()->route('services')->with('error', 'Jadwal ini sudah tidak tersedia.');
        }

        $count = DonorRegistration::where('donor_schedule_id', $schedule->id)
            ->whereIn('registration_status', ['pending', 'approved'])->count();
        if ($count >= $schedule->quota) {
            return redirect()->route('services')->with('error', 'Kuota jadwal sudah penuh.');
        }

        $alreadyRegistered = DonorRegistration::where('user_id', $user->id)
            ->where('donor_schedule_id', $schedule->id)
            ->whereIn('registration_status', ['pending', 'approved'])->exists();
        if ($alreadyRegistered) {
            return redirect()->route('donor.registrations.index')->with('error', 'Anda sudah mendaftar pada jadwal ini.');
        }

        return view('donor.registrations.create', compact('schedule', 'user'));
    }

    public function store(Request $request)
    {
        $this->authorizeDonor();

        if (auth()->user()->account_status !== 'approved') {
            return back()->with('error', 'Akun harus diverifikasi admin sebelum mendaftar jadwal donor.');
        }

        $user = auth()->user();
        if (!$user->nik || !$user->jenis_kelamin || !$user->golongan_darah || !$user->tanggal_lahir || !$user->alamat || !$user->nomor_telepon) {
            return redirect()->route('profile.edit')->with('error', 'Lengkapi data profil pendonor terlebih dahulu.');
        }

        $data = $request->validate([
            'donor_schedule_id' => ['required', 'exists:donor_schedules,id'],
        ]);

        $schedule = DonorSchedule::findOrFail($data['donor_schedule_id']);

        if ($schedule->status !== 'open' || $schedule->schedule_date->lt(today())) {
            return back()->with('error', 'Jadwal ini sudah tidak tersedia.');
        }

        $count = DonorRegistration::where('donor_schedule_id', $schedule->id)
            ->whereIn('registration_status', ['pending', 'approved'])
            ->count();

        if ($count >= $schedule->quota) {
            return back()->with('error', 'Kuota jadwal sudah penuh.');
        }

        $alreadyRegistered = DonorRegistration::where('user_id', auth()->id())
            ->where('donor_schedule_id', $schedule->id)
            ->whereIn('registration_status', ['pending', 'approved'])
            ->exists();

        if ($alreadyRegistered) {
            return back()->with('error', 'Anda sudah mendaftar pada jadwal ini.');
        }

        DonorRegistration::create([
            'user_id' => auth()->id(),
            'donor_schedule_id' => $schedule->id,
            'registration_status' => 'pending',
            'payment_status' => 'pending',
        ]);

        return redirect()->route('donor.registrations.index')
            ->with('success', 'Pendaftaran jadwal berhasil dikirim dan menunggu verifikasi admin.');
    }

    public function paymentForm(DonorRegistration $registration)
    {
        $this->authorizeDonor();
        abort_unless($registration->user_id === auth()->id(), 403);

        if ($registration->registration_status !== 'approved') {
            return redirect()->route('donor.registrations.index')
                ->with('error', 'Pembayaran dapat dikonfirmasi setelah pendaftaran disetujui admin.');
        }

        if ($registration->payment_status === 'approved') {
            return redirect()->route('donor.registrations.index')
                ->with('success', 'Pembayaran Anda sudah diverifikasi admin.');
        }

        if ($registration->payment_proof && $registration->payment_status === 'pending') {
            return redirect()->route('donor.registrations.index')
                ->with('success', 'Bukti pembayaran sedang menunggu verifikasi admin.');
        }

        if (!$registration->payment_expires_at || $registration->payment_expires_at->isPast()) {
            $registration->payment_reference = '880' . str_pad((string) $registration->id, 10, '0', STR_PAD_LEFT);
            $registration->payment_expires_at = now()->addMinutes(15);
            $registration->save();
        }

        return view('donor.registrations.payment', compact('registration'));
    }

    public function paymentStore(Request $request, DonorRegistration $registration)
    {
        $this->authorizeDonor();
        abort_unless($registration->user_id === auth()->id(), 403);

        if ($registration->registration_status !== 'approved') {
            return back()->with('error', 'Pendaftaran belum disetujui admin.');
        }

        $data = $request->validate([
            'payment_method' => ['required', 'in:virtual_account,qris'],
            'payment_proof' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if (!$registration->payment_expires_at || $registration->payment_expires_at->isPast()) {
            return back()->with('error', 'Kode pembayaran telah kedaluwarsa. Muat ulang halaman untuk membuat kode baru.');
        }

        if ($registration->payment_status === 'approved') {
            return back()->with('error', 'Pembayaran sudah diverifikasi admin.');
        }

        if ($registration->payment_proof && $registration->payment_status === 'pending') {
            return back()->with('error', 'Bukti pembayaran sedang menunggu verifikasi admin.');
        }

        if ($registration->payment_proof) {
            Storage::disk('public')->delete($registration->payment_proof);
        }

        $registration->payment_proof = $data['payment_proof']->store('payment-proofs', 'public');
        $registration->payment_method = $data['payment_method'];
        $registration->payment_status = 'pending';
        $registration->save();

        return redirect()->route('donor.registrations.index')
            ->with('success', 'Bukti pembayaran berhasil dikirim untuk diperiksa admin.');
    }

    public function adminIndex()
    {
        $this->authorizeAdmin();

        $query = DonorRegistration::with(['user', 'schedule']);

        if (request()->filled('search')) {
            $search = trim(request('search'));
            $query->whereHas('user', function ($userQuery) use ($search) {
                $userQuery->where('name', 'like', '%' . $search . '%');
            });
        }

        if (request()->filled('date')) {
            $date = request('date');
            $query->whereHas('user', function ($userQuery) use ($date) {
                $userQuery->whereDate('created_at', $date);
            });
        }

        $registrations = $query->latest()->paginate(10)->withQueryString();

        return view('admin.registrations.index', compact('registrations'));
    }

    public function adminUpdateStatus(Request $request, DonorRegistration $registration)
    {
        $this->authorizeAdmin();

        $data = $request->validate([
            'registration_status' => ['required', 'in:approved,rejected'],
        ]);

        $registration->registration_status = $data['registration_status'];
        $registration->save();

        $message = $data['registration_status'] === 'approved'
            ? 'Pendaftaran jadwal ' . ($registration->schedule?->title ?? '') . ' disetujui. Silakan lanjutkan pembayaran.'
            : 'Pendaftaran jadwal ' . ($registration->schedule?->title ?? '') . ' ditolak.';

        DonorNotification::create([
            'user_id' => $registration->user_id,
            'title' => $data['registration_status'] === 'approved' ? 'Pendaftaran disetujui' : 'Pendaftaran ditolak',
            'message' => $message,
        ]);

        return back()->with('success', 'Status pendaftaran berhasil diperbarui.');
    }

    public function adminUpdatePayment(Request $request, DonorRegistration $registration)
    {
        $this->authorizeAdmin();
        abort_unless($registration->payment_proof, 404);

        $data = $request->validate([
            'payment_status' => ['required', 'in:approved,rejected'],
        ]);

        $registration->payment_status = $data['payment_status'];

        if ($data['payment_status'] === 'approved') {
            $registration->registration_status = 'approved';
        }

        $registration->save();

        $notificationTitle = 'Status pembayaran diperbarui';
        $notificationMessage = 'Pembayaran pendaftaran ' . ($registration->schedule?->title ?? '') . ' telah ditolak. Silakan periksa bukti pembayaran dan kirim ulang.';
        if ($data['payment_status'] === 'approved') {
            $notificationTitle = 'Pendaftaran donor telah dikonfirmasi';
            $notificationMessage = 'Pembayaran Anda telah diverifikasi. Pendaftaran donor ' . ($registration->schedule?->title ?? '') . ' resmi terdaftar.';
        }

        DonorNotification::create([
            'user_id' => $registration->user_id,
            'title' => $notificationTitle,
            'message' => $notificationMessage,
        ]);

        return back()->with('success', 'Status pembayaran berhasil diperbarui.');
    }
}
