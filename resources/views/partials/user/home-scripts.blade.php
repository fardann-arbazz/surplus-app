<script>
    // ──────────────────────────────────────────────────────
    // Home Page JS - Nearby Stores & Recommended Menus
    // ──────────────────────────────────────────────────────

    const HomeApp = (function() {
        // ── State ──────────────────────────────────────────
        var STATE = {
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
        var DOM = {
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
        var API = {
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

        // ── Helper Functions ─────────────────────────────
        function getCSRF() {
            var meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.content : '';
        }

        function formatCurrency(amount) {
            return 'Rp ' + Number(amount || 0).toLocaleString('id-ID');
        }

        function escapeHTML(str) {
            if (!str) return '';
            var div = document.createElement('div');
            div.textContent = str;
            return div.innerHTML;
        }

        // ── Init ──────────────────────────────────────────
        function init() {
            initDOM();

            // Listen for location ready dari LocationPicker
            document.addEventListener('locationReady', function() {
                if (!STATE.locationReady) {
                    STATE.locationReady = true;
                    showHomeContent();
                    loadAllData();
                }
            });

            // Listen for location updated
            document.addEventListener('locationUpdated', function() {
                STATE.locationReady = true;
                STATE.stores = [];
                STATE.menus = [];
                STATE.retryCount = 0;
                showHomeContent();
                loadAllData();
            });

            // Cek status lokasi ke server
            // Tetap simpan listener sebagai fallback
            document.addEventListener('DOMContentLoaded', function() {
                if (!STATE.locationReady) {
                    checkLocationAndLoad();
                }
            });
        }

        function checkLocationAndLoad() {
            fetch('/user/location/status', {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCSRF(),
                    }
                })
                .then(function(r) {
                    return r.json();
                })
                .then(function(data) {
                    console.log('Location status:', data);

                    if (data.has_location) {
                        STATE.locationReady = true;
                        showHomeContent();
                        loadAllData();
                    } else {
                        showWaitingLocation();
                    }
                })
                .catch(function(error) {
                    console.error('Gagal cek lokasi:', error);
                    // FALLBACK: Jika fetch gagal, coba gunakan event
                    setTimeout(function() {
                        if (!STATE.locationReady && typeof LocationPicker !== 'undefined') {
                            LocationPicker.open();
                        }
                    }, 1000);
                });
        }

        function showHomeContent() {
            if (DOM.waitingLocation) DOM.waitingLocation.classList.add('hidden');
            if (DOM.homeContent) DOM.homeContent.classList.remove('hidden');
        }

        function showWaitingLocation() {
            if (DOM.waitingLocation) DOM.waitingLocation.classList.remove('hidden');
            if (DOM.homeContent) DOM.homeContent.classList.add('hidden');

            setTimeout(function() {
                if (typeof LocationPicker !== 'undefined' && LocationPicker.open) {
                    LocationPicker.open();
                }
            }, 500);
        }

        // ── Load All Data ────────────────────────────────
        function loadAllData() {
            fetchNearbyStores(false);
            fetchRecommendedMenus(false);
        }

        // ── Fetch Nearby Stores ──────────────────────────
        function fetchNearbyStores(isLoadMore) {
            if (STATE.storesLoading) return;

            STATE.storesLoading = true;
            isLoadMore = isLoadMore || false;

            if (!isLoadMore) {
                STATE.storesPage = 1;
                showSkeleton('stores');
            }

            var controller = new AbortController();
            var timeoutId = setTimeout(function() {
                controller.abort();
            }, STATE.fetchTimeout);

            var params = new URLSearchParams({
                radius: STATE.radius,
                page: STATE.storesPage,
                limit: 8,
            });

            fetch(API.nearbyStores + '?' + params.toString(), {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCSRF(),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    signal: controller.signal,
                })
                .then(function(response) {
                    clearTimeout(timeoutId);
                    console.log('Stores response status:', response.status);

                    if (!response.ok) {
                        return response.json().then(function(errorData) {
                            // 403 = location not set atau auth issue
                            if (response.status === 403) {
                                if (errorData.code === 'LOCATION_NOT_SET') {
                                    STATE.locationReady = false;
                                    showWaitingLocation();
                                    return null; // Stop disini
                                }

                                // Auth issue - jangan block, return empty
                                console.warn('Stores fetch forbidden');
                                return {
                                    data: []
                                };
                            }
                            throw new Error(errorData.message || 'Server error (' + response
                                .status + ')');
                        });
                    }
                    return response.json();
                })
                .then(function(data) {
                    if (data === null) return; // Stopped earlier

                    console.log('Stores data:', data);

                    var stores = (data.data || []).map(function(store) {
                        return formatStoreData(store);
                    });

                    if (isLoadMore) {
                        STATE.stores = STATE.stores.concat(stores);
                    } else {
                        STATE.stores = stores;
                    }

                    STATE.hasMoreStores = data.next_page_url != null;
                    STATE.storesPage = data.current_page || 1;
                    STATE.retryCount = 0;

                    renderStores(STATE.stores);
                    hideSkeleton('stores');
                })
                .catch(function(error) {
                    clearTimeout(timeoutId);
                    console.error('Fetch stores error:', error);

                    if (!isLoadMore) {
                        hideSkeleton('stores');
                        if (DOM.storesGrid) DOM.storesGrid.innerHTML = '';
                        if (DOM.storesEmpty) DOM.storesEmpty.classList.remove('hidden');
                    }
                })
                .finally(function() {
                    STATE.storesLoading = false;
                });
        }

        function formatStoreData(store) {
            return {
                id: store.id,
                name: store.name || 'Toko',
                image: store.image_url || store.logo || '/images/placeholder-store.png',
                address: store.address,
                distance: store.distance_km ? parseFloat(store.distance_km).toFixed(1) + ' km' : '',
                rating: parseFloat(store.rating || store.average_rating || 0).toFixed(1),
                category: (store.category && store.category.name) ? store.category.name : 'Restaurant',
                time: store.opening_hours || '09:00 - 21:00',
                status: store.is_online ? 'Open' : 'Closed',
                statusClass: store.is_online ? 'badge-success' : 'badge-error',
                promo: store.promo_text || null,
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

            var html = '';
            for (var i = 0; i < stores.length; i++) {
                var store = stores[i];
                var name = escapeHTML(store.name);
                var cat = escapeHTML(store.category);
                var promoHTML = store.promo ?
                    '<div class="absolute bottom-3 left-3 badge badge-error text-white text-xs font-semibold border-0">' +
                    escapeHTML(store.promo) + '</div>' : '';

                html += `
                <div class="card bg-base-100 border border-base-200 shadow-sm hover:shadow-md transition-all overflow-hidden group cursor-pointer"
                     onclick="navigateToStore(${store.id})">
                    <figure class="relative h-40 lg:h-48">
                        <img src="${store.image}" 
                             alt="${name}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                             loading="lazy"
                             onerror="this.src='/images/placeholder-store.png'" />
                      
                        ${promoHTML}
                    </figure>
                    <div class="card-body p-4">
                        <div class="flex items-start justify-between mb-1">
                            <h3 class="card-title text-sm">${name}</h3>
                            <div class="flex items-center gap-1 text-xs">
                                <span class="text-yellow-500">⭐</span>
                                <span class="font-medium">${store.rating}</span>
                            </div>
                        </div>
                        <p class="text-xs text-base-content/60">${cat}</p>
                        <hr class="border border-slate-50 my-1"/>
                        <div class="card-actions justify-between items-center">
                            <span class="text-xs text-base-content/50 truncate">${store.address}</span>
                            <span class="badge ${store.statusClass} badge-sm text-xs">${store.status}</span>
                        </div>
                    </div>
                </div>
            `;
            }

            DOM.storesGrid.innerHTML = html;

            if (DOM.storesLoadMore) {
                if (STATE.hasMoreStores) {
                    DOM.storesLoadMore.classList.remove('hidden');
                } else {
                    DOM.storesLoadMore.classList.add('hidden');
                }
            }
        }

        // ── Fetch Recommended Menus ──────────────────────
        function fetchRecommendedMenus(isLoadMore) {
            if (STATE.menusLoading) return;

            STATE.menusLoading = true;
            isLoadMore = isLoadMore || false;

            if (!isLoadMore) {
                STATE.menusPage = 1;
                showSkeleton('menus');
            }

            var controller = new AbortController();
            var timeoutId = setTimeout(function() {
                controller.abort();
            }, STATE.fetchTimeout);

            var params = new URLSearchParams({
                radius: STATE.radius,
                page: STATE.menusPage,
                limit: 10,
            });

            console.log('Fetching menus...', params.toString());

            fetch(API.surplusNearby + '?' + params.toString(), {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': getCSRF(),
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    signal: controller.signal,
                })
                .then(function(response) {
                    clearTimeout(timeoutId);
                    console.log('Menus response status:', response.status);

                    if (!response.ok) {
                        return response.json().then(function(errorData) {
                            if (response.status === 403 && errorData.code === 'LOCATION_NOT_SET') {
                                STATE.locationReady = false;
                                showWaitingLocation();
                                return null;
                            }
                            throw new Error(errorData.message || 'Server error (' + response
                                .status + ')');
                        });
                    }
                    return response.json();
                })
                .then(function(data) {
                    if (data === null) return;

                    console.log('Menus data:', data);

                    var menus = (data.data || []).map(function(item) {
                        return formatMenuData(item);
                    });

                    if (isLoadMore) {
                        STATE.menus = STATE.menus.concat(menus);
                    } else {
                        STATE.menus = menus;
                    }

                    STATE.hasMoreMenus = data.next_page_url != null;
                    STATE.menusPage = data.current_page || 1;

                    console.log("menus state", STATE.menus);


                    renderMenus(STATE.menus);
                    hideSkeleton('menus');
                })
                .catch(function(error) {
                    clearTimeout(timeoutId);
                    console.error('Fetch menus error:', error);

                    if (!isLoadMore) {
                        hideSkeleton('menus');
                        if (DOM.menusGrid) DOM.menusGrid.innerHTML = '';
                        if (DOM.menusEmpty) DOM.menusEmpty.classList.remove('hidden');
                    }
                })
                .finally(function() {
                    STATE.menusLoading = false;
                });
        }

        function formatMenuData(item) {
            if (!item) return null;

            console.log("iem format menu data", item);

            var product = item.product || {};
            var store = product.store || {};
            var productImg = product.product_img || [];

            var discountPrice = parseFloat(item.discount_price || 0);
            var originalPrice = parseFloat(product.price || 0);
            var discount = null;

            if (originalPrice > 0 && discountPrice > 0 && originalPrice > discountPrice) {
                discount = Math.round((originalPrice - discountPrice) / originalPrice * 100) + '% OFF';
            }

            var image = '/images/placeholder-food.png';
            if (productImg.length > 0 && productImg[0].img_url) {
                image = productImg[0].img_url;
            }

            return {
                id: item.id,
                surplusId: item.id,
                name: product.name || 'Menu Surplus',
                restaurant: store.name || 'Restaurant',
                image: image,
                price: formatCurrency(discountPrice),
                originalPrice: originalPrice > discountPrice ? formatCurrency(originalPrice) : null,
                discount: discount,
                rating: parseFloat(store.rating || store.average_rating || 0).toFixed(1),
                distance: item.distance_km ? parseFloat(item.distance_km).toFixed(1) + ' km' : '',
                remainingQuantity: item.remaining_quantity || 0,
                rawDiscountPrice: discountPrice,
                rawOriginalPrice: originalPrice,
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

            var html = '';
            for (var i = 0; i < menus.length; i++) {
                var menu = menus[i];
                if (!menu) continue;

                var name = escapeHTML(menu.name);
                var restaurant = escapeHTML(menu.restaurant);
                var originalPriceHTML = menu.originalPrice ?
                    '<span class="text-xs text-base-content/30 line-through ml-1">' + menu.originalPrice +
                    '</span>' :
                    '';
                var discountHTML = menu.discount ?
                    '<div class="absolute bottom-2 left-2 badge badge-error text-white text-xs font-semibold border-0">🔥 ' +
                    menu.discount + '</div>' :
                    '';

                html += `
                <div class="card bg-base-100 border border-base-200 shadow-sm hover:shadow-md transition-all overflow-hidden group cursor-pointer"
                     onclick="navigateToSurplus(${menu.id})">
                    <figure class="relative h-36 lg:h-44">
                        <img src="${menu.image}" 
                             alt="${name}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                             loading="lazy"
                             onerror="this.src='/images/placeholder-food.png'" />
                        ${discountHTML}
                    </figure>
                    <div class="card-body p-3">
                        <h3 class="card-title text-sm mb-1 truncate">${name}</h3>
                        <p class="text-xs text-base-content/60 mb-2 truncate">📍 ${restaurant}</p>
                        <div class="card-actions justify-between items-center">
                            <divore.category.name) ? store.category.name : 'Restaurant',
                time: store.opening_hours || '09:00 - 21:00',
                status: store.is_online ? 'Open' : 'Closed',
                statusClass: store.is_online ? 'badge-success' : 'badge-error',
                promo: store.promo_text || null,
            };
        }

        function renderStores(stores) {
            if (!DOM.storesGrid) return;>
                                <span class="text-sm font-bold text-warning">${menu.price}</span>
                                ${originalPriceHTML}
                            </divore.category.name>
                            <div class="flex items-center gap-1 text-xs">
                                <span class="text-yellow-500">⭐</span>
                                <span class="text-base-content/50">${menu.rating}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            }

            DOM.menusGrid.innerHTML = html;

            if (DOM.menusLoadMore) {
                if (STATE.hasMoreMenus) {
                    DOM.menusLoadMore.classList.remove('hidden');
                } else {
                    DOM.menusLoadMore.classList.add('hidden');
                }
            }
        }

        // ── Surplus Detail ──────────────────────────────
        window.navigateToSurplus = function(id) {
            window.location.href = `/surplus/${id}`;
        };

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

        // ── Retry buttons ────────────────────────────────
        function retryStores() {
            if (DOM.storesError) DOM.storesError.classList.add('hidden');
            STATE.retryCount = 0;
            showSkeleton('stores');
            fetchNearbyStores(false);
        }

        function retryMenus() {
            if (DOM.menusError) DOM.menusError.classList.add('hidden');
            showSkeleton('menus');
            fetchRecommendedMenus(false);
        }

        // ── Public API ────────────────────────────────────
        return {
            init: init,
            loadAllData: loadAllData,
            refreshAll: refreshAll,
            loadMoreStores: loadMoreStores,
            loadMoreMenus: loadMoreMenus,
            retryStores: retryStores,
            retryMenus: retryMenus,
        };
    })();

    // Initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM ready, initializing HomeApp...');
            HomeApp.init();
        });
    } else {
        console.log('DOM already ready, initializing HomeApp...');
        HomeApp.init();
    }

    // Auto-refresh setiap 5 menit
    var homeRefreshInterval;
    document.addEventListener('locationReady', function() {
        if (homeRefreshInterval) clearInterval(homeRefreshInterval);
        homeRefreshInterval = setInterval(function() {
            HomeApp.refreshAll();
        }, 5 * 60 * 1000);
    });

    window.addEventListener('beforeunload', function() {
        if (homeRefreshInterval) clearInterval(homeRefreshInterval);
    });
</script>
