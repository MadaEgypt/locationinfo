<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class IpService
{
    private $ipServices = [
        'https://api.ipify.org?format=json',
        'https://api64.ipify.org?format=json',
        'https://ipinfo.io/json',
        'https://api.myip.com',
        'https://ip.seeip.org/jsonip'
    ];

    public function getPublicIp()
    {
        foreach ($this->ipServices as $service) {
            try {
                $response = Http::withoutProxy()
                    ->timeout(3)
                    ->withHeaders([
                        'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                    ])
                    ->get($service);

                if ($response->successful()) {
                    $data = $response->json();
                    $ip = $data['ip'] ?? null;
                    
                    if ($ip && filter_var($ip, FILTER_VALIDATE_IP) && $ip !== '127.0.0.1') {
                        Log::info("Successfully got IP from {$service}: {$ip}");
                        return $ip;
                    }
                }
            } catch (\Exception $e) {
                Log::error("Error getting IP from {$service}: " . $e->getMessage());
                continue;
            }
        }

        return null;
    }

    public function getLocationData($ip)
    {
        try {
            $response = Http::withoutProxy()
                ->timeout(5)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                ])
                ->get("http://ip-api.com/json/{$ip}");

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'lat' => $data['lat'] ?? 0,
                    'lon' => $data['lon'] ?? 0,
                    'city' => $data['city'] ?? 'Unknown',
                    'country' => $data['country'] ?? 'Unknown',
                    'isp' => $data['isp'] ?? 'Unknown',
                    'org' => $data['org'] ?? 'Unknown'
                ];
            }
        } catch (\Exception $e) {
            Log::error("Error getting location data for IP {$ip}: " . $e->getMessage());
        }

        return null;
    }
}
