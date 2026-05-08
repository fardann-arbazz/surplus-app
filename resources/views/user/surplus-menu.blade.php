{{-- resources/views/user/surplus-menu.blade.php --}}
@extends('layouts.user')

@section('title', 'Surplus Terdekat - Rantangku')

@section('content')
    <div class="min-h-screen bg-base-200 pb-20">

        {{-- TOPBAR --}}
        <div class="sticky top-0 z-10 bg-base-100/95 backdrop-blur border-b border-base-200 px-4 py-3">
            <div class="flex items-center justify-between">
                <div class="flex-1 min-w-0">
                    <h1 class="text-lg font-bold truncate">Surplus Terdekat</h1>
                    <p class="text-xs text-base-content/50 truncate">Makanan segar, harga hemat, selamatkan bumi 🌍</p>
                </div>
            </div>
        </div>

        {{-- STATE 1: WAITING LOCATION --}}
        <div id="stateWaiting" class="flex flex-col items-center justify-center min-h-[60vh] px-4 hidden">
            <div class="w-24 h-24 rounded-full bg-warning/10 flex items-center justify-center mb-6 animate-pulse">
                <svg class="w-12 h-12 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </div>
            <h2 class="text-lg font-semibold mb-2">Atur Lokasi Dulu Yuk!</h2>
            <p class="text-sm text-base-content/60 text-center mb-6 max-w-xs">
                Kami perlu tahu lokasi kamu untuk menampilkan surplus makanan terdekat
            </p>
            <button onclick="LocationPicker.open()" class="btn btn-warning gap-2 px-8">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Pilih Lokasi
            </button>
        </div>

        {{-- STATE 2: LOADING --}}
        <div id="stateLoading" class="hidden">
            {{-- Filter Bar (tetap tampil saat loading) --}}
            <div class="px-4 pt-4 pb-2">
                <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-hide" id="categoryChips">
                    {{-- Rendered by JS --}}
                </div>
                <div class="flex items-center gap-3 mt-3">
                    <span class="text-sm text-base-content/60 whitespace-nowrap">Radius</span>
                    <input type="range" min="1" max="20" step="1" value="5" id="radiusSlider"
                        class="range range-warning range-sm flex-1" oninput="onRadiusChange(this.value)">
                    <span class="text-sm font-medium min-w-[45px] text-right" id="radiusVal">5 km</span>
                </div>
            </div>

            {{-- Skeleton Grid --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 px-4 mt-2">
                @for ($i = 0; $i < 8; $i++)
                    <div class="card bg-base-100 shadow-sm overflow-hidden">
                        <div class="skeleton h-36 w-full rounded-none"></div>
                        <div class="p-3 space-y-2">
                            <div class="skeleton h-4 w-20 rounded"></div>
                            <div class="skeleton h-4 w-full rounded"></div>
                            <div class="skeleton h-3 w-3/4 rounded"></div>
                            <div class="skeleton h-5 w-1/2 rounded mt-1"></div>
                            <div class="flex justify-between mt-2">
                                <div class="skeleton h-3 w-16 rounded"></div>
                                <div class="skeleton h-3 w-16 rounded"></div>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>

        {{-- STATE 3: PRODUCT GRID --}}
        <div id="stateContent" class="hidden">
            {{-- Filter Bar --}}
            <div class="px-4 pt-4 pb-2">
                <div class="flex items-center justify-between mb-2">
                    <div class="flex gap-2 overflow-x-auto pb-1 scrollbar-hide flex-1 mr-2" id="categoryChipsActive">
                        {{-- Rendered by JS --}}
                    </div>
                    <button onclick="refreshData()" class="btn btn-ghost btn-sm btn-circle flex-shrink-0" title="Refresh">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                    </button>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-sm text-base-content/60 whitespace-nowrap">Radius</span>
                    <input type="range" min="1" max="20" step="1" value="5"
                        id="radiusSliderActive" class="range range-warning range-sm flex-1"
                        oninput="onRadiusChange(this.value)">
                    <span class="text-sm font-medium min-w-[45px] text-right" id="radiusValActive">5 km</span>
                </div>
                {{-- Result count --}}
                <p class="text-xs text-base-content/40 mt-2" id="resultCount"></p>
            </div>

            {{-- Product Grid --}}
            <div id="productGrid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 px-4">
                {{-- Rendered by JS --}}
            </div>

            {{-- Load More --}}
            <div id="loadMoreContainer" class="text-center py-6 hidden">
                <button onclick="loadMore()" id="loadMoreBtn" class="btn btn-outline btn-sm gap-2">
                    <span id="loadMoreText">Muat Lebih Banyak</span>
                    <span id="loadMoreSpinner" class="loading loading-spinner loading-xs hidden"></span>
                </button>
            </div>
        </div>

        {{-- STATE 4: ERROR --}}
        <div id="stateError" class="flex flex-col items-center justify-center min-h-[60vh] px-4 hidden">
            <div class="w-24 h-24 rounded-full bg-error/10 flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.5c-.77-.833-2.694-.833-3.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <h2 class="text-lg font-semibold mb-2" id="errorTitle">Oops! Gagal Memuat</h2>
            <p class="text-sm text-base-content/60 text-center mb-6 max-w-xs" id="errorMessage">
                Terjadi kesalahan saat mengambil data. Coba lagi ya!
            </p>
            <div class="flex gap-3">
                <button onclick="refreshData()" class="btn btn-warning gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Coba Lagi
                </button>
                <button onclick="LocationPicker.open()" class="btn btn-outline gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    </svg>
                    Ganti Lokasi
                </button>
            </div>
        </div>

        {{-- STATE 5: EMPTY --}}
        <div id="stateEmpty" class="flex flex-col items-center justify-center min-h-[60vh] px-4 hidden">
            <div class="w-24 h-24 rounded-full bg-warning/10 flex items-center justify-center mb-6">
                <svg class="w-12 h-12 text-warning/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
            </div>
            <h2 class="text-lg font-semibold mb-2">Belum Ada Surplus</h2>
            <p class="text-sm text-base-content/60 text-center mb-6 max-w-xs">
                Belum ada surplus makanan di sekitar kamu. Coba perluas radius atau cek lagi nanti!
            </p>
            <button onclick="expandRadius()" class="btn btn-warning gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                Perluas ke 20 km
            </button>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        // ── State Management ───────────────────────────────────
        const STATE = {
            currentRadius: 5,
            currentCat: 'semua',
            locationReady: false,
            currentPage: 1,
            hasMore: false,
            allProducts: [],
            isLoading: false,
            retryCount: 0,
            maxRetries: 3,
            fetchTimeout: 10000, // 10 detik timeout
        };

        // Cache categories untuk render ulang
        let categoriesCache = @json($categories ?? []);

        // ── DOM Elements ───────────────────────────────────────
        const DOM = {
            stateWaiting: document.getElementById('stateWaiting'),
            stateLoading: document.getElementById('stateLoading'),
            stateContent: document.getElementById('stateContent'),
            stateError: document.getElementById('stateError'),
            stateEmpty: document.getElementById('stateEmpty'),
            productGrid: document.getElementById('productGrid'),
            loadMoreContainer: document.getElementById('loadMoreContainer'),
            resultCount: document.getElementById('resultCount'),
            errorTitle: document.getElementById('errorTitle'),
            errorMessage: document.getElementById('errorMessage'),
        };

        // ── State Transitions ──────────────────────────────────
        function showState(state) {
            // hanya hide state utama
            const states = [
                DOM.stateWaiting,
                DOM.stateLoading,
                DOM.stateContent,
                DOM.stateError,
                DOM.stateEmpty,
            ];

            states.forEach(el => {
                if (el) el.classList.add('hidden');
            });

            // show target state
            const target = document.getElementById(
                `state${state.charAt(0).toUpperCase() + state.slice(1)}`
            );

            if (target) {
                target.classList.remove('hidden');
            }

            // update slider
            document.querySelectorAll('[id^="radiusSlider"]').forEach(slider => {
                slider.value = STATE.currentRadius;
            });

            document.querySelectorAll('[id^="radiusVal"]').forEach(val => {
                val.textContent = STATE.currentRadius + ' km';
            });
        }

        // ── Event Listeners ────────────────────────────────────
        document.addEventListener('locationReady', () => {
            if (!STATE.locationReady) {
                STATE.locationReady = true;
                renderCategoryChips();
                showState('loading');
                fetchSurplus(true);
            }
        });

        document.addEventListener('locationUpdated', () => {
            if (STATE.locationReady) {
                STATE.currentPage = 1;
                STATE.allProducts = [];
                showState('loading');
                fetchSurplus(true);
            }
        });

        // Initial check - jika location belum ready
        document.addEventListener('DOMContentLoaded', () => {
            if (!STATE.locationReady) {
                showState('waiting');
            }
        });

        // ── Render Categories ──────────────────────────────────
        function renderCategoryChip(cat, activeCat) {
            return `
            <button
                class="chip shrink-0 ${activeCat == cat.id ? 'chip-active' : ''}"
                data-cat="${cat.id}"
            >
                ${escapeHTML(cat.name)}
            </button>
            `;
        }

        function renderCategoryChips() {
            const activeCat = STATE.currentCat;
            const chipsHTML = `
        <button class="chip shrink-0 ${activeCat === 'semua' ? 'chip-active' : ''}" 
            data-cat="semua" onclick="setFilter(this, 'semua')">
            🍽️ Semua
        </button>
        ${categoriesCache.map(cat => renderCategoryChip(cat, activeCat)).join('')}
    `;

            document.querySelectorAll('#categoryChips, #categoryChipsActive').forEach(el => {
                el.innerHTML = chipsHTML;
            });
        }

        // ── Fetch Data ─────────────────────────────────────────
        async function fetchSurplus(isNew = false) {
            if (STATE.isLoading) return;

            STATE.isLoading = true;

            if (isNew) {
                STATE.retryCount = 0;
                STATE.currentPage = 1;
                STATE.allProducts = [];
                DOM.productGrid.innerHTML = '';
                DOM.loadMoreContainer.classList.add('hidden');
            }

            await doFetch(isNew);
        }

        async function doFetch(isNew = false) {
            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), STATE.fetchTimeout);

            try {
                const params = new URLSearchParams({
                    radius: STATE.currentRadius,
                    page: STATE.currentPage,
                });

                if (STATE.currentCat !== 'semua') {
                    params.append('category_id', STATE.currentCat);
                }

                const response = await fetch(`/surplus-products/nearby?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    signal: controller.signal,
                });

                clearTimeout(timeoutId);

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));

                    if (response.status === 403 && errorData.code === 'LOCATION_NOT_SET') {
                        // Lokasi belum diset
                        STATE.locationReady = false;
                        showState('waiting');
                        setTimeout(() => LocationPicker.open(), 300);
                        return;
                    }

                    if (response.status === 429) {
                        throw new Error('Terlalu banyak permintaan. Coba sebentar lagi.');
                    }

                    throw new Error(errorData.message || `Server error (${response.status})`);
                }

                const data = await response.json();

                // Success!
                STATE.retryCount = 0;

                const products = data.data || [];
                STATE.hasMore = data.next_page_url != null;
                STATE.currentPage = data.current_page || 1;

                if (isNew || STATE.currentPage === 1) {
                    STATE.allProducts = products;
                } else {
                    STATE.allProducts = [...STATE.allProducts, ...products];
                }

                renderProducts(STATE.allProducts);

            } catch (error) {
                clearTimeout(timeoutId);

                if (error.name === 'AbortError') {
                    handleError('Koneksi lambat', 'Request timeout. Periksa koneksi internet kamu.');
                } else if (error.message.includes('Failed to fetch') || error.message.includes('NetworkError')) {
                    handleError('Tidak Ada Koneksi', 'Sepertinya kamu offline. Periksa koneksi internet.');
                } else {
                    handleError('Gagal Memuat', error.message || 'Terjadi kesalahan. Coba lagi nanti.');
                }
            } finally {
                STATE.isLoading = false;
                hideLoadingStates();
            }
        }

        function handleError(title, message) {
            STATE.retryCount++;

            if (STATE.retryCount <= STATE.maxRetries) {
                console.log(`Retrying... (${STATE.retryCount}/${STATE.maxRetries})`);

                setTimeout(() => {
                    doFetch(STATE.currentPage === 1);
                }, 1000 * STATE.retryCount);

                return;
            }

            DOM.errorTitle.textContent = title;
            DOM.errorMessage.textContent = message;

            showState('error');
        }

        function hideLoadingStates() {
            // Hanya hide loading jika ada data
            if (STATE.allProducts.length > 0) {
                showState('content');
            }
        }

        // ── Render Products ────────────────────────────────────
        function renderProducts(products) {
            console.log("render product", products);

            if (!products.length) {
                showState('empty');
                return;
            }

            showState('content');

            // Update result count
            DOM.resultCount.textContent = `Menampilkan ${products.length} surplus makanan`;

            // Render grid
            DOM.productGrid.innerHTML = products.map(p => renderProductCard(p)).join('');

            // Show/hide load more
            if (STATE.hasMore) {
                DOM.loadMoreContainer.classList.remove('hidden');
            } else {
                DOM.loadMoreContainer.classList.add('hidden');
            }
        }

        function renderProductCard(p) {
            const product = p.product ?? {};
            const store = product.store ?? {};
            const category = product.category ?? {};

            // Calculate discount
            const originalPrice = parseFloat(product.price ?? p.initial_price ?? 0);
            const currentPrice = parseFloat(p.discount_price ?? 0);
            const disc = originalPrice > 0 ?
                Math.round((originalPrice - currentPrice) / originalPrice * 100) :
                0;

            // Get image
            const img = product.product_img[0].img_url;

            // Distance
            const dist = p.distance_km != null ?
                parseFloat(p.distance_km).toFixed(1) + ' km' :
                '';

            // Expiry
            let expiryHTML = '';
            if (p.expired_at) {
                const expiredDate = new Date(p.expired_at);
                const now = new Date();
                const hoursLeft = Math.max(0, (expiredDate - now) / (1000 * 60 * 60));

                let expiryClass = 'badge-success';
                if (hoursLeft < 1) expiryClass = 'badge-error';
                else if (hoursLeft < 3) expiryClass = 'badge-warning';

                const timeStr = expiredDate.toLocaleTimeString('id-ID', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

                expiryHTML = `
            <div class="absolute bottom-2 left-2 badge ${expiryClass} badge-xs gap-1 text-white border-0">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                </svg>
                Sampai ${timeStr}
            </div>`;
            }

            // Discount badge
            let discBadge = '';
            if (disc >= 50) {
                discBadge = '<span class="badge badge-error badge-xs">🔥 ' + disc + '% OFF</span>';
            } else if (disc >= 30) {
                discBadge = '<span class="badge badge-warning badge-xs">⚡ ' + disc + '% OFF</span>';
            } else if (disc > 0) {
                discBadge = '<span class="badge badge-success badge-xs">' + disc + '% OFF</span>';
            }

            const originalPriceHTML =
                originalPrice > currentPrice ?
                `
                <span class="text-xs text-base-content/40 line-through">
                    Rp ${formatNumber(originalPrice)}
                </span>
                ` :
                '';

            return `
        <div class="card bg-base-100 shadow-sm border border-base-200 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 cursor-pointer group"
             onclick="openProductDetail(${p.id})">
            <figure class="relative h-36 overflow-hidden">
                <img src="${img}" 
                     alt="${escapeHTML(product.name ?? 'Surplus Product')}"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                     loading="lazy"
                     onerror="this.src='/images/placeholder-food.png'">
                
                <div class="absolute top-2 right-2 bg-warning text-warning-content text-[10px] font-bold px-2 py-1 rounded-full shadow-lg">
                    SURPLUS
                </div>
                
                ${expiryHTML}
                
                <div class="absolute bottom-0 left-0 right-0 h-1 bg-base-300">
                    <div class="h-full bg-warning transition-all" 
                         style="width: ${Math.min(100, (p.remaining_quantity / (p.quantity || 1)) * 100)}%">
                    </div>
                </div>
            </figure>
            
            <div class="card-body p-3 gap-1.5">
                <span class="text-[10px] text-base-content/50 uppercase tracking-wider">
                    ${escapeHTML(category.name ?? '')}
                </span>
                
                <h3 class="font-semibold text-sm leading-tight line-clamp-2 group-hover:text-warning transition-colors">
                    ${escapeHTML(product.name ?? 'Produk Surplus')}
                </h3>
                
                <div class="flex items-center gap-1 text-xs text-base-content/50">
                    <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span class="truncate">${escapeHTML(store.name ?? 'Toko')}</span>
                </div>
                
                <div class="flex items-baseline gap-2 mt-1">
                    <span class="text-warning font-bold text-base">
                        Rp ${formatNumber(currentPrice)}
                    </span>
                  ${originalPriceHTML}
                </div>
                
                ${discBadge}
                
                <div class="flex items-center justify-between text-[10px] text-base-content/40 mt-1 pt-2 border-t border-base-200">
                    <span class="flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                        </svg>
                        ${dist || '?'}
                    </span>
                    <span>Sisa ${p.remaining_quantity}</span>
                </div>
            </div>
        </div>
    `;
        }

        // ── Load More ──────────────────────────────────────────
        async function loadMore() {
            if (STATE.isLoading || !STATE.hasMore) return;

            STATE.isLoading = true;
            STATE.currentPage++;

            const loadMoreBtn = document.getElementById('loadMoreBtn');
            const loadMoreText = document.getElementById('loadMoreText');
            const loadMoreSpinner = document.getElementById('loadMoreSpinner');

            loadMoreBtn.disabled = true;
            loadMoreText.textContent = 'Memuat...';
            loadMoreSpinner.classList.remove('hidden');

            await doFetch();

            loadMoreBtn.disabled = false;
            loadMoreText.textContent = 'Muat Lebih Banyak';
            loadMoreSpinner.classList.add('hidden');

            // Auto-hide if no more
            if (!STATE.hasMore) {
                DOM.loadMoreContainer.classList.add('hidden');
            }
        }

        // ── Filter & Radius ────────────────────────────────────
        function setFilter(el, catId) {
            STATE.currentCat = catId;

            // Update UI
            document.querySelectorAll('#categoryChips .chip, #categoryChipsActive .chip').forEach(c => {
                c.classList.remove('chip-active');
                if (c.dataset.cat == catId) c.classList.add('chip-active');
            });

            if (STATE.locationReady) {
                STATE.currentPage = 1;
                STATE.allProducts = [];
                showState('loading');
                fetchSurplus(true);
            }
        }

        function onRadiusChange(val) {
            STATE.currentRadius = parseInt(val);

            // Update all radius displays
            document.querySelectorAll('[id^="radiusVal"]').forEach(el => {
                el.textContent = val + ' km';
            });
            document.querySelectorAll('[id^="radiusSlider"]').forEach(el => {
                el.value = val;
            });

            if (STATE.locationReady) {
                // Debounce radius change
                clearTimeout(window.radiusTimeout);
                window.radiusTimeout = setTimeout(() => {
                    STATE.currentPage = 1;
                    STATE.allProducts = [];
                    showState('loading');
                    fetchSurplus(true);
                }, 500);
            }
        }

        function expandRadius() {
            STATE.currentRadius = 20;
            onRadiusChange(20);
        }

        // ── Refresh ────────────────────────────────────────────
        function refreshData() {
            if (!STATE.locationReady) return;
            STATE.currentPage = 1;
            STATE.allProducts = [];
            STATE.retryCount = 0;
            showState('loading');
            fetchSurplus(true);
        }

        // ── Product Detail Navigation ──────────────────────────
        function openProductDetail(id) {
            // Optional: navigate to detail page
            // window.location.href = `/surplus/${id}`;
            console.log('Open product:', id);
        }

        // ── Helpers ────────────────────────────────────────────
        function formatNumber(num) {
            return Number(num).toLocaleString('id-ID');
        }

        function escapeHTML(str) {
            if (!str) return '';
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        // ── Auto-refresh setiap 5 menit ────────────────────────
        let autoRefreshInterval;
        document.addEventListener('locationReady', () => {
            if (autoRefreshInterval) clearInterval(autoRefreshInterval);
            autoRefreshInterval = setInterval(() => {
                if (STATE.locationReady && !STATE.isLoading) {
                    refreshData();
                }
            }, 5 * 60 * 1000); // 5 menit
        });

        // Cleanup on page unload
        window.addEventListener('beforeunload', () => {
            if (autoRefreshInterval) clearInterval(autoRefreshInterval);
        });
    </script>

    <style>
        .chip {
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            border: 1px solid hsl(var(--b3));
            background: hsl(var(--b1));
            font-size: 0.875rem;
            color: hsl(var(--bc) / 0.6);
            cursor: pointer;
            transition: all 0.2s;
            white-space: nowrap;
        }

        .chip:hover {
            background: hsl(var(--b2));
            color: hsl(var(--bc));
        }

        .chip-active {
            background: #f97316 !important;
            border-color: #f97316 !important;
            color: white !important;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Card hover effect */
        .card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
@endpush
