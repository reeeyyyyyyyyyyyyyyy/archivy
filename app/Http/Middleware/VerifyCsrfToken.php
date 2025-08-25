<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        // Only exclude Telegram webhook and specific API endpoints that need it
        'telegram/webhook',
        'api/telegram/*',
        'api/auth/login-test'
    ];
}
