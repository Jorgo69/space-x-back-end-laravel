<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SpaceXService
{
    protected const LAUNCHES_URL = 'https://api.spacexdata.com/v5/launches';
    protected const ROCKETS_URL = 'https://api.spacexdata.com/v4/rockets/';
    protected const LAUNCHPADS_URL = 'https://api.spacexdata.com/v4/launchpads/';

    protected const CACHE_KEY = 'spacex_launches';
    protected const CACHE_TTL_MINUTES = 1440; // 24h

    /**
     * Récupère tous les lancements (depuis cache ou API)
     */
    public function getAllLaunches(bool $forceRefresh = false): array
    {
        if ($forceRefresh) {
            Cache::forget(self::CACHE_KEY);
            Log::info('Cache SpaceX vidé – resynchronisation demandée');
        }

        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL_MINUTES, function () {
            Log::info('Appel à l’API SpaceX v5 pour récupérer les lancements...');
            $response = Http::timeout(30)->get(self::LAUNCHES_URL);

            if (!$response->successful()) {
                Log::error('Échec de l’appel à l’API SpaceX', ['status' => $response->status()]);
                return [];
            }

            $launches = $response->json();
            Log::info('Données SpaceX récupérées avec succès', ['count' => count($launches)]);
            return $launches;
        });
    }

    /**
     * Récupère une fusée par ID (v4)
     */
    public function getRocket(string $id): ?array
    {
        $cacheKey = "rocket_{$id}";
        return Cache::remember($cacheKey, 1440, function () use ($id) {
            $response = Http::get(self::ROCKETS_URL . $id);
            return $response->successful() ? $response->json() : null;
        });
    }

    /**
     * Récupère un launchpad par ID (v4)
     */
    public function getLaunchpad(string $id): ?array
    {
        $cacheKey = "launchpad_{$id}";
        return Cache::remember($cacheKey, 1440, function () use ($id) {
            $response = Http::get(self::LAUNCHPADS_URL . $id);
            return $response->successful() ? $response->json() : null;
        });
    }
}