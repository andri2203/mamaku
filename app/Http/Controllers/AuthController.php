<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\EmailService; // ⬅️ tambahkan ini
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;
    }

    // Halaman login
    public function login()
    {
        return view('login');
    }

    // Halaman verifikasi email
    public function verify_email()
    {
        return view('verify-email');
    }

    // Halaman Password Request
    public function password_request()
    {
        return view('forgot-password');
    }

    // Handle login post
    public function login_post(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
            'remember' => 'sometimes|in:on',
        ]);

        $remember = $request->has('remember') && $credentials['remember'] === 'on';
        unset($credentials['remember']);

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Cek apakah email belum diverifikasi
            if (is_null(Auth::user()->email_verified_at)) {
                // Kirim kode verifikasi 6 digit
                $code = $this->emailService->sendVerificationCode(Auth::user()->email);

                // Simpan email user di session agar halaman verifikasi tahu targetnya
                session(['pending_verification_email' => Auth::user()->email]);

                // Logout sementara agar user tidak fully login
                Auth::logout();

                return redirect('/verify-email')
                    ->with('info', 'Kami telah mengirimkan kode verifikasi ke email Anda.');
            }

            if (!is_null(Auth::user()->two_factor_secret)) {
                return redirect()->intended('/two_factor_auth')
                    ->with('success', 'Login berhasil! Silahkan masukkan kode keamanan anda.');
            } else {
                return redirect()->intended('/')
                    ->with('success', 'Login berhasil!');
            }
        }

        return back()->withInput()->with('error', 'Email atau Password salah');
    }

    public function two_factor_auth()
    {
        return view('two_factor_auth');
    }

    public function two_factor_process(Request $request)
    {
        $validated = $request->validate([
            'verification_code' => 'required|digits:6',
        ]);

        if (Auth::user()->two_factor_secret != $validated['verification_code']) {
            return back()->withErrors(['verification_code' => 'Kode Keamanan tidak cocok']);
        }

        return redirect()->intended('/')
            ->with('success', 'Login berhasil!');
    }

    public function verify_email_post(Request $request)
    {
        $request->validate([
            'verification_code' => 'required|digits:6',
        ]);

        $sessionCode = session('verification_code');
        $email = session('pending_verification_email');
        $expiresAt = session('verification_expires_at');

        if (!$sessionCode || !$email) {
            return redirect('/login')->with('error', 'Kode verifikasi tidak ditemukan.');
        }

        if (now()->greaterThan($expiresAt)) {
            session()->forget(['verification_code', 'verification_email', 'verification_expires_at', 'pending_verification_email']);
            return back()->with('error', 'Kode verifikasi telah kedaluwarsa.');
        }

        if ($request->verification_code != $sessionCode) {
            return back()->with('error', 'Kode verifikasi salah.');
        }

        // Update email_verified_at
        $user = User::where('email', $email)->first();
        if ($user) {
            $user->email_verified_at = now();
            $user->save();
        }

        // Hapus session verifikasi
        session()->forget(['verification_code', 'verification_email', 'verification_expires_at', 'pending_verification_email']);

        // Auto login setelah berhasil verifikasi
        Auth::login($user);

        return redirect('/')->with('success', 'Email berhasil diverifikasi!');
    }

    public function resend_verification_code()
    {
        $email = session('pending_verification_email');

        if (!$email) {
            return redirect('/login')->with('error', 'Tidak ada email untuk diverifikasi.');
        }

        $this->emailService->sendVerificationCode($email);

        return back()->with('info', 'Kode verifikasi baru telah dikirim ke email Anda.');
    }

    public function password_request_post(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            // Kirim kode verifikasi 6 digit jika two_factor_recovery_codes kosong
            if (is_null($user->email_verified_at) || empty($user->two_factor_recovery_codes)) {
                $code = $this->emailService->sendVerificationCode($user->email);

                session(['verification_code' => $code, 'user_id' => $user->id, 'reset_code_type' => 'email_send']);
            } else {
                session(['verification_code' => $user->two_factor_recovery_codes, 'user_id' => $user->id, 'reset_code_type' => 'two_factor_recovery_codes']);
            }

            return redirect()->route('password.reset')->with('info', 'Silahkan reset password anda');
        }

        return back()->with('error', 'Email tidak terdaftar');
    }

    public function password_reset()
    {
        $reset_code_type = session('reset_code_type');

        $data = [
            'reset_code_type' => $reset_code_type
        ];

        return view('reset-password', $data);
    }

    public function password_reset_post(Request $request)
    {
        $validated = $request->validate([
            'verification_code' => 'required|digits:6',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $sessionCode = session('verification_code');
        $user_id = session('user_id');
        $user = User::findOrFail($user_id);

        if (!$user) {
            return back()->with('error', 'Akun tidak ditemukan');
        }

        if ($validated['verification_code'] != $sessionCode) {
            return back()->withErrors(['verification_code' => 'Kode Verifikasi tidak cocok']);
        }

        try {
            session()->forget(['verification_code', 'user_id', 'reset_code_type']);

            $user->password = Hash::make($validated['password']);

            $user->save();

            return redirect()->route('login')->with('success', 'Password berhasil di reset. Silahkan login');
        } catch (\Throwable $th) {
            return back()->with('error', 'Terjadi Kesalahan : ' . $th->getMessage());
        }
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
