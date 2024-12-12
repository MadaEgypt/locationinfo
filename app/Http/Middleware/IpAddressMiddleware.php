<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IpAddressMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $this->getRealIp($request);
        $request->merge(['real_ip' => $ip]);
        return $next($request);
    }

    private function getRealIp(Request $request)
    {
        // محاولة الحصول على IP من الـ headers
        $ip = $request->header('X-Real-IP');
        if (!empty($ip)) {
            return $ip;
        }

        $ip = $request->header('X-Forwarded-For');
        if (!empty($ip)) {
            $ips = explode(',', $ip);
            return trim($ips[0]);
        }

        // إذا كان localhost
        if ($request->ip() == '127.0.0.1' || $request->ip() == '::1') {
            try {
                $response = Http::get('https://api.ipify.org?format=json');
                if ($response->successful()) {
                    return $response->json()['ip'];
                }
            } catch (\Exception $e) {
                Log::error('Error getting public IP: ' . $e->getMessage());
            }
        }

        return $request->ip();
    }
}
