<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminForgotPasswordRequest;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Http\Requests\Admin\AdminResetPasswordRequest;
use App\Http\Requests\Admin\AdminSignupRequest;
use App\Http\Requests\Admin\AdminVerifyOtpRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class AdminAuthController extends Controller
{
    /**
     * How long an issued OTP stays valid. Must match
     * AdminVerifyOtpRequest::OTP_TTL_MINUTES.
     */
    private const OTP_TTL_MINUTES = 10;

    /**
     * Minimum gap enforced between "resend code" requests for the same
     * session, so the resend action can't be used to spam an inbox.
     */
    private const RESEND_COOLDOWN_SECONDS = 60;

    public function login(): Response
    {
        return Inertia::render('Admin/Login');
    }

    public function loginPost(AdminLoginRequest $request): SymfonyResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // The login page renders under the bare "guest" root template, while
        // the dashboard renders under the "admin" one (see
        // HandleInertiaRequests::rootView()). A normal Inertia redirect only
        // swaps the SPA's content and never re-fetches the document <head>,
        // so the admin layout's CSS/JS never loads. Inertia::location()
        // forces a real full-page browser visit instead.
        return Inertia::location(
            $request->session()->pull('url.intended', route('admin.dashboard'))
        );
    }

    public function signup(): Response
    {
        return Inertia::render('Admin/Signup');
    }

    public function signupPost(AdminSignupRequest $request): SymfonyResponse
    {
        $user = User::create([
            'name' => $request->string('name'),
            'email' => $request->string('email'),
            // The User model casts `password` as `hashed`, so this is
            // hashed automatically on assignment.
            'password' => $request->string('password'),
        ]);

        event(new Registered($user));

        Auth::login($user);

        $request->session()->regenerate();

        // See the comment in loginPost() — this crosses the same
        // guest/admin layout boundary and needs a full page visit.
        return Inertia::location(route('admin.dashboard'));
    }

    public function logout(Request $request): SymfonyResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // See the comment in loginPost() — logging out crosses back over
        // the admin/guest layout boundary and needs a full page visit.
        return Inertia::location(route('admin.login.index'));
    }

    public function forgorpassword(): Response
    {
        return Inertia::render('Admin/ForgotPassword');
    }

    public function forgorPasswordPost(AdminForgotPasswordRequest $request): RedirectResponse
    {
        $this->issueOtp($request, $request->string('email')->toString());

        return redirect()->route('admin.verifyotp.index');
    }

    public function verifyotp(Request $request): Response|RedirectResponse
    {
        if (! $request->session()->has('otp_email')) {
            return redirect()->route('admin.forgotpassword.index');
        }

        return Inertia::render('Admin/VerifyOtp', [
            'maskedEmail' => $this->maskEmail($request->session()->get('otp_email')),
            'expiresAt' => $request->session()->get('otp_expires_at'),
        ]);
    }

    public function verifyotpPost(AdminVerifyOtpRequest $request): RedirectResponse
    {
        $request->verify();

        return redirect()->route('admin.resetpassword.index');
    }

    public function verifyotpResend(Request $request): RedirectResponse
    {
        $email = $request->session()->get('otp_email');

        if (! $email) {
            return redirect()->route('admin.forgotpassword.index');
        }

        $issuedAt = $request->session()->get('otp_issued_at');

        if ($issuedAt && now()->diffInSeconds(Carbon::parse($issuedAt)) < self::RESEND_COOLDOWN_SECONDS) {
            return back()->withErrors(['otp' => 'Please wait a moment before requesting another code.']);
        }

        $this->issueOtp($request, $email);

        return redirect()->route('admin.verifyotp.index')->with('success', 'A new code has been sent.');
    }

    public function resetpassword(Request $request): Response|RedirectResponse
    {
        if (! $this->hasVerifiedOtp($request)) {
            return redirect()->route('admin.forgotpassword.index');
        }

        return Inertia::render('Admin/ResetPassword');
    }

    public function resetPasswordPost(AdminResetPasswordRequest $request): RedirectResponse
    {
        if (! $this->hasVerifiedOtp($request)) {
            return redirect()->route('admin.forgotpassword.index');
        }

        $email = $request->session()->get('otp_email');
        $user = User::where('email', $email)->firstOrFail();

        $user->update([
            'password' => $request->string('password'),
            // Invalidate any "remember me" cookies elsewhere now that the
            // password has changed via an OTP-verified session.
            'remember_token' => Str::random(60),
        ]);

        DB::table('password_reset_tokens')->where('email', $email)->delete();

        $request->session()->forget(['otp_email', 'otp_expires_at', 'otp_issued_at', 'otp_verified']);

        return redirect()->route('admin.login.index')->with('success', 'Your password has been reset. Please sign in.');
    }

    private function hasVerifiedOtp(Request $request): bool
    {
        return $request->session()->get('otp_verified') === true
            && $request->session()->has('otp_email');
    }

    /**
     * Generate a fresh OTP, persist only its hash, email it to the user,
     * and stash the session state the verify/reset steps key off - the
     * email and code itself never touch the URL or get echoed back to
     * the client.
     */
    private function issueOtp(Request $request, string $email): void
    {
        $otp = app()->isProduction() ? (string) random_int(1000, 9999) : '1234';

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            ['token' => Hash::make($otp), 'created_at' => now()]
        );

        Mail::raw(
            "Your verification code is {$otp}. It expires in ".self::OTP_TTL_MINUTES.' minutes.',
            fn ($message) => $message->to($email)->subject('Your verification code')
        );

        $request->session()->put('otp_email', $email);
        $request->session()->put('otp_issued_at', now()->toIso8601String());
        $request->session()->put('otp_expires_at', now()->addMinutes(self::OTP_TTL_MINUTES)->toIso8601String());
        $request->session()->forget('otp_verified');
    }

    /**
     * Mask an email for display on the verify-otp screen, e.g.
     *
     * "jane.doe@example.com" -> "j***@example.com".
     */
    private function maskEmail(string $email): string
    {
        [$name, $domain] = explode('@', $email, 2);
        $visible = mb_substr($name, 0, 1);

        return $visible.str_repeat('*', max(mb_strlen($name) - 1, 3)).'@'.$domain;
    }
}
