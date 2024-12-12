<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Services\CityImageService;

// تعيين headers عامة لجميع الاستجابات
Route::middleware(['web'])->group(function () {
    Route::get('/', function () {
        return view('location');
    });

    Route::get('/get-location', function () {
        try {
            // الحصول على IP الحقيقي للمستخدم
            $ip = null;
            
            // 1. محاولة الحصول على IP من Cloudflare
            if (request()->header('HTTP_CF_CONNECTING_IP')) {
                $ip = request()->header('HTTP_CF_CONNECTING_IP');
            }
            // 2. محاولة الحصول على IP من خلال proxy
            elseif (request()->header('HTTP_X_FORWARDED_FOR')) {
                // أخذ أول IP في القائمة (IP الأصلي)
                $ip = explode(',', request()->header('HTTP_X_FORWARDED_FOR'))[0];
            }
            // 3. محاولة الحصول على IP الحقيقي
            elseif (request()->header('HTTP_X_REAL_IP')) {
                $ip = request()->header('HTTP_X_REAL_IP');
            }
            // 4. استخدام IP الطلب المباشر
            else {
                $ip = request()->ip();
            }

            // التحقق من صحة IP
            if (!filter_var($ip, FILTER_VALIDATE_IP)) {
                throw new \Exception('Invalid IP address');
            }

            // محاولة الحصول على معلومات الموقع من ipapi.co
            $response = Http::timeout(5)->get("https://ipapi.co/{$ip}/json/");
            
            if ($response->successful()) {
                $data = $response->json();
                
                // التحقق من وجود رسالة خطأ من API
                if (isset($data['error']) && $data['error'] === true) {
                    throw new \Exception('IP API returned an error');
                }

                return response()->json([
                    'ip' => $ip,
                    'city' => $data['city'] ?? 'Cairo',
                    'region' => $data['region'] ?? 'Cairo Governorate',
                    'country' => $data['country_name'] ?? 'Egypt',
                    'latitude' => $data['latitude'] ?? 30.0588,
                    'longitude' => $data['longitude'] ?? 31.2268,
                    'timezone' => $data['timezone'] ?? 'Africa/Cairo'
                ])->header('Content-Type', 'application/json')
                  ->header('X-Content-Type-Options', 'nosniff');
            }

            throw new \Exception('Failed to fetch location data');
        } catch (\Exception $e) {
            \Log::error('Location detection error: ' . $e->getMessage() . ' for IP: ' . ($ip ?? 'unknown'));
            
            // إرجاع بيانات افتراضية مع تسجيل الخطأ
            return response()->json([
                'ip' => $ip ?? request()->ip(),
                'city' => 'Cairo',
                'region' => 'Cairo Governorate',
                'country' => 'Egypt',
                'latitude' => 30.0588,
                'longitude' => 31.2268,
                'timezone' => 'Africa/Cairo'
            ])->header('Content-Type', 'application/json')
              ->header('X-Content-Type-Options', 'nosniff');
        }
    });

    Route::get('/city-image/{city}', function ($city) {
        $cityImageService = new CityImageService();
        $imageUrl = $cityImageService->getCityImage($city);
        return response()->json(['url' => $imageUrl]);
    });
});
