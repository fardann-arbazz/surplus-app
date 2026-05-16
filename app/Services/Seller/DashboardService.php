<?php

namespace App\Services\Seller;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Orders;
use App\Models\Product;
use App\Models\Products;
use App\Models\Stores;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function __construct(private readonly Stores $store) {}

    // ──────────────────────────────────────────────────────────────
    // Stats Cards
    // ──────────────────────────────────────────────────────────────

    /**
     * Ringkasan stats untuk 4 kartu di atas dashboard.
     */
    public function getStats(): array
    {
        $storeId = $this->store->id;
        // dd($storeId);

        $totalOrders   = Orders::forStore($storeId)->count();
        $totalRevenue  = Orders::forStore($storeId)->paid()->sum('total_price');
        $activeMenus   = Products::forStore($storeId)->active()->count();

        // Persentase perubahan dibanding bulan lalu
        $now           = Carbon::now();
        $startThisMonth = $now->copy()->startOfMonth();
        $startLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endLastMonth   = $now->copy()->subMonth()->endOfMonth();

        $ordersThisMonth = Orders::forStore($storeId)
            ->where('created_at', '>=', $startThisMonth)
            ->count();

        $ordersLastMonth = Orders::forStore($storeId)
            ->whereBetween('created_at', [$startLastMonth, $endLastMonth])
            ->count();

        $revenueThisMonth = Orders::forStore($storeId)->paid()
            ->where('created_at', '>=', $startThisMonth)
            ->sum('total_price');

        $revenueLastMonth = Orders::forStore($storeId)->paid()
            ->whereBetween('created_at', [$startLastMonth, $endLastMonth])
            ->sum('total_price');

        return [
            'total_orders'          => $totalOrders,
            'total_revenue'         => $totalRevenue,
            'active_menus'          => $activeMenus,
            'orders_change_pct'     => $this->percentChange($ordersLastMonth, $ordersThisMonth),
            'revenue_change_pct'    => $this->percentChange($revenueLastMonth, $revenueThisMonth),
        ];
    }

    // ──────────────────────────────────────────────────────────────
    // Chart Data
    // ──────────────────────────────────────────────────────────────

    /**
     * Data grafik berdasarkan periode: daily | weekly | monthly
     */
    public function getChartData(string $period = 'daily'): array
    {
        return match ($period) {
            'weekly'  => $this->weeklyChart(),
            'monthly' => $this->monthlyChart(),
            default   => $this->dailyChart(),
        };
    }

    private function dailyChart(): array
    {
        // 7 hari terakhir
        $days = collect(range(6, 0))->map(fn($i) => Carbon::today()->subDays($i));

        $rows = Orders::forStore($this->store->id)
            ->paid()
            ->where('created_at', '>=', Carbon::today()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as date, SUM(total_price) as revenue, COUNT(*) as orders')
            ->groupByRaw('DATE(created_at)')
            ->get()
            ->keyBy('date');

        $labels  = [];
        $revenue = [];
        $orders  = [];

        foreach ($days as $day) {
            $key       = $day->toDateString();
            $labels[]  = $day->translatedFormat('D'); // Mon, Tue, …
            $revenue[] = (int) ($rows[$key]->revenue ?? 0);
            $orders[]  = (int) ($rows[$key]->orders  ?? 0);
        }

        return compact('labels', 'revenue', 'orders');
    }

    private function weeklyChart(): array
    {
        // 8 minggu terakhir
        $weeks = collect(range(7, 0))->map(fn($i) => Carbon::now()->subWeeks($i)->startOfWeek());

        $rows = Orders::forStore($this->stores->id)
            ->paid()
            ->where('created_at', '>=', Carbon::now()->subWeeks(7)->startOfWeek())
            ->selectRaw('YEARWEEK(created_at, 1) as yw, SUM(total_price) as revenue, COUNT(*) as orders')
            ->groupByRaw('YEARWEEK(created_at, 1)')
            ->get()
            ->keyBy('yw');

        $labels  = [];
        $revenue = [];
        $orders  = [];

        foreach ($weeks as $week) {
            $yw        = $week->format('oW'); // ISO year+week number
            $labels[]  = 'W' . $week->weekOfYear;
            $revenue[] = (int) ($rows[$yw]->revenue ?? 0);
            $orders[]  = (int) ($rows[$yw]->orders  ?? 0);
        }

        return compact('labels', 'revenue', 'orders');
    }

    private function monthlyChart(): array
    {
        // 6 bulan terakhir
        $months = collect(range(5, 0))->map(fn($i) => Carbon::now()->subMonths($i)->startOfMonth());

        $rows = Orders::forStore($this->stores->id)
            ->paid()
            ->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as ym, SUM(total_price) as revenue, COUNT(*) as orders')
            ->groupByRaw('DATE_FORMAT(created_at, "%Y-%m")')
            ->get()
            ->keyBy('ym');

        $labels  = [];
        $revenue = [];
        $orders  = [];

        foreach ($months as $month) {
            $ym        = $month->format('Y-m');
            $labels[]  = $month->translatedFormat('M Y'); // Jan 2025, …
            $revenue[] = (int) ($rows[$ym]->revenue ?? 0);
            $orders[]  = (int) ($rows[$ym]->orders  ?? 0);
        }

        return compact('labels', 'revenue', 'orders');
    }

    // ──────────────────────────────────────────────────────────────
    // Recent Orders
    // ──────────────────────────────────────────────────────────────

    /**
     * 5 order terbaru untuk store ini.
     */
    public function getRecentOrders(): Collection
    {
        return Orders::forStore($this->store->id)
            ->with(['user', 'orderItems'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn(Orders $order) => [
                'id'         => $order->id,
                'customer'   => $order->user->name ?? 'Guest',
                'items'      => $order->orderItems->count(),
                'total'      => 'Rp ' . number_format($order->total_price, 0, ',', '.'),
                'status'     => $order->status->label(),
                'statusBg'   => $this->statusBg($order->status),
                'statusClass' => $this->statusClass($order->status),
                'created_at' => $order->created_at->diffForHumans(),
            ]);
    }

    // ──────────────────────────────────────────────────────────────
    // Popular Menus
    // ──────────────────────────────────────────────────────────────
    public function getPopularMenus(): Collection
    {
        return Products::forStore($this->store->id)
            ->with('productImg')
            ->withCount([
                'surplusProducts as order_count' => function ($q) {

                    $q->join(
                        'order_items',
                        'order_items.surplus_id',
                        '=',
                        'surplus_products.id'
                    )
                        ->join(
                            'orders',
                            'orders.id',
                            '=',
                            'order_items.order_id'
                        )
                        ->where('orders.store_id', $this->store->id)

                        ->whereIn('orders.status', [
                            'paid',
                            'ready_for_pickup',
                            'completed'
                        ])

                        ->select(
                            DB::raw('COUNT(DISTINCT orders.id)')
                        );
                },
            ])

            ->orderByDesc('order_count')
            ->limit(5)

            ->get()

            ->map(fn(Products $product) => [

                'id'    => $product->id,

                'name'  => $product->name,

                'sold'  => $product->order_count,

                'price' => 'Rp ' . number_format(
                    $product->price,
                    0,
                    ',',
                    '.'
                ),

                'image' => $product->productImg->first()?->img_url
                    ?? 'https://placehold.co/80x80/f97316/ffffff?text=' .
                    urlencode(substr($product->name, 0, 2)),
            ]);
    }

    // ──────────────────────────────────────────────────────────────
    // Helpers
    // ──────────────────────────────────────────────────────────────

    private function percentChange(int|float $old, int|float $new): string
    {
        if ($old == 0) {
            return $new > 0 ? '+100%' : '0%';
        }

        $pct = (($new - $old) / $old) * 100;
        $sign = $pct >= 0 ? '+' : '';

        return $sign . number_format($pct, 1) . '%';
    }


    private function statusBg(OrderStatus $status): string
    {
        return match ($status) {
            OrderStatus::Pending => 'bg-yellow-100',

            OrderStatus::Paid => 'bg-blue-100',

            OrderStatus::ReadyForPickup => 'bg-purple-100',

            OrderStatus::Completed => 'bg-green-100',

            OrderStatus::Expired => 'bg-red-100',
        };
    }

    private function statusClass(OrderStatus $status): string
    {
        return match ($status) {
            OrderStatus::Pending => 'text-yellow-700 bg-yellow-50',

            OrderStatus::Paid => 'text-blue-700 bg-blue-50',

            OrderStatus::ReadyForPickup => 'text-purple-700 bg-purple-50',

            OrderStatus::Completed => 'text-green-700 bg-green-50',

            OrderStatus::Expired => 'text-red-700 bg-red-50',
        };
    }
}
