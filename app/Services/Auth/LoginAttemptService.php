<?php

namespace App\Services\Auth;

use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;

class LoginAttemptService
{
    private const MAX_ATTEMPTS = 5;
    private const DECAY_MINUTES = 15; // in minutes

    public function __construct(private RateLimiter $rateLimiter) {}

    public function tooManyAttempts(Request $request): bool
    {
        return $this->rateLimiter->tooManyAttempts(
            $this->throttleKey($request),
            self::MAX_ATTEMPTS
        );
    }

    public function incrementAttempts(Request $request): void
    {
        $this->rateLimiter->hit(
            $this->throttleKey($request),
            self::DECAY_MINUTES * 60
        );
    }

    public function clearAttempts(Request $request): void
    {
        $this->rateLimiter->clear($this->throttleKey($request));
    }

    public function availableIn(Request $request): int
    {
        return $this->rateLimiter->availableIn($this->throttleKey($request));
    }

    public function remainingAttempts(Request $request): int
    {
        return $this->rateLimiter->retriesLeft(
            $this->throttleKey($request),
            self::MAX_ATTEMPTS
        );
    }

    private function throttleKey(Request $request): string
    {
        return 'login_' . strtolower($request->input('email')) . '_' . $request->ip();
    }
}
