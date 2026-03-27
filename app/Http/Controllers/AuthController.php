<?php

namespace App\Http\Controllers;

use App\Mail\VerifyOtpMail;
use App\Mail\WelcomeNutritionistMail;
use App\Models\EmailVerificationOtp;
use App\Models\NutritionistProfile;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class AuthController extends Controller
{
    private const OTP_EXPIRY_MINUTES = 10;

    public function showLogin(): Response
    {
        return Inertia::render('Login');
    }

    public function showRegister(): Response
    {
        return Inertia::render('Register');
    }

    public function showVerifyOtp(Request $request): Response
    {
        $email = (string) $request->query('email', '');

        return Inertia::render('VerifyOtp', [
            'email' => $email,
            'expiryMinutes' => self::OTP_EXPIRY_MINUTES,
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if ($user && ! $user->is_active) {
            return back()->withErrors([
                'general' => 'Tu cuenta está desactivada. Contacta al administrador.',
            ]);
        }

        if ($user && is_null($user->email_verified_at)) {
            return back()->withErrors([
                'general' => 'Debes verificar tu correo antes de iniciar sesión.',
            ]);
        }

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'general' => 'Credenciales incorrectas. Verifica tu correo y contraseña.',
            ]);
        }

        $request->session()->regenerate();

        $authUser = Auth::user();
        if ($authUser instanceof User) {
            $authUser->update(['last_login_at' => now()]);
        }

        return redirect()->intended('/');
    }

    public function register(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name'              => ['required', 'string', 'max:150'],
            'email'             => ['required', 'email', 'max:255', 'unique:users,email'],
            'professional_info' => ['required', 'string', 'max:150'],
            'password'          => ['required', 'string', 'min:8', 'confirmed'],
            'terms'             => ['accepted'],
        ], [
            'terms.accepted' => 'Debes aceptar los términos para crear tu cuenta.',
        ]);

        $parts = preg_split('/\s*\/\s*/', trim($data['professional_info']), 2);
        $specialization = null;
        $licenseNumber = trim($parts[0] ?? '');

        if (count($parts) === 2) {
            $specialization = trim($parts[0]) !== '' ? trim($parts[0]) : null;
            $licenseNumber = trim($parts[1]);
        }

        if ($licenseNumber === '') {
            return back()->withErrors([
                'professional_info' => 'Ingresa tu cédula profesional (puedes usar formato: Especialidad / Cédula).',
            ])->withInput();
        }

        $licenseExists = NutritionistProfile::where('license_number', $licenseNumber)->exists();
        if ($licenseExists) {
            return back()->withErrors([
                'professional_info' => 'La cédula profesional ya está registrada.',
            ])->withInput();
        }

        $nameParts = explode(' ', trim($data['name']), 2);
        $firstName = $nameParts[0];
        $lastName = $nameParts[1] ?? '-';

        $user = DB::transaction(function () use ($data, $firstName, $lastName, $specialization, $licenseNumber) {
            $user = User::create([
                'first_name' => $firstName,
                'last_name'  => $lastName,
                'email'      => $data['email'],
                'password'   => Hash::make($data['password']),
                'role_key'   => 'nutritionist',
                'is_active'  => true,
            ]);

            NutritionistProfile::create([
                'user_id'        => $user->id,
                'license_number' => $licenseNumber,
                'specialization' => $specialization,
            ]);

            return $user;
        });

        $otpSent = $this->issueAndSendOtp($user);

        if (! $otpSent) {
            return redirect()->route('register.verify', ['email' => $user->email])
                ->with('warning', 'Cuenta creada, pero no se pudo enviar el OTP. Revisa tu configuración SMTP y reenvía el código.');
        }

        return redirect()->route('register.verify', ['email' => $user->email])
            ->with('success', 'Cuenta creada. Te enviamos un código OTP para verificar tu correo.');
    }

    public function verifyOtp(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp' => ['required', 'regex:/^\d{6}$/'],
        ], [
            'otp.regex' => 'El OTP debe tener exactamente 6 dígitos.',
        ]);

        $user = User::where('email', $data['email'])->firstOrFail();

        if (! is_null($user->email_verified_at)) {
            return redirect()->route('login')->with('success', 'Tu correo ya estaba verificado. Ya puedes iniciar sesión.');
        }

        $otpRow = EmailVerificationOtp::where('user_id', $user->id)
            ->whereNull('used_at')
            ->latest('id')
            ->first();

        if (! $otpRow) {
            return back()->withErrors([
                'otp' => 'No hay un código activo. Solicita un reenvío.',
            ])->withInput();
        }

        if ($otpRow->expires_at->isPast()) {
            return back()->withErrors([
                'otp' => 'El código OTP expiró. Solicita uno nuevo.',
            ])->withInput();
        }

        if (! Hash::check($data['otp'], $otpRow->code_hash)) {
            return back()->withErrors([
                'otp' => 'El código OTP es incorrecto.',
            ])->withInput();
        }

        DB::transaction(function () use ($user, $otpRow) {
            $otpRow->update(['used_at' => now()]);
            $user->forceFill(['email_verified_at' => now()])->save();
        });

        try {
            Mail::to($user->email)->send(new WelcomeNutritionistMail($user));
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('login')->with('success', 'Correo verificado correctamente. Ya puedes iniciar sesión.');
    }

    public function resendOtp(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ]);

        $user = User::where('email', $data['email'])->firstOrFail();

        if (! is_null($user->email_verified_at)) {
            return redirect()->route('login')->with('success', 'Tu correo ya estaba verificado.');
        }

        $recentOtp = EmailVerificationOtp::where('user_id', $user->id)
            ->latest('id')
            ->first();

        if ($recentOtp && $recentOtp->created_at && $recentOtp->created_at->gt(now()->subMinute())) {
            return back()->withErrors([
                'otp' => 'Espera al menos 60 segundos antes de reenviar otro código.',
            ])->withInput();
        }

        $otpSent = $this->issueAndSendOtp($user);

        if (! $otpSent) {
            return back()->with('warning', 'No se pudo reenviar el OTP. Revisa la configuración SMTP e inténtalo de nuevo.');
        }

        return back()->with('success', 'Te enviamos un nuevo código OTP.');
    }

    private function issueAndSendOtp(User $user): bool
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        DB::transaction(function () use ($user, $otp) {
            EmailVerificationOtp::where('user_id', $user->id)
                ->whereNull('used_at')
                ->update(['used_at' => now()]);

            EmailVerificationOtp::create([
                'user_id' => $user->id,
                'code_hash' => Hash::make($otp),
                'expires_at' => now()->addMinutes(self::OTP_EXPIRY_MINUTES),
            ]);
        });

        try {
            Mail::to($user->email)->send(new VerifyOtpMail($user, $otp));
            return true;
        } catch (\Throwable $e) {
            report($e);
            return false;
        }
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
