<?php

namespace App\Http\Controllers;

use App\Services\SpaceXService;
use Illuminate\Http\JsonResponse;

class SyncController extends Controller
{
    public function resync(SpaceXService $spaceXService): JsonResponse
    {
        \Log::info('ADMIN a déclenché une resynchronisation manuelle');
        $launches = $spaceXService->getAllLaunches(true); // force refresh
        return response()->json([
            'message' => 'Synchronisation terminée',
            'launches_count' => count($launches),
        ]);
    }
}