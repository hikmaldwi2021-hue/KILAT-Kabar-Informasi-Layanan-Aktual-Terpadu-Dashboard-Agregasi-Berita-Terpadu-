<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Paksa HTTPS hanya jika request memang datang
        // melalui reverse proxy seperti Ngrok.
        if (
            request()->header('x-forwarded-proto') === 'https'
        ) {
            URL::forceScheme('https');
        }

        RateLimiter::for('api-client', function ($request) {
            $client = $request->attributes->get('api_client');

            return Limit::perMinute(10)
                ->by($client?->id ?? $request->ip());
        });
    }
}

