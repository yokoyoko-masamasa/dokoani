<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            // 失敗を数える。数える期間は最初の失敗から15分
            $failures = RateLimiter::hit($this->throttleKey(), 15 * 60);

            // 5回目の失敗で15分ロックし、回数は0から数え直す
            if ($failures >= 5) {
                RateLimiter::hit($this->lockoutKey(), 15 * 60);
                RateLimiter::clear($this->throttleKey());
            }

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        // ロック中なら、パスワードを確かめる前に断る
        // ロック専用キーは1回分あれば「ロック中」
        if (! RateLimiter::tooManyAttempts($this->lockoutKey(), 1)) {
            return;
        }

        event(new Lockout($this));

        // ロックが解けるまでの秒数（メッセージでは分に切り上げる）
        $seconds = RateLimiter::availableIn($this->lockoutKey());

        throw ValidationException::withMessages([
            'email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }

    /**
     * ロックの印に使うキー。失敗を数えるキーとは分ける
     */
    public function lockoutKey(): string
    {
        return 'login-lockout|'.$this->throttleKey();
    }
}
