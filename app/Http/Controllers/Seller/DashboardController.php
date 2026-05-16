<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\Stores;
use App\Services\Seller\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    // ──────────────────────────────────────────────────────────────
    // Main Page
    // ──────────────────────────────────────────────────────────────

    public function showDashboard(): View
    {
        $store   = $this->getStore();
        $service = new DashboardService($store);

        return view('seller.dashboard', [
            'store'        => $store,
            'stats'        => $service->getStats(),
            'recentOrders' => $service->getRecentOrders(),
            'popularMenus' => $service->getPopularMenus(),
            'chartData'    => $service->getChartData('daily'), // default
        ]);
    }

    // ──────────────────────────────────────────────────────────────
    // AJAX — Chart period switcher
    // ──────────────────────────────────────────────────────────────

    public function chartData(Request $request): JsonResponse
    {
        $period  = $request->input('period', 'daily');
        $store   = $this->getStore();
        $service = new DashboardService($store);

        return response()->json($service->getChartData($period));
    }

    // ──────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────

    private function getStore(): Stores
    {
        return Stores::where('user_id', Auth::id())->firstOrFail();
    }
}
