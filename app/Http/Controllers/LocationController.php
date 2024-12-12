<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Exception;

class LocationController extends Controller
{
    public function index()
    {
        return view('location');
    }

    public function getLocation(Request $request)
    {
        try {
            // First attempt with ipapi.co
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ])->get('https://ipapi.co/json/');

            if ($response->successful()) {
                $data = $response->json();
                
                if (!isset($data['error'])) {
                    $locationData = $this->formatLocationResponse($data, 'ipapi');
                    
                    // Get timezone data
                    $timezoneData = $this->getTimezoneData($locationData['latitude'], $locationData['longitude']);
                    
                    return response()->json(array_merge($locationData, $timezoneData));
                }
            }

            // Fallback to ip-api.com
            $fallbackResponse = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
            ])->get('http://ip-api.com/json/');

            if ($fallbackResponse->successful()) {
                $locationData = $this->formatLocationResponse($fallbackResponse->json(), 'ip-api');
                
                // Get timezone data
                $timezoneData = $this->getTimezoneData($locationData['latitude'], $locationData['longitude']);
                
                return response()->json(array_merge($locationData, $timezoneData));
            }

            return response()->json([
                'error' => 'Unable to fetch location data'
            ], 500);

        } catch (Exception $e) {
            return response()->json([
                'error' => 'Error fetching location: ' . $e->getMessage()
            ], 500);
        }
    }

    private function formatLocationResponse($data, $source)
    {
        if ($source === 'ipapi') {
            return [
                'ip' => $data['ip'],
                'city' => $data['city'],
                'country' => $data['country_name'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'timezone' => $data['timezone']
            ];
        }

        // Format for ip-api.com
        return [
            'ip' => $data['query'],
            'city' => $data['city'],
            'country' => $data['country'],
            'latitude' => $data['lat'],
            'longitude' => $data['lon'],
            'timezone' => $data['timezone']
        ];
    }

    private function getTimezoneData($lat, $lon)
    {
        try {
            $response = Http::get('http://api.timezonedb.com/v2.1/get-time-zone', [
                'key' => env('TIMEZONE_API_KEY', 'SLNKC41Y8ZQN'),
                'format' => 'json',
                'by' => 'position',
                'lat' => $lat,
                'lng' => $lon
            ]);

            if ($response->successful()) {
                $data = $response->json();
                return [
                    'localTime' => $data['formatted'],
                    'zoneName' => $data['zoneName']
                ];
            }

            return [
                'localTime' => null,
                'zoneName' => null
            ];

        } catch (Exception $e) {
            return [
                'localTime' => null,
                'zoneName' => null
            ];
        }
    }
}
