<?php

namespace App\Http\Middleware;

use App\Models\ApiClient;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidateApiToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Token tidak ditemukan.'], 401);
        }

        $client = ApiClient::where('token', $token)
            ->where('status_aktif', true)
            ->first();

        if (! $client) {
            return response()->json(['message' => 'Token tidak valid atau tidak aktif.'], 401);
        }

        $client->update(['last_used_at' => now()]);

        $request->attributes->set('api_client', $client);

        return $next($request);
    }
}