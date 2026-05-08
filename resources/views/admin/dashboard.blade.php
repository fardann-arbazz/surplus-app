@extends('layouts.admin')

@section('title', 'Dashboard')

@section('content')

    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl lg:text-2xl font-bold">Dashboard Overview</h1>
            <p class="text-sm text-base-content/50 mt-1">Welcome back, Admin! Here's what's happening today.</p>
        </div>
        <div class="flex items-center gap-2">
            <select class="select select-bordered select-sm text-sm focus:outline-none focus:border-warning">
                <option>Today</option>
                <option>This Week</option>
                <option>This Month</option>
                <option>This Year</option>
            </select>
            <button class="btn btn-outline btn-sm gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Total Users -->
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body p-4 lg:p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-blue-600" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-success bg-success/10 px-2 py-1 rounded-full">+12.5%</span>
                </div>
                <p class="text-2xl lg:text-3xl font-bold">{{ $countUser }}</p>
                <p class="text-xs lg:text-sm text-base-content/50 mt-1">Total Users</p>
            </div>
        </div>

        <!-- Total Sellers -->
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body p-4 lg:p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 lg:w-12 lg:h-12 bg-warning/10 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-warning" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-success bg-success/10 px-2 py-1 rounded-full">+8.2%</span>
                </div>
                <p class="text-2xl lg:text-3xl font-bold">{{ $countSellerActive }}</p>
                <p class="text-xs lg:text-sm text-base-content/50 mt-1">Active Sellers</p>
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body p-4 lg:p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 lg:w-12 lg:h-12 bg-error/10 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-error" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-error bg-error/10 px-2 py-1 rounded-full">Needs Action</span>
                </div>
                <p class="text-2xl lg:text-3xl font-bold">{{ $countSellerPending }}</p>
                <p class="text-xs lg:text-sm text-base-content/50 mt-1">Pending Approvals</p>
            </div>
        </div>

        <!-- Revenue -->
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body p-4 lg:p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 lg:w-12 lg:h-12 bg-success/10 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 lg:w-6 lg:h-6 text-success" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <span class="text-xs font-medium text-success bg-success/10 px-2 py-1 rounded-full">+18.7%</span>
                </div>
                <p class="text-2xl lg:text-3xl font-bold">Rp 128M</p>
                <p class="text-xs lg:text-sm text-base-content/50 mt-1">Total Revenue</p>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Revenue Chart -->
        <div class="lg:col-span-2 card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body p-4 lg:p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="card-title text-base">Revenue Overview</h3>
                    <div class="btn-group">
                        <button class="btn btn-xs btn-warning">Daily</button>
                        <button class="btn btn-xs btn-ghost">Weekly</button>
                        <button class="btn btn-xs btn-ghost">Monthly</button>
                    </div>
                </div>
                <div class="h-64 lg:h-80">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body p-4 lg:p-6">
                <h3 class="card-title text-base mb-4">Quick Stats</h3>
                <div class="space-y-4">
                    @php
                        $quickStats = [
                            ['label' => 'User Registrations Today', 'value' => '45', 'percentage' => 75],
                            ['label' => 'Orders Today', 'value' => '234', 'percentage' => 62],
                            ['label' => 'Seller Applications', 'value' => '12', 'percentage' => 40],
                            ['label' => 'System Uptime', 'value' => '99.9%', 'percentage' => 100],
                        ];
                    @endphp
                    @foreach ($quickStats as $stat)
                        <div>
                            <div class="flex justify-between mb-1">
                                <span class="text-xs text-base-content/60">{{ $stat['label'] }}</span>
                                <span class="text-xs font-medium">{{ $stat['value'] }}</span>
                            </div>
                            <progress class="progress progress-warning w-full" value="{{ $stat['percentage'] }}"
                                max="100"></progress>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Pending Seller Approvals -->
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body p-4 lg:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="card-title text-base">Pending Seller Approvals</h3>
                    <a href="#" class="text-xs text-warning font-medium">View All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Restaurant</th>
                                <th>Owner</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $pendingSellers = [
                                    [
                                        'id' => 1,
                                        'name' => 'Warung Padang Jaya',
                                        'owner' => 'Ahmad Fauzi',
                                        'date' => '28 Apr 2026',
                                        'initial' => 'WP',
                                    ],
                                    [
                                        'id' => 2,
                                        'name' => 'Bakso Malang Cak Man',
                                        'owner' => 'Cak Man',
                                        'date' => '28 Apr 2026',
                                        'initial' => 'BM',
                                    ],
                                    [
                                        'id' => 3,
                                        'name' => 'Sushi Fusion',
                                        'owner' => 'Dewi Lestari',
                                        'date' => '27 Apr 2026',
                                        'initial' => 'SF',
                                    ],
                                ];
                            @endphp
                            @foreach ($pendingSellers as $seller)
                                <tr>
                                    <td>
                                        <div class="flex items-center gap-2">
                                            <div class="avatar placeholder">
                                                <div class="bg-base-200 rounded-full w-8">
                                                    <span class="text-xs">{{ $seller['initial'] }}</span>
                                                </div>
                                            </div>
                                            <span class="text-sm font-medium">{{ $seller['name'] }}</span>
                                        </div>
                                    </td>
                                    <td class="text-sm">{{ $seller['owner'] }}</td>
                                    <td class="text-sm text-base-content/50">{{ $seller['date'] }}</td>
                                    <td>
                                        <div class="flex gap-1">
                                            <button class="btn btn-success btn-xs">Approve</button>
                                            <button class="btn btn-error btn-xs btn-outline">Reject</button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body p-4 lg:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="card-title text-base">Recent Activities</h3>
                    <a href="#" class="text-xs text-warning font-medium">View All</a>
                </div>
                <div class="space-y-3">
                    @php
                        $recentActivities = [
                            [
                                'icon' => '✅',
                                'message' => 'Seller "Ayam Geprek Juara" approved',
                                'time' => '10 min ago',
                                'bg' => 'bg-success/10',
                            ],
                            [
                                'icon' => '👤',
                                'message' => 'New user registered: budi.santoso@email.com',
                                'time' => '25 min ago',
                                'bg' => 'bg-info/10',
                            ],
                            [
                                'icon' => '📋',
                                'message' => 'Bulk order import completed',
                                'time' => '1 hour ago',
                                'bg' => 'bg-warning/10',
                            ],
                            [
                                'icon' => '🔧',
                                'message' => 'System maintenance completed',
                                'time' => '3 hours ago',
                                'bg' => 'bg-base-300',
                            ],
                        ];
                    @endphp
                    @foreach ($recentActivities as $activity)
                        <div
                            class="flex items-start gap-3 p-2 rounded-lg hover:bg-base-200 transition-colors cursor-pointer">
                            <div
                                class="w-8 h-8 rounded-full flex items-center justify-center shrink-0 {{ $activity['bg'] }}">
                                <span>{{ $activity['icon'] }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm">{{ $activity['message'] }}</p>
                                <p class="text-xs text-base-content/50">{{ $activity['time'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- System Health -->
    <div class="card bg-base-100 border border-base-200 shadow-sm">
        <div class="card-body p-4 lg:p-6">
            <h3 class="card-title text-base mb-4">System Health</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @php
                    $systemHealth = [
                        ['label' => 'API Response', 'value' => '145ms', 'color' => 'text-success'],
                        ['label' => 'Database Load', 'value' => '34%', 'color' => 'text-success'],
                        ['label' => 'Storage', 'value' => '62%', 'color' => 'text-warning'],
                        ['label' => 'Memory', 'value' => '48%', 'color' => 'text-success'],
                    ];
                @endphp
                @foreach ($systemHealth as $health)
                    <div class="text-center p-4 bg-base-200 rounded-xl">
                        <p class="text-2xl lg:text-3xl font-bold {{ $health['color'] }}">{{ $health['value'] }}</p>
                        <p class="text-xs text-base-content/50 mt-1">{{ $health['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const ctx = document.getElementById('revenueChart');
            if (!ctx) return;

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                    datasets: [{
                            label: 'Revenue',
                            data: [4200000, 5800000, 4900000, 7200000, 6100000, 8900000, 7800000],
                            borderColor: '#f97316',
                            backgroundColor: 'rgba(249, 115, 22, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#f97316',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                        },
                        {
                            label: 'Platform Fee',
                            data: [420000, 580000, 490000, 720000, 610000, 890000, 780000],
                            borderColor: '#6366f1',
                            backgroundColor: 'rgba(99, 102, 241, 0.1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#6366f1',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        intersect: false,
                        mode: 'index'
                    },
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 24,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: (ctx) => ctx.dataset.label + ': Rp ' + ctx.parsed.y.toLocaleString()
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: '#f1f5f9'
                            },
                            ticks: {
                                callback: (value) => 'Rp ' + (value / 1000000).toFixed(1) + 'M'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush
