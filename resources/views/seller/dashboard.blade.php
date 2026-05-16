@extends('layouts.seller')

@section('title', 'Dashboard')

@section('content')
    {{-- Page Title --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold">Dashboard</h1>
            <p class="text-sm text-base-content/50 mt-1">
                Selamat datang kembali, <span class="font-medium text-base-content/70">{{ Auth::user()->name }}</span>!
                Berikut ringkasan toko Anda.
            </p>
        </div>

        <div class="flex items-center gap-2 bg-base-200/50 rounded-lg px-4 py-2.5">
            <span class="text-xs text-base-content/50 font-medium">Status Toko</span>
            <div class="flex items-center gap-2">
                <span class="relative flex h-2.5 w-2.5">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full {{ $store->is_online ? 'bg-success' : 'bg-gray-400' }} opacity-75"></span>
                    <span
                        class="relative inline-flex rounded-full h-2.5 w-2.5 {{ $store->is_online ? 'bg-success' : 'bg-gray-400' }}"></span>
                </span>
                <span class="text-sm font-semibold {{ $store->is_online ? 'text-success' : 'text-base-content/40' }}">
                    {{ $store->is_online ? 'Online' : 'Offline' }}
                </span>
            </div>
        </div>
    </div>

    {{-- Flash Message --}}
    @if (session('success'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" x-show="show" x-transition class="mb-4">
            <div class="alert alert-success shadow-md flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 stroke-current" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button @click="show = false" class="btn btn-sm btn-ghost">✕</button>
            </div>
        </div>
    @endif

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        {{-- Total Orders --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-4 lg:p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 lg:w-6 lg:h-6 text-blue-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <span
                    class="text-xs font-medium px-2 py-1 rounded-full
                    {{ str_starts_with($stats['orders_change_pct'], '+') ? 'text-green-600 bg-green-50' : 'text-red-500 bg-red-50' }}">
                    {{ $stats['orders_change_pct'] }}
                </span>
            </div>
            <p class="text-2xl lg:text-3xl font-bold text-slate-800">{{ number_format($stats['total_orders']) }}</p>
            <p class="text-xs lg:text-sm text-slate-500 mt-1">Total Orders</p>
        </div>

        {{-- Revenue --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-4 lg:p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 lg:w-6 lg:h-6 text-green-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span
                    class="text-xs font-medium px-2 py-1 rounded-full
                    {{ str_starts_with($stats['revenue_change_pct'], '+') ? 'text-green-600 bg-green-50' : 'text-red-500 bg-red-50' }}">
                    {{ $stats['revenue_change_pct'] }}
                </span>
            </div>
            <p class="text-2xl lg:text-3xl font-bold text-slate-800">
                Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}
            </p>
            <p class="text-xs lg:text-sm text-slate-500 mt-1">Total Revenue</p>
        </div>

        {{-- Active Menu --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-4 lg:p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-orange-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 lg:w-6 lg:h-6 text-orange-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
            </div>
            <p class="text-2xl lg:text-3xl font-bold text-slate-800">{{ $stats['active_menus'] }}</p>
            <p class="text-xs lg:text-sm text-slate-500 mt-1">Active Menu</p>
        </div>

        {{-- Avg Rating — placeholder, skip logic sesuai permintaan --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-4 lg:p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 lg:w-6 lg:h-6 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
                <span class="text-xs font-medium text-slate-400 bg-slate-50 px-2 py-1 rounded-full">—</span>
            </div>
            <p class="text-2xl lg:text-3xl font-bold text-slate-800">—</p>
            <p class="text-xs lg:text-sm text-slate-500 mt-1">Avg Rating</p>
        </div>

    </div>

    {{-- Chart & Recent Orders --}}

    {{-- @dd($chartData) --}}

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6"
        x-data='salesChart(
        @json($chartData),
        "{{ route('seller.dashboard.chart-data') }}"
    )'>

        {{-- Sales Chart --}}
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 p-4 lg:p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-base lg:text-lg font-semibold text-slate-800">Sales Overview</h3>
                <div class="flex gap-2">
                    <template x-for="p in periods" :key="p.value">
                        <button @click="changePeriod(p.value)"
                            :class="activePeriod === p.value ?
                                'bg-orange-500 text-white' :
                                'text-slate-600 hover:bg-slate-100'"
                            class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors" x-text="p.label">
                        </button>
                    </template>
                </div>
            </div>

            {{-- Loading overlay --}}
            <div x-show="loading" class="flex items-center justify-center h-64 lg:h-72">
                <span class="loading loading-spinner loading-md text-orange-500"></span>
            </div>

            <div x-show="!loading" class="h-64 lg:h-72">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        {{-- Recent Orders --}}
        <div class="bg-white rounded-2xl border border-slate-100 p-4 lg:p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-base lg:text-lg font-semibold text-slate-800">Recent Orders</h3>
                <a href="{{ route('seller.orders.index') }}" class="text-xs font-medium text-orange-600">View All</a>
            </div>

            @if ($recentOrders->isEmpty())
                <div class="flex flex-col items-center justify-center h-40 text-slate-400">
                    <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                    <p class="text-sm">Belum ada pesanan</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($recentOrders as $order)
                        <div
                            class="flex items-center gap-3 p-2 rounded-md hover:bg-slate-50 transition-colors cursor-pointer">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-800 truncate">{{ $order['customer'] }}</p>
                                <p class="text-xs text-slate-500">{{ $order['items'] }} items • {{ $order['total'] }}</p>
                            </div>
                            <span class="text-xs font-medium px-2 py-1 rounded-full {{ $order['statusClass'] }}">
                                {{ $order['status'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

    </div>

    {{-- Popular Menu --}}
    <div class="bg-white rounded-2xl border border-slate-100 p-4 lg:p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-base lg:text-lg font-semibold text-slate-800">Popular Menu Items</h3>
            <a href="{{ route('seller.menu-management') }}" class="text-xs font-medium text-orange-600">Manage Menu</a>
        </div>

        @if ($popularMenus->isEmpty())
            <div class="flex flex-col items-center justify-center h-40 text-slate-400">
                <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                <p class="text-sm">Belum ada produk</p>
            </div>
        @else
            <div class="space-y-4">
                @foreach ($popularMenus as $index => $item)
                    <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-slate-50 transition-colors">
                        <span class="text-sm font-bold text-slate-400 w-6">#{{ $index + 1 }}</span>
                        <img src="{{ $item['image'] }}" alt="{{ $item['name'] }}"
                            class="w-12 h-12 rounded-xl object-cover"
                            onerror="this.src='https://placehold.co/48x48/f97316/ffffff?text={{ urlencode(substr($item['name'], 0, 2)) }}'">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800 truncate">{{ $item['name'] }}</p>
                            <p class="text-xs text-slate-500">{{ $item['sold'] }} orders</p>
                        </div>
                        <span class="text-sm font-semibold text-orange-600">{{ $item['price'] }}</span>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('salesChart', (initialData, chartUrl) => ({
                activePeriod: 'daily',
                loading: false,
                chartInstance: null,
                periods: [{
                        value: 'daily',
                        label: 'Daily'
                    },
                    {
                        value: 'weekly',
                        label: 'Weekly'
                    },
                    {
                        value: 'monthly',
                        label: 'Monthly'
                    },
                ],

                init() {
                    this.$nextTick(() => this.buildChart(initialData));
                },

                async changePeriod(period) {
                    if (this.activePeriod === period) return;

                    this.activePeriod = period;
                    this.loading = true;

                    try {
                        const res = await fetch(`${chartUrl}?period=${period}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await res.json();

                        this.$nextTick(() => {
                            this.loading = false;
                            this.$nextTick(() => this.buildChart(data));
                        });
                    } catch (e) {
                        console.error('Chart fetch error:', e);
                        this.loading = false;
                    }
                },

                buildChart(data) {
                    const ctx = document.getElementById('salesChart');
                    if (!ctx) return;

                    if (this.chartInstance) {
                        this.chartInstance.destroy();
                        this.chartInstance = null;
                    }

                    this.chartInstance = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                    label: 'Revenue (Rp)',
                                    data: data.revenue,
                                    borderColor: '#f97316',
                                    backgroundColor: 'rgba(249,115,22,0.1)',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.4,
                                    pointBackgroundColor: '#f97316',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    pointRadius: 4,
                                    yAxisID: 'yRevenue',
                                },
                                {
                                    label: 'Orders',
                                    data: data.orders,
                                    borderColor: '#6366f1',
                                    backgroundColor: 'rgba(99,102,241,0.1)',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.4,
                                    pointBackgroundColor: '#6366f1',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    pointRadius: 4,
                                    yAxisID: 'yOrders',
                                },
                            ],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            interaction: {
                                mode: 'index',
                                intersect: false
                            },
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20
                                    },
                                },
                                tooltip: {
                                    callbacks: {
                                        label(ctx) {
                                            if (ctx.dataset.yAxisID === 'yRevenue') {
                                                return ' Rp ' + ctx.parsed.y.toLocaleString(
                                                    'id-ID');
                                            }
                                            return ' ' + ctx.parsed.y + ' orders';
                                        },
                                    },
                                },
                            },
                            scales: {
                                yRevenue: {
                                    type: 'linear',
                                    position: 'left',
                                    beginAtZero: true,
                                    grid: {
                                        color: '#f1f5f9'
                                    },
                                    ticks: {
                                        callback: v => 'Rp ' + (v >= 1000000 ?
                                            (v / 1000000).toFixed(1) + 'M' :
                                            (v / 1000).toFixed(0) + 'K'),
                                    },
                                },
                                yOrders: {
                                    type: 'linear',
                                    position: 'right',
                                    beginAtZero: true,
                                    grid: {
                                        drawOnChartArea: false
                                    },
                                    ticks: {
                                        precision: 0
                                    },
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                },
                            },
                        },
                    });
                },
            }));
        });
    </script>
@endpush
