<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class CityImageService
{
    private $pexelsKey = 'Bh4nXzIxjddxai34Xps1uzgo5dXANgfRNd1OY6Uc63o5JjZllhY1Vxjx';
    private $pixabayKey = '47549478-7394855896b9f3e4bb41186b0';
    private $defaultImage = '/images/default-city.jpg';

    public function getCityImage($cityName)
    {
        try {
            \Log::info('Fetching image for city: ' . $cityName);
            
            // Try Pexels first
            $pexelsImage = $this->fetchFromPexels($cityName);
            if ($pexelsImage) {
                \Log::info('Using Pexels image: ' . $pexelsImage);
                return $pexelsImage;
            }
            
            // Try Pixabay second
            $pixabayImage = $this->fetchFromPixabay($cityName);
            if ($pixabayImage) {
                \Log::info('Using Pixabay image: ' . $pixabayImage);
                return $pixabayImage;
            }
            
            // Try Unsplash last
            $unsplashImage = $this->fetchFromUnsplash($cityName);
            if ($unsplashImage) {
                \Log::info('Using Unsplash image: ' . $unsplashImage);
                return $unsplashImage;
            }

            \Log::warning('No image found for city: ' . $cityName);
            return $this->defaultImage;
        } catch (\Exception $e) {
            \Log::error('Error in getCityImage: ' . $e->getMessage());
            return $this->defaultImage;
        }
    }

    private function fetchFromPexels($cityName)
    {
        try {
            \Log::info('Trying Pexels API for: ' . $cityName);
            
            $response = Http::withHeaders([
                'Authorization' => $this->pexelsKey
            ])->get('https://api.pexels.com/v1/search', [
                'query' => $cityName . ' city skyline',
                'per_page' => 1,
                'orientation' => 'landscape',
                'size' => 'large'
            ]);

            \Log::info('Pexels API Response: ' . $response->body());

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['photos'])) {
                    $image = $data['photos'][0]['src']['landscape'];
                    \Log::info('Found Pexels image: ' . $image);
                    return $image;
                }
            }
            
            \Log::warning('No images found in Pexels response');
            return null;
        } catch (\Exception $e) {
            \Log::error('Pexels API error: ' . $e->getMessage());
            return null;
        }
    }

    private function fetchFromPixabay($cityName)
    {
        try {
            \Log::info('Trying Pixabay API for: ' . $cityName);
            
            $response = Http::get('https://pixabay.com/api/', [
                'key' => $this->pixabayKey,
                'q' => $cityName . ' city skyline',
                'per_page' => 3,
                'orientation' => 'horizontal',
                'image_type' => 'photo',
                'category' => 'places',
                'safesearch' => true
            ]);

            \Log::info('Pixabay API Response: ' . $response->body());

            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['hits'])) {
                    $image = $data['hits'][0]['largeImageURL'];
                    \Log::info('Found Pixabay image: ' . $image);
                    return $image;
                }
            }
            
            \Log::warning('No images found in Pixabay response');
            return null;
        } catch (\Exception $e) {
            \Log::error('Pixabay API error: ' . $e->getMessage());
            return null;
        }
    }

    private function fetchFromUnsplash($cityName)
    {
        try {
            \Log::info('Trying Unsplash for: ' . $cityName);
            
            $response = Http::withHeaders([
                'Accept-Version' => 'v1'
            ])->get("https://source.unsplash.com/1600x900/?" . urlencode($cityName . " city skyline"));
            
            if ($response->successful()) {
                $imageUrl = $response->effectiveUri();
                if ($imageUrl) {
                    \Log::info('Found Unsplash image: ' . $imageUrl);
                    return $imageUrl;
                }
            }
            
            \Log::warning('No image found from Unsplash');
            return null;
        } catch (\Exception $e) {
            \Log::error('Unsplash API error: ' . $e->getMessage());
            return null;
        }
    }
}
