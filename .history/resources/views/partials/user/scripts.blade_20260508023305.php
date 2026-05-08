{{-- resources/views/partials/user/home-scripts.blade.php --}}
<script>
    // ──────────────────────────────────────────────────────
    // Home Page JS - Nearby Stores & Recommended Menus
    // ──────────────────────────────────────────────────────

    document.addEventListener('alpine:init', () => {

        Alpine.data('homePage', () => ({
            cartOpen: false,

            cart: [],

            get cartCount() {
                return this.cart.reduce((total, item) => total + item.qty, 0);
            },

            get totalPrice() {
                return this.cart.reduce((total, item) => {
                    return total + (item.price * item.qty);
                }, 0);
            },

            toggleCart() {
                this.cartOpen = !this.cartOpen;
            },

            addToCart(item) {
                const existing = this.cart.find(i => i.id === item.id);

                if (existing) {
                    existing.qty++;
                } else {
                    this.cart.push({
                        ...item,
                        qty: 1
                    });
                }
            },

            increaseQty(index) {
                this.cart[index].qty++;
            },

            decreaseQty(index) {
                if (this.cart[index].qty > 1) {
                    this.cart[index].qty--;
                } else {
                    this.cart.splice(index, 1);
                }
            }
        }));

    });


    window.HomeApp = (() => {
        // ── State ──────────────────────────────────────────
        const STATE = {
            locationReady: false,
            stores: [],
            menus: [],
            storesLoading: false,
            menusLoading: false,
            storesPage: 1,
            menusPage: 1,
            hasMoreStores: false,
            hasMoreMenus: false,
            radius: 10,
            retryCount: 0,
            maxRetries: 2,
            fetchTimeout: 8000,
        };

        // ── DOM Elements ──────────────────────────────────
        const DOM = {
            storesGrid: null,
            storesSkeleton: null,
            storesEmpty: null,
            storesError: null,
            storesLoadMore: null,
            menusGrid: null,
            menusSkeleton: null,
            menusEmpty: null,
            menusError: null,
            menusLoadMore: null,
            homeContent: null,
            waitingLocation: null,
        };

        // ── API Endpoints ─────────────────────────────────
        const API = {
            nearbyStores: '/user/nearby-stores',
            surplusNearby: '/surplus-products/nearby',
        };

        // ── Init DOM References ──────────────────────────
        function initDOM() {
            DOM.storesGrid = document.getElementById('nearbyStoresGrid');
            DOM.storesSkeleton = document.getElementById('storesSkeleton');
            DOM.storesEmpty = document.getElementById('storesEmpty');
            DOM.storesError = document.getElementById('storesError');
            DOM.storesLoadMore = document.getElementById('storesLoadMore');
            DOM.menusGrid = document.getElementById('recommendedMenusGrid');
            DOM.menusSkeleton = document.getElementById('menusSkeleton');
            DOM.menusEmpty = document.getElementById('menusEmpty');
            DOM.menusError = document.getElementById('menusError');
            DOM.menusLoadMore = document.getElementById('menusLoadMore');
            DOM.homeContent = document.getElementById('homeContent');
            DOM.waitingLocation = document.getElementById('homeWaitingLocation');
        }

        // ── Init ──────────────────────────────────────────
        function init() {
            initDOM();

            // Cek apakah LocationPicker sudah me-trigger locationReady
            // Jika iya, STATE.locationReady sudah true dari event listener

            // Listen for location ready dari LocationPicker
            document.addEventListener('locationReady', () => {
                if (!STATE.locationReady) {
                    STATE.locationReady = true;
                    showHomeContent();
                    loadAllData();
                }
            });

            // Listen for location updated
            document.addEventListener('locationUpdated', () => {
                STATE.locationReady = true;
                STATE.stores = [];
                STATE.menus = [];
                STATE.retryCount = 0;
                showHomeContent();
                loadAllData();
            });

            // 🔥 PENTING: Langsung cek status lokasi ke server
            // Ini fallback kalau LocationPicker belum di-init
            checkLocationAndLoad();
        }

        async function checkLocationAndLoad() {
            try {
                const response = await fetch('/user/location/status', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCSRF(),
                    }
                });
                const data = await response.json();

                console.log('Location status:', data);

                if (data.has_location) {
                    STATE.locationReady = true;
                    showHomeContent();
                    loadAllData();
                } else {
                    // Belum ada lokasi → tampilkan waiting
                    showWaitingLocation();
                }
            } catch (error) {
                console.error('Gagal cek lokasi:', error);
                showWaitingLocation();
            }
        }

        function showHomeContent() {
            console.log('Showing home content');
            if (DOM.waitingLocation) DOM.waitingLocation.classList.add('hidden');
            if (DOM.homeContent) DOM.homeContent.classList.remove('hidden');
        }

        function showWaitingLocation() {
            console.log('Showing waiting location');
            if (DOM.waitingLocation) DOM.waitingLocation.classList.remove('hidden');
            if (DOM.homeContent) DOM.homeContent.classList.add('hidden');

            // Auto-open location picker after short delay
            setTimeout(() => {
                if (typeof LocationPicker !== 'undefined' && LocationPicker.open) {
                    LocationPicker.open();
                }
            }, 500);
        }

        // ── Load All Data ────────────────────────────────
        function loadAllData() {
            console.log('Loading all data...');
            fetchNearbyStores();
            fetchRecommendedMenus();
        }

        // ── Fetch Nearby Stores ──────────────────────────
        async function fetchNearbyStores(isLoadMore = false) {
            if (STATE.storesLoading) return;

            STATE.storesLoading = true;

            if (!isLoadMore) {
                STATE.storesPage = 1;
                showSkeleton('stores');
            }

            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), STATE.fetchTimeout);

            try {
                const params = new URLSearchParams({
                    radius: STATE.radius,
                    page: STATE.storesPage,
                    limit: 8,
                });

                console.log('Fetching stores...', params.toString());

                const response = await fetch(`${API.nearbyStores}?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCSRF(),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    signal: controller.signal,
                });

                clearTimeout(timeoutId);

                console.log('Stores response status:', response.status);

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));

                    if (response.status === 403 && errorData.code === 'LOCATION_NOT_SET') {
                        STATE.locationReady = false;
                        showWaitingLocation();
                        return;
                    }

                    throw new Error(errorData.message || `Server error (${response.status})`);
                }

                const data = await response.json();
                console.log('Stores data:', data);

                const stores = (data.data || []).map(store => formatStoreData(store));

                if (isLoadMore) {
                    STATE.stores = [...STATE.stores, ...stores];
                } else {
                    STATE.stores = stores;
                }

                STATE.hasMoreStores = data.next_page_url != null;
                STATE.storesPage = data.current_page || 1;
                STATE.retryCount = 0;

                renderStores(STATE.stores);
                hideSkeleton('stores');

            } catch (error) {
                clearTimeout(timeoutId);
                console.error('Fetch stores error:', error);

                if (!isLoadMore) {
                    handleStoreError(error);
                } else {
                    showToast('Gagal memuat lebih banyak toko', 'error');
                }
            } finally {
                STATE.storesLoading = false;
            }
        }

        function handleStoreError(error) {
            STATE.retryCount++;

            if (STATE.retryCount <= STATE.maxRetries) {
                console.log(`Retrying stores... (${STATE.retryCount}/${STATE.maxRetries})`);
                setTimeout(() => {
                    fetchNearbyStores(false);
                }, 1000 * STATE.retryCount);
                return;
            }

            hideSkeleton('stores');

            if (DOM.storesError) {
                DOM.storesError.classList.remove('hidden');
                const errorMsg = DOM.storesError.querySelector('.error-message');
                if (errorMsg) {
                    if (error.name === 'AbortError') {
                        errorMsg.textContent = 'Koneksi lambat. Periksa internet kamu.';
                    } else if (error.message.includes('Failed to fetch')) {
                        errorMsg.textContent = 'Sepertinya kamu offline.';
                    } else {
                        errorMsg.textContent = error.message || 'Gagal memuat toko terdekat.';
                    }
                }
            }
        }

        function formatStoreData(store) {
            return {
                id: store.id,
                name: store.name || 'Toko',
                image: store.image_url || store.logo || '/images/placeholder-store.png',
                distance: store.distance_km ? `${parseFloat(store.distance_km).toFixed(1)} km` : '',
                rating: parseFloat(store.rating || store.average_rating || 0).toFixed(1),
                category: store.category?.name || store.categories?.[0]?.name || 'Restaurant',
                time: store.opening_hours || store.operational_hours || '09:00 - 21:00',
                status: store.is_open ? 'Open' : 'Closed',
                statusClass: store.is_open ? 'badge-success' : 'badge-error',
                promo: store.promo_text || store.active_promo || null,
            };
        }

        function renderStores(stores) {
            if (!DOM.storesGrid) return;

            if (!stores.length) {
                if (DOM.storesEmpty) DOM.storesEmpty.classList.remove('hidden');
                DOM.storesGrid.innerHTML = '';
                if (DOM.storesLoadMore) DOM.storesLoadMore.classList.add('hidden');
                return;
            }

            if (DOM.storesEmpty) DOM.storesEmpty.classList.add('hidden');

            DOM.storesGrid.innerHTML = stores.map(store => `
                <div class="card bg-base-100 border border-base-200 shadow-sm hover:shadow-md transition-all overflow-hidden group cursor-pointer"
                     onclick="navigateToStore(${store.id})">
                    <figure class="relative h-40 lg:h-48">
                        <img src="${store.image}" 
                             alt="${escapeHTML(store.name)}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                             loading="lazy"
                             onerror="this.src='/images/placeholder-store.png'" />
                        
                        <div class="absolute top-3 left-3 badge badge-ghost bg-white/90 text-xs font-semibold text-warning">
                            📍 ${store.distance}
                        </div>
                        
                        ${store.promo ? `
                            <div class="absolute bottom-3 left-3 badge badge-error text-white text-xs font-semibold border-0">
                                ${escapeHTML(store.promo)}
                            </div>
                        ` : ''}
                    </figure>
                    
                    <div class="card-body p-4">
                        <div class="flex items-start justify-between mb-1">
                            <h3 class="card-title text-sm">${escapeHTML(store.name)}</h3>
                            <div class="flex items-center gap-1 text-xs">
                                <span class="text-yellow-500">⭐</span>
                                <span class="font-medium">${store.rating}</span>
                            </div>
                        </div>
                        
                        <p class="text-xs text-base-content/60 mb-3">${escapeHTML(store.category)}</p>
                        
                        <div class="card-actions justify-between items-center">
                            <span class="text-xs text-base-content/50">🕐 ${store.time}</span>
                            <span class="badge ${store.statusClass} badge-sm text-xs">${store.status}</span>
                        </div>
                    </div>
                </div>
            `).join('');

            if (DOM.storesLoadMore) {
                if (STATE.hasMoreStores) {
                    DOM.storesLoadMore.classList.remove('hidden');
                } else {
                    DOM.storesLoadMore.classList.add('hidden');
                }
            }
        }

        // ── Fetch Recommended Menus ──────────────────────
        async function fetchRecommendedMenus(isLoadMore = false) {
            if (STATE.menusLoading) return;

            STATE.menusLoading = true;

            if (!isLoadMore) {
                STATE.menusPage = 1;
                showSkeleton('menus');
            }

            const controller = new AbortController();
            const timeoutId = setTimeout(() => controller.abort(), STATE.fetchTimeout);

            try {
                const params = new URLSearchParams({
                    radius: STATE.radius,
                    page: STATE.menusPage,
                    limit: 10,
                });

                const response = await fetch(`${API.surplusNearby}?${params}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCSRF(),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    signal: controller.signal,
                });

                clearTimeout(timeoutId);

                console.log('Menus response status:', response.status);

                if (!response.ok) {
                    const errorData = await response.json().catch(() => ({}));

                    if (response.status === 403 && errorData.code === 'LOCATION_NOT_SET') {
                        STATE.locationReady = false;
                        showWaitingLocation();
                        return;
                    }

                    throw new Error(errorData.message || `Server error (${response.status})`);
                }

                const data = await response.json();
                console.log('Menus data:', data);

                const menus = (data.data || []).map(item => formatMenuData(item));

                if (isLoadMore) {
                    STATE.menus = [...STATE.menus, ...menus];
                } else {
                    STATE.menus = menus;
                }

                STATE.hasMoreMenus = data.next_page_url != null;
                STATE.menusPage = data.current_page || 1;

                renderMenus(STATE.menus);
                hideSkeleton('menus');

            } catch (error) {
                clearTimeout(timeoutId);
                console.error('Fetch menus error:', error);

                if (!isLoadMore) {
                    handleMenuError(error);
                } else {
                    showToast('Gagal memuat lebih banyak menu', 'error');
                }
            } finally {
                STATE.menusLoading = false;
            }
        }

        function handleMenuError(error) {
            hideSkeleton('menus');

            if (DOM.menusError) {
                DOM.menusError.classList.remove('hidden');
                const errorMsg = DOM.menusError.querySelector('.error-message');
                if (errorMsg) {
                    if (error.name === 'AbortError') {
                        errorMsg.textContent = 'Koneksi lambat. Coba lagi.';
                    } else if (error.message.includes('Failed to fetch')) {
                        errorMsg.textContent = 'Kamu sedang offline.';
                    } else {
                        errorMsg.textContent = error.message || 'Gagal memuat menu rekomendasi.';
                    }
                }
            }
        }

        function formatMenuData(item) {
            const product = item.product || {};

            return {
                id: item.id,
                surplusId: item.id,
                name: product.name || 'Menu Surplus',
                restaurant: product.store?.name || 'Restaurant',
                image: product.product_img[0]?.img_url,
                price: formatCurrency(item.discount_price || item.current_price || 0),
                originalPrice: product.price && product.price > (item.discount_price || item.current_price) ?
                    formatCurrency(product.price) : null,
                discount: item.discount_percentage ?
                    `${Math.round(item.discount_percentage)}% OFF` : (product.price && item.discount_price &&
                        product.price > item.discount_price ?
                        `${Math.round((product.price - item.discount_price) / product.price * 100)}% OFF` :
                        null),
                rating: parseFloat(product.store?.rating || product.store?.average_rating || 0).toFixed(1),
                distance: item.distance_km ? `${parseFloat(item.distance_km).toFixed(1)} km` : '',
                remainingQuantity: item.remaining_quantity || 0,
            };
        }

        function renderMenus(menus) {
            if (!DOM.menusGrid) return;

            console.log("render menus", menus);


            if (!menus.length) {
                if (DOM.menusEmpty) DOM.menusEmpty.classList.remove('hidden');
                DOM.menusGrid.innerHTML = '';
                if (DOM.menusLoadMore) DOM.menusLoadMore.classList.add('hidden');
                return;
            }

            if (DOM.menusEmpty) DOM.menusEmpty.classList.add('hidden');

            DOM.menusGrid.innerHTML = menus.map(menu => `
                <div class="card bg-base-100 border border-base-200 shadow-sm hover:shadow-md transition-all overflow-hidden group cursor-pointer"
                     onclick="navigateToSurplus(${menu.surplusId})">
                    <figure class="relative h-36 lg:h-44">
                        <img src="${menu.image}" 
                             alt="${escapeHTML(menu.name)}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                             loading="lazy"
                             onerror="this.src='/images/placeholder-food.png'" />
                        
                        <button onclick="event.stopPropagation(); addToCartGlobal(${menu.id}, {id: ${menu.id}, name: '${escapeHTML(menu.name)}', price: ${menu.discount_price || 0}, image: '${menu.image}', storeName: '${escapeHTML(menu.restaurant)}'})"
                            class="absolute top-2 right-2 btn btn-circle btn-sm bg-white shadow-md text-warning hover:bg-warning hover:text-warning-content border-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </button>
                    </figure>
                    
                    <div class="card-body p-3">
                        <h3 class="card-title text-sm mb-1 truncate">${escapeHTML(menu.name)}</h3>
                        <p class="text-xs text-base-content/60 mb-2 truncate">📍 ${escapeHTML(menu.restaurant)}</p>
                        
                        <div class="card-actions justify-between items-center">
                            <div>
                                <span class="text-sm font-bold text-warning">${menu.price}</span>
                                ${menu.originalPrice ? `
                                    <span class="text-xs text-base-content/30 line-through ml-1">${menu.originalPrice}</span>
                                ` : ''}
                            </div>
                            <div class="flex items-center gap-1 text-xs">
                                <span class="text-yellow-500">⭐</span>
                                <span class="text-base-content/50">${menu.rating}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');

            if (DOM.menusLoadMore) {
                if (STATE.hasMoreMenus) {
                    DOM.menusLoadMore.classList.remove('hidden');
                } else {
                    DOM.menusLoadMore.classList.add('hidden');
                }
            }
        }

        // ── Skeleton Helpers ──────────────────────────────
        function showSkeleton(type) {
            if (type === 'stores') {
                if (DOM.storesSkeleton) DOM.storesSkeleton.classList.remove('hidden');
                if (DOM.storesGrid) DOM.storesGrid.innerHTML = '';
                if (DOM.storesEmpty) DOM.storesEmpty.classList.add('hidden');
                if (DOM.storesError) DOM.storesError.classList.add('hidden');
            } else if (type === 'menus') {
                if (DOM.menusSkeleton) DOM.menusSkeleton.classList.remove('hidden');
                if (DOM.menusGrid) DOM.menusGrid.innerHTML = '';
                if (DOM.menusEmpty) DOM.menusEmpty.classList.add('hidden');
                if (DOM.menusError) DOM.menusError.classList.add('hidden');
            }
        }

        function hideSkeleton(type) {
            if (type === 'stores' && DOM.storesSkeleton) {
                DOM.storesSkeleton.classList.add('hidden');
            }
            if (type === 'menus' && DOM.menusSkeleton) {
                DOM.menusSkeleton.classList.add('hidden');
            }
        }

        // ── Load More ─────────────────────────────────────
        function loadMoreStores() {
            if (STATE.storesLoading || !STATE.hasMoreStores) return;
            STATE.storesPage++;
            fetchNearbyStores(true);
        }

        function loadMoreMenus() {
            if (STATE.menusLoading || !STATE.hasMoreMenus) return;
            STATE.menusPage++;
            fetchRecommendedMenus(true);
        }

        // ── Refresh ───────────────────────────────────────
        function refreshAll() {
            STATE.stores = [];
            STATE.menus = [];
            STATE.retryCount = 0;
            showSkeleton('stores');
            showSkeleton('menus');
            loadAllData();
        }

        // ── Helpers ───────────────────────────────────────
        function formatCurrency(amount) {
            return 'Rp ' + Number(amount).toLocaleString('id-ID');
        }

        function escapeHTML(str) {
            if (!str) return '';
            const div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        function getCSRF() {
            return document.querySelector('meta[name="csrf-token"]')?.content || '';
        }

        function showToast(msg, type = 'info') {
            const colors = {
                error: 'alert-error',
                warning: 'alert-warning',
                info: 'alert-info',
                success: 'alert-success'
            };
            const toast = document.createElement('div');
            toast.className = 'toast toast-top toast-end z-[9999]';
            toast.innerHTML = `<div class="alert ${colors[type]} text-sm py-2 px-4 shadow-lg">${msg}</div>`;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        // ── Retry buttons ────────────────────────────────
        function retryStores() {
            if (DOM.storesError) DOM.storesError.classList.add('hidden');
            STATE.retryCount = 0;
            showSkeleton('stores');
            fetchNearbyStores();
        }

        function retryMenus() {
            if (DOM.menusError) DOM.menusError.classList.add('hidden');
            showSkeleton('menus');
            fetchRecommendedMenus();
        }

        // ── Public API ────────────────────────────────────
        return {
            init,
            loadAllData,
            refreshAll,
            loadMoreStores,
            loadMoreMenus,
            retryStores,
            retryMenus,
        };
    })();

    // 🔥 Initialize
    (function() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                console.log('DOM ready, initializing HomeApp...');
                if (window.HomeApp && window.HomeApp.init) {
                    window.HomeApp.init();
                }
            });
        } else {
            console.log('DOM already ready, initializing HomeApp...');
            if (window.HomeApp && window.HomeApp.init) {
                window.HomeApp.init();
            }
        }
    })();

    // Auto-refresh setiap 5 menit
    (function() {
        let homeRefreshInterval;

        function startAutoRefresh() {
            if (homeRefreshInterval) clearInterval(homeRefreshInterval);
            homeRefreshInterval = setInterval(() => {
                if (window.HomeApp && window.HomeApp.refreshAll) {
                    window.HomeApp.refreshAll();
                }
            }, 5 * 60 * 1000);
        }

        document.addEventListener('locationReady', startAutoRefresh);

        window.addEventListener('beforeunload', () => {
            if (homeRefreshInterval) clearInterval(homeRefreshInterval);
        });
    })();
</script>
