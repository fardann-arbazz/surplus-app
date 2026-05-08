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

                <!-- Close button -->
                <button @click="show = false" class="btn btn-sm btn-ghost">✕</button>
            </div>
        </div>
    @endif

    {{-- Stats Grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-2xl border border-slate-100 p-4 lg:p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 lg:w-6 lg:h-6 text-blue-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">+12%</span>
            </div>
            <p class="text-2xl lg:text-3xl font-bold text-slate-800">156</p>
            <p class="text-xs lg:text-sm text-slate-500 mt-1">Total Orders</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 p-4 lg:p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 lg:w-6 lg:h-6 text-green-600" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">+8%</span>
            </div>
            <p class="text-2xl lg:text-3xl font-bold text-slate-800">Rp 4.2M</p>
            <p class="text-xs lg:text-sm text-slate-500 mt-1">Revenue</p>
        </div>

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
            <p class="text-2xl lg:text-3xl font-bold text-slate-800">32</p>
            <p class="text-xs lg:text-sm text-slate-500 mt-1">Active Menu</p>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 p-4 lg:p-5 shadow-sm">
            <div class="flex items-center justify-between mb-3">
                <div class="w-10 h-10 lg:w-12 lg:h-12 bg-yellow-100 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 lg:w-6 lg:h-6 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                    </svg>
                </div>
                <span class="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">4.8</span>
            </div>
            <p class="text-2xl lg:text-3xl font-bold text-slate-800">4.8</p>
            <p class="text-xs lg:text-sm text-slate-500 mt-1">Avg Rating</p>
        </div>
    </div>

    {{-- Chart & Recent Orders --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 p-4 lg:p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-base lg:text-lg font-semibold text-slate-800">Sales Overview</h3>
                <div class="flex gap-2">
                    <button class="px-3 py-1.5 rounded-lg text-xs font-medium bg-orange-500 text-white">Daily</button>
                    <button
                        class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100">Weekly</button>
                    <button
                        class="px-3 py-1.5 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-100">Monthly</button>
                </div>
            </div>
            <div class="h-64 lg:h-72">
                <canvas id="salesChart"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 p-4 lg:p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-base lg:text-lg font-semibold text-slate-800">Recent Orders</h3>
                <a href="#" class="text-xs font-medium text-orange-600">View All</a>
            </div>
            <div class="space-y-4">
                <template x-for="order in recentOrders" :key="order.id">
                    <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50 transition-colors cursor-pointer">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0" :class="order.statusBg">
                            <span x-text="order.icon"></span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800 truncate" x-text="order.customer"></p>
                            <p class="text-xs text-slate-500" x-text="order.items + ' items • ' + order.total"></p>
                        </div>
                        <span class="text-xs font-medium px-2 py-1 rounded-full" :class="order.statusClass"
                            x-text="order.status"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Popular Menu & Pending Orders --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-100 p-4 lg:p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-base lg:text-lg font-semibold text-slate-800">Popular Menu Items</h3>
                <a href="#" class="text-xs font-medium text-orange-600">Manage Menu</a>
            </div>
            <div class="space-y-4">
                <template x-for="(item, index) in popularMenus" :key="item.id">
                    <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-slate-50 transition-colors">
                        <span class="text-sm font-bold text-slate-400 w-6" x-text="'#' + (index + 1)"></span>
                        <img :src="item.image" :alt="item.name" class="w-12 h-12 rounded-xl object-cover">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-slate-800 truncate" x-text="item.name"></p>
                            <p class="text-xs text-slate-500" x-text="item.sold + ' sold'"></p>
                        </div>
                        <span class="text-sm font-semibold text-orange-600" x-text="item.price"></span>
                    </div>
                </template>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-slate-100 p-4 lg:p-6 shadow-sm">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-base lg:text-lg font-semibold text-slate-800">Pending Orders</h3>
                <span class="text-xs font-bold bg-red-100 text-red-600 px-2 py-1 rounded-full">5 New</span>
            </div>
            <div class="space-y-3">
                <template x-for="order in pendingOrders" :key="order.id">
                    <div class="p-3 bg-orange-50 rounded-xl border border-orange-100">
                        <div class="flex items-start justify-between mb-2">
                            <div>
                                <p class="text-sm font-semibold text-slate-800" x-text="order.orderId"></p>
                                <p class="text-xs text-slate-500" x-text="order.customer"></p>
                            </div>
                            <span class="text-xs font-medium text-orange-600 bg-orange-100 px-2 py-1 rounded-full"
                                x-text="order.time"></span>
                        </div>
                        <p class="text-xs text-slate-600 mb-3" x-text="order.items"></p>
                        <div class="flex gap-2">
                            <button
                                class="flex-1 py-1.5 bg-orange-500 text-white rounded-lg text-xs font-semibold hover:bg-orange-600 transition-colors">Accept</button>
                            <button
                                class="px-3 py-1.5 border border-slate-200 rounded-lg text-xs font-medium text-slate-600 hover:bg-slate-50 transition-colors">Reject</button>
                        </div>
                    </div>
                </template>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('sellerDashboard', () => ({
                sidebarOpen: false,
                recentOrders: [{
                        id: 1,
                        customer: 'Sarah Johnson',
                        items: 3,
                        total: 'Rp 85.000',
                        status: 'Completed',
                        statusBg: 'bg-green-100',
                        statusClass: 'text-green-700 bg-green-50',
                        icon: '✅'
                    },
                    {
                        id: 2,
                        customer: 'Budi Santoso',
                        items: 2,
                        total: 'Rp 52.000',
                        status: 'Processing',
                        statusBg: 'bg-blue-100',
                        statusClass: 'text-blue-700 bg-blue-50',
                        icon: '🔄'
                    },
                    {
                        id: 3,
                        customer: 'Ani Rahmawati',
                        items: 5,
                        total: 'Rp 145.000',
                        status: 'New Order',
                        statusBg: 'bg-orange-100',
                        statusClass: 'text-orange-700 bg-orange-50',
                        icon: '🆕'
                    },
                    {
                        id: 4,
                        customer: 'David Chen',
                        items: 1,
                        total: 'Rp 28.000',
                        status: 'Completed',
                        statusBg: 'bg-green-100',
                        statusClass: 'text-green-700 bg-green-50',
                        icon: '✅'
                    },
                ],
                popularMenus: [{
                        id: 1,
                        name: 'Nasi Goreng Special',
                        sold: 45,
                        price: 'Rp 25.000',
                        image: 'https://images.unsplash.com/photo-1603133872878-684f208fb84b?w=80&h=80&fit=crop'
                    },
                    {
                        id: 2,
                        name: 'Ayam Goreng Kremes',
                        sold: 38,
                        price: 'Rp 22.000',
                        image: 'https://images.unsplash.com/photo-1626082927389-6cd097cdc6ec?w=80&h=80&fit=crop'
                    },
                    {
                        id: 3,
                        name: 'Mie Goreng Jawa',
                        sold: 32,
                        price: 'Rp 18.000',
                        image: 'https://images.unsplash.com/photo-1569718212165-3a8278d5f624?w=80&h=80&fit=crop'
                    },
                    {
                        id: 4,
                        name: 'Es Teh Manis',
                        sold: 67,
                        price: 'Rp 5.000',
                        image: 'https://images.unsplash.com/photo-1570029913791-648eed32aa07?w=80&h=80&fit=crop'
                    },
                ],
                pendingOrders: [{
                        id: 1,
                        orderId: '#ORD-001',
                        customer: 'Rina Wijaya',
                        time: '2 min ago',
                        items: '2x Nasi Goreng, 1x Es Teh'
                    },
                    {
                        id: 2,
                        orderId: '#ORD-002',
                        customer: 'Alex Kumar',
                        time: '5 min ago',
                        items: '1x Ayam Kremes, 2x Mie Goreng, 2x Es Jeruk'
                    },
                    {
                        id: 3,
                        orderId: '#ORD-003',
                        customer: 'Maya Dewi',
                        time: '8 min ago',
                        items: '3x Nasi Goreng Special'
                    },
                ],
                init() {
                    this.initChart();
                    window.addEventListener('resize', () => {
                        if (window.innerWidth >= 1024) this.sidebarOpen = false;
                    });
                },
                initChart() {
                    const ctx = document.getElementById('salesChart');
                    if (!ctx) return;
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                            datasets: [{
                                    label: 'Revenue',
                                    data: [450000, 520000, 480000, 610000, 550000, 720000,
                                        680000
                                    ],
                                    borderColor: '#f97316',
                                    backgroundColor: 'rgba(249,115,22,0.1)',
                                    borderWidth: 2,
                                    fill: true,
                                    tension: 0.4,
                                    pointBackgroundColor: '#f97316',
                                    pointBorderColor: '#fff',
                                    pointBorderWidth: 2,
                                    pointRadius: 4,
                                },
                                {
                                    label: 'Orders',
                                    data: [15, 18, 16, 22, 20, 25, 23],
                                    borderColor: '#6366f1',
                                    backgroundColor: 'rgba(99,102,241,0.1)',
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
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20
                                    }
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    grid: {
                                        color: '#f1f5f9'
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
                }
            }));
        });
    </script>
@endpush
