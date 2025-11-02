<?php

namespace App\Http\Controllers;

use App\Services\SpaceXService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LaunchController extends Controller
{
    protected $spaceXService;

    public function __construct(SpaceXService $spaceXService)
    {
        $this->spaceXService = $spaceXService;
    }

    /**
     * Retourne les KPIs + stats pour le dashboard
     */
    public function dashboard()
    {
        Log::info('Demande du dashboard');
        $launches = $this->spaceXService->getAllLaunches();

        // KPIs
        $total = count($launches);
        $successCount = count(array_filter($launches, fn($l) => $l['success'] === true));
        $successRate = $total ? round(($successCount / $total) * 100, 2) : 0;

        // Prochain lancement
        $upcoming = collect($launches)->filter(fn($l) => !isset($l['date_utc']) || $l['date_utc'] > now()->toIso8601String())
            ->sortBy('date_utc')
            ->first();

        // Stats par année
        $byYear = collect($launches)->filter(fn($l) => isset($l['date_utc']))
            ->groupBy(fn($l) => substr($l['date_utc'], 0, 4))
            ->map(function ($yearLaunches, $year) {
                $total = $yearLaunches->count();
                $success = $yearLaunches->where('success', true)->count();
                return [
                    'year' => $year,
                    'total' => $total,
                    'success_rate' => $total ? round(($success / $total) * 100, 2) : 0,
                ];
            })
            ->values()
            ->sortBy('year');

        return response()->json([
            'kpi' => [
                'total_launches' => $total,
                'success_rate' => $successRate,
                'next_launch' => $upcoming ? [
                    'name' => $upcoming['name'],
                    'date_utc' => $upcoming['date_utc'],
                    'days_until' => isset($upcoming['date_utc']) ? now()->diffInDays($upcoming['date_utc'], false) : null,
                ] : null,
            ],
            'stats_by_year' => $byYear,
            'launches' => $launches, // Angular paginera/filtrera côté client ou via API paginée si besoin
        ]);
    }

    /**
     * Liste paginée + filtrable des lancements
     */
    public function list(Request $request)
    {
        $launches = collect($this->spaceXService->getAllLaunches());

        // Filtres
        if ($request->filled('year')) {
            $launches = $launches->filter(fn($l) => isset($l['date_utc']) && substr($l['date_utc'], 0, 4) == $request->year);
        }
        if ($request->filled('success')) {
            $success = filter_var($request->success, FILTER_VALIDATE_BOOLEAN);
            $launches = $launches->filter(fn($l) => ($l['success'] ?? false) === $success);
        }

        // Pagination manuelle (ou utiliser paginate si tu veux vrai paginate côté Laravel)
        $perPage = $request->get('per_page', 10);
        $page = $request->get('page', 1);
        $offset = ($page - 1) * $perPage;
        $paginated = $launches->slice($offset, $perPage)->values();

        return response()->json([
            'data' => $paginated,
            'total' => $launches->count(),
            'page' => $page,
            'per_page' => $perPage,
        ]);
    }

    /**
     * Détail d’un lancement
     */
    public function show(string $id)
    {
        $launches = $this->spaceXService->getAllLaunches();
        $launch = collect($launches)->firstWhere('id', $id);

        if (!$launch) {
            return response()->json(['error' => 'Lancement non trouvé'], 404);
        }

        // Enrichir avec rocket et launchpad si disponibles
        $rocket = null;
        $launchpad = null;
        if (!empty($launch['rocket'])) {
            $rocket = $this->spaceXService->getRocket($launch['rocket']);
        }
        if (!empty($launch['launchpad'])) {
            $launchpad = $this->spaceXService->getLaunchpad($launch['launchpad']);
        }

        return response()->json([
            'launch' => $launch,
            'rocket' => $rocket,
            'launchpad' => $launchpad,
        ]);
    }
}