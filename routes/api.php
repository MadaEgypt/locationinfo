<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Services\CityImageService;

Route::get('/ip', function (Request $request) {
    try {
        $response = Http::get('http://ip-api.com/json/');
        
        if ($response->successful()) {
            $data = $response->json();
            return [
                'ip' => $request->ip(),
                'city' => $data['city'] ?? 'Cairo', // Default to Cairo if not found
                'country' => $data['country'] ?? 'Egypt',
                'latitude' => $data['lat'] ?? 30.0588,
                'longitude' => $data['lon'] ?? 31.2268,
                'timezone' => $data['timezone'] ?? 'Africa/Cairo'
            ];
        }

        // Fallback to default values if API fails
        return [
            'ip' => $request->ip(),
            'city' => 'Cairo',
            'country' => 'Egypt',
            'latitude' => 30.0588,
            'longitude' => 31.2268,
            'timezone' => 'Africa/Cairo'
        ];
    } catch (\Exception $e) {
        \Log::error('IP API error: ' . $e->getMessage());
        return [
            'ip' => $request->ip(),
            'city' => 'Cairo',
            'country' => 'Egypt',
            'latitude' => 30.0588,
            'longitude' => 31.2268,
            'timezone' => 'Africa/Cairo'
        ];
    }
});

Route::get('/city-image/{city}', function ($city, CityImageService $imageService) {
    return response()->json([
        'image_url' => $imageService->getCityImage($city)
    ]);
});
