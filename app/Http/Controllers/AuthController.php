<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Announcement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'nomor_telepon' => ['required', 'string', 'max:20'],
            'nik' => ['required', 'digits:16', 'unique:users,nik'],
            'jenis_kelamin' => ['required', 'in:Laki-laki,Perempuan'],
            'golongan_darah' => ['required', 'in:A+,A-,B+,B-,AB+,AB-,O+,O-'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'alamat' => ['required', 'string', 'max:1000'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);

        $user = new User();
        $user->name = $data['name'];
        $user->nomor_telepon = $data['nomor_telepon'];
        $user->nik = $data['nik'];
        $user->jenis_kelamin = $data['jenis_kelamin'];
        $user->golongan_darah = $data['golongan_darah'];
        $user->tanggal_lahir = $data['tanggal_lahir'];
        $user->alamat = $data['alamat'];
        $user->email = $data['email'];
        $user->password = Hash::make($data['password']);
        $user->role = 'donor';
        $user->account_status = 'pending';
        $user->save();

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('dashboard')
            ->with('success', 'Registrasi berhasil. Akun Anda menunggu verifikasi admin.');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email atau kata sandi tidak sesuai.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $destination = Auth::user()->role === 'admin'
            ? route('admin.dashboard')
            : route('dashboard');

        return redirect($destination)->with('success', 'Login berhasil.');
    }

    public function dashboard()
    {
        if (Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $user = Auth::user();
        $registrations = $user->donorRegistrations()->with('schedule')->latest()->take(5)->get();
        $totalRegistrations = $user->donorRegistrations()->count();
        $pendingRegistrations = $user->donorRegistrations()->where('registration_status', 'pending')->count();
        $pendingPayments = $user->donorRegistrations()
            ->whereNotNull('payment_proof')
            ->where('payment_status', 'pending')
            ->count();
        $announcements = Announcement::latest()->take(4)->get();

        return view('dashboard', compact(
            'user', 'registrations', 'totalRegistrations',
            'pendingRegistrations', 'pendingPayments', 'announcements'
        ));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Anda berhasil logout.');
    }
}
