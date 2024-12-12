<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Http;

class IpMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $realIp = null;

        // محاولة الحصول على IP من الهيدرز
        $headers = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];

        foreach ($headers as $header) {
            if ($request->server($header)) {
                $ips = explode(',', $request->server($header));
                $realIp = trim($ips[0]);
                break;
            }
        }

        // إذا لم نجد IP من الهيدرز، نحاول الحصول عليه من API خارجي
        if (!$realIp || $realIp === '127.0.0.1') {
            try {
                $response = Http::withoutProxy()->timeout(5)->get('https://api.ipify.org?format=json');
                if ($response->successful()) {
                    $realIp = $response->json()['ip'];
                }
            } catch (\Exception $e) {
                \Log::error("Error getting IP from ipify: " . $e->getMessage());
            }
        }

        // تخزين IP في الطلب
        $request->merge(['real_ip' => $realIp ?? 'Unknown']);

        return $next($request);
    }
}
