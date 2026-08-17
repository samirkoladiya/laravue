<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class AdminVerifyOtpRequest extends FormRequest
{
    /**
     * Max failed attempts allowed per email+IP before locking out. Kept
     * tight since a 4-digit code only has 10,000 possible values.
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Must match AdminAuthController::OTP_TTL_MINUTES - the lifetime of
     * the code stored in password_reset_tokens.
     */
    private const OTP_TTL_MINUTES = 10;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'otp' => ['required', 'digits:4'],
        ];
    }

    /**
     * Verify the submitted code against the hash stored for the email held
     * in session, enforcing expiry and a per-email+IP attempt lockout.
     *
     * @throws ValidationException
     */
    public function verify(): void
    {
        $this->ensureIsNotRateLimited();

        $email = $this->session()->get('otp_email');
        $record = $email ? DB::table('password_reset_tokens')->where('email', $email)->first() : null;

        $expired = ! $record
            || Carbon::parse($record->created_at)->addMinutes(self::OTP_TTL_MINUTES)->isPast();

        if ($expired || ! Hash::check($this->string('otp'), $record->token)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'otp' => $expired
                    ? 'This code has expired. Please request a new one.'
                    : 'The code you entered is incorrect.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());

        $this->session()->put('otp_verified', true);
    }

    /**
     * @throws ValidationException
     */
    private function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), self::MAX_ATTEMPTS)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'otp' => "Too many attempts. Please try again in {$seconds} seconds.",
        ]);
    }

    private function throttleKey(): string
    {
        return 'otp-verify|'.$this->session()->get('otp_email').'|'.$this->ip();
    }
}
