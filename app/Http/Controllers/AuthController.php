<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use PragmaRX\Google2FAQRCode\Google2FA;

class AuthController extends Controller
{
    public function loginForm(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $request->validate([
            'login' => 'required',
            'password' => 'required',
        ]);

        $login = $request->login;

        if (filter_var($login, FILTER_VALIDATE_EMAIL)) {
            $field = 'email';
            $value = $login;
        } else {
            $field = 'name';
            $value = strtoupper($login);
        }

        if (Auth::attempt([$field => $value, 'password' => $request->password], $request->boolean('remember'))) {
            $user = Auth::user();

            if ($user->role !== 'superadmin') {
                Auth::logout();

                $request->session()->put('captcha:user_id', $user->id);
                $request->session()->put('captcha:remember', $request->boolean('remember'));

                return redirect()->route('login.captcha');
            }

            if ($user->role === 'superadmin' && $user->hasTwoFactorEnabled()) {
                Auth::logout();

                $request->session()->put('2fa:user_id', $user->id);
                $request->session()->put('2fa:remember', $request->boolean('remember'));

                return redirect()->route('login.2fa');
            }

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang kembali!');
        }

        return back()->withErrors(['login' => 'Email/Nama atau password salah.'])->onlyInput('login');
    }

    public function captchaForm(): View|RedirectResponse
    {
        if (! session()->has('captcha:user_id')) {
            return redirect()->route('login');
        }

        $this->generateCaptcha();

        return view('auth.verify-captcha');
    }

    public function verifyCaptcha(Request $request): RedirectResponse
    {
        $request->validate([
            'captcha' => 'required|string|max:5',
        ]);

        $userId = session('captcha:user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $expected = $request->session()->pull('captcha_answer');

        if (! $expected || strtoupper($request->captcha) !== $expected) {
            $this->generateCaptcha();

            return back()->withErrors(['captcha' => 'Kode verifikasi salah.']);
        }

        $user = User::find($userId);

        if (! $user) {
            session()->forget(['captcha:user_id', 'captcha:remember']);

            return redirect()->route('login');
        }

        Auth::login($user, session('captcha:remember', false));
        session()->forget(['captcha:user_id', 'captcha:remember']);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang kembali!');
    }

    public function verify2faForm(): View|RedirectResponse
    {
        if (! session()->has('2fa:user_id')) {
            return redirect()->route('login');
        }

        return view('auth.verify-2fa');
    }

    public function verify2fa(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => 'required|string|size:6',
        ]);

        $userId = session('2fa:user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (! $user || ! $user->hasTwoFactorEnabled()) {
            session()->forget(['2fa:user_id', '2fa:remember']);

            return redirect()->route('login');
        }

        $google2fa = new Google2FA;
        $valid = $google2fa->verifyKey(decrypt($user->two_factor_secret), $request->code);

        if (! $valid) {
            return back()->withErrors(['code' => 'Kode tidak valid. Silakan coba lagi.']);
        }

        Auth::login($user, session('2fa:remember', false));
        session()->forget(['2fa:user_id', '2fa:remember']);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'))->with('success', 'Selamat datang kembali!');
    }

    public function forgotPasswordForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function resetPasswordForm(string $token): View
    {
        return view('auth.reset-password', ['token' => $token, 'email' => request('email')]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill(['password' => $password])->save();
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('login')->with('success', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil logout.');
    }

    private function generateCaptcha(): void
    {
        // Excludes 0/O, 1/I/L and other easily-confused characters.
        $chars = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

        $code = '';
        for ($i = 0; $i < 5; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }

        session()->put('captcha_answer', $code);
        session()->put('captcha_question', $code);
    }
}
