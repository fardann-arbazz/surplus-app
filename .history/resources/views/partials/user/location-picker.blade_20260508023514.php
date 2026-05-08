{{-- Trigger button di topbar --}}
<button id="locationTrigger" onclick="LocationPicker.open()"
    class="flex items-center gap-2 bg-base-200 border border-base-200 rounded-lg px-3 py-1.5 text-xs text-base-content/60 hover:bg-base-300 transition-colors"
    style="display: none;">
    <span class="w-2 h-2 rounded-full bg-success" id="locDot"></span>
    <span id="locText" class="max-w-35 truncate">Lokasi tersimpan</span>
    <svg class="w-3 h-3 opacity-50 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
    </svg>
</button>

{{-- Full-Screen Modal --}}
<div id="locationModal" class="fixed inset-0 z-50 hidden items-center justify-center"
    style="background: rgba(0,0,0,0.5); backdrop-filter: blur(4px);">

    {{-- Desktop Container --}}
    <div
        class="w-full h-full lg:w-225 lg:h-150 lg:rounded-2xl lg:shadow-2xl bg-base-100 flex flex-col lg:flex-row overflow-hidden">

        {{-- LEFT PANEL - Search & Address Info --}}
        <div
            class="w-full lg:w-100 lg:min-w-100 flex flex-col bg-base-100 border-b lg:border-b-0 lg:border-r border-base-200">

            {{-- Header --}}
            <div class="shrink-0 px-5 pt-12 lg:pt-6 pb-4">
                <div class="flex items-center justify-between mb-3">
                    <button onclick="LocationPicker.close()" id="modalCloseBtn"
                        class="btn btn-ghost btn-sm btn-circle lg:hidden">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </button>
                    <h2 class="text-xl font-bold flex-1 lg:text-center">Pilih Lokasi</h2>
                    <button onclick="LocationPicker.close()" id="modalCloseBtnDesktop"
                        class="hidden lg:flex btn btn-ghost btn-sm btn-circle">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <p class="text-sm text-base-content/60 lg:text-center">Cari alamat atau geser peta untuk menandai lokasi
                </p>
            </div>

            {{-- Search Bar --}}
            <div class="shrink-0 px-5 pb-4">
                <div class="relative">
                    <div
                        class="flex items-center gap-3 bg-base-200 rounded-xl px-4 py-3.5 ring-1 ring-transparent focus-within:ring-warning focus-within:ring-2 focus-within:bg-base-100 transition-all">
                        <svg class="w-5 h-5 text-base-content/40 shrink-0" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" id="locationSearchInput" placeholder="Cari alamat, jalan, atau area..."
                            class="bg-transparent flex-1 text-sm outline-none placeholder:text-base-content/40"
                            autocomplete="off">
                        <button id="clearSearchBtn" onclick="LocationPicker.clearSearch()"
                            class="hidden shrink-0 w-6 h-6 rounded-full bg-base-300 hover:bg-base-content/20 flex items-center justify-center transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Search Suggestions - Positioned below search bar --}}
                    <div id="searchSuggestions"
                        class="absolute top-full left-0 right-0 mt-2 bg-base-100 rounded-xl shadow-2xl border border-base-200 max-h-64 overflow-y-auto hidden z-30 divide-y divide-base-200">
                    </div>
                </div>
            </div>

            {{-- Divider --}}
            <div class="border-t border-base-200 mx-5"></div>

            {{-- Address Preview --}}
            <div class="shrink-0 px-5 py-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-warning/10 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 text-warning" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs text-base-content/50 mb-1">Lokasi Pengantaran</p>
                        <p class="text-sm font-semibold leading-relaxed line-clamp-2" id="addressPreview">
                            <span class="text-base-content/40">Memuat alamat...</span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Current Location Button --}}
            <div class="shrink-0 px-5 pb-3">
                <button onclick="LocationPicker.useGPS()" id="gpsBtn"
                    class="btn btn-outline btn-sm w-full gap-2 border-base-300 hover:bg-base-200">
                    <svg class="w-4 h-4 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="3" />
                        <path d="M12 2v2M12 20v2M2 12h2M20 12h2" />
                    </svg>
                    <span id="gpsBtnText">Gunakan Lokasi Saya Saat Ini</span>
                    <span class="loading loading-spinner loading-xs hidden" id="gpsBtnSpinner"></span>
                </button>
            </div>

            {{-- Info Text --}}
            <div class="shrink-0 px-5 pb-4">
                <div class="flex items-start gap-2 text-xs text-base-content/50 bg-base-200 rounded-lg p-3">
                    <svg class="w-4 h-4 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10" />
                        <path d="M12 16v-4M12 8h.01" />
                    </svg>
                    <span>Geser peta untuk menyesuaikan titik lokasi secara manual</span>
                </div>
            </div>

            {{-- Spacer --}}
            <div class="flex-1 hidden lg:block"></div>

            {{-- Action Buttons --}}
            <div class="shrink-0 px-5 py-4 border-t border-base-200 bg-base-100">
                <button id="confirmLocationBtn" onclick="LocationPicker.confirm()"
                    class="btn btn-warning w-full gap-2 text-base font-semibold h-12">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Konfirmasi Lokasi
                </button>
                <button id="cancelLocationBtn" onclick="LocationPicker.close()"
                    class="btn btn-ghost w-full text-sm mt-2 hidden">
                    Kembali
                </button>
            </div>
        </div>

        {{-- RIGHT PANEL - Map --}}
        <div class="flex-1 relative min-h-75 lg:min-h-0 bg-base-300">
            <div id="locationMap" class="w-full h-full"></div>

            {{-- Center Pin --}}
            <div
                class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-full z-1000 pointer-events-none">
                <div class="custom-pin">
                    <div class="custom-pin-inner"></div>
                    <div class="custom-pin-shadow"></div>
                </div>
            </div>

            {{-- Loading Overlay --}}
            <div id="mapLoadingOverlay"
                class="absolute inset-0 z-999 hidden items-center justify-center bg-base-100/90">
                <div class="flex flex-col items-center gap-3">
                    <span class="loading loading-spinner loading-lg text-warning"></span>
                    <span class="text-sm text-base-content/60" id="mapLoadingText">Mendapatkan lokasi...</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- @once --}}
@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        /* ── Modal Animations ─────────────────────────── */
        #locationModal.show {
            display: flex !important;
        }

        #locationModal.show>div {
            animation: modalSlideUp 0.35s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes modalSlideUp {
            from {
                opacity: 0;
                transform: translateY(30px) scale(0.97);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* ── Desktop: fade in ────────────────────────── */
        @media (min-width: 1024px) {
            #locationModal.show>div {
                animation: modalFadeIn 0.3s ease;
            }

            @keyframes modalFadeIn {
                from {
                    opacity: 0;
                    transform: scale(0.95);
                }

                to {
                    opacity: 1;
                    transform: scale(1);
                }
            }
        }

        /* ── Map ─────────────────────────────────────── */
        #locationMap {
            background: #e8e4df;
        }

        #locationMap .leaflet-container {
            background: #e8e4df;
        }

        .leaflet-control-attribution {
            display: none !important;
        }

        /* ── Custom Pin ─────────────────────────────── */
        .custom-pin {
            position: relative;
            width: 40px;
            height: 40px;
        }

        .custom-pin::before {
            content: '';
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%) rotate(-45deg);
            width: 28px;
            height: 28px;
            background: #f97316;
            border: 3px solid white;
            border-radius: 50% 50% 50% 0;
            box-shadow: 0 4px 16px rgba(249, 115, 22, 0.5);
            z-index: 2;
        }

        .custom-pin-inner {
            position: absolute;
            bottom: 9px;
            left: 50%;
            transform: translateX(-50%);
            width: 10px;
            height: 10px;
            background: white;
            border-radius: 50%;
            z-index: 3;
        }

        .custom-pin-shadow {
            position: absolute;
            bottom: 2px;
            left: 50%;
            transform: translateX(-50%);
            width: 8px;
            height: 8px;
            background: rgba(0, 0, 0, 0.2);
            border-radius: 50%;
            filter: blur(4px);
            z-index: 1;
        }

        /* ── Search Suggestions ──────────────────────── */
        .suggestion-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 14px 16px;
            cursor: pointer;
            transition: background-color 0.15s ease;
        }

        .suggestion-item:hover,
        .suggestion-item.active {
            background-color: hsl(var(--b2, 220 15% 95%));
        }

        .suggestion-item:first-child {
            border-radius: 12px 12px 0 0;
        }

        .suggestion-item:last-child {
            border-radius: 0 0 12px 12px;
        }

        .suggestion-icon {
            width: 40px;
            height: 40px;
            border-radius: 12px;
            background: hsl(var(--b3, 220 15% 90%));
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .suggestion-content {
            flex: 1;
            min-width: 0;
        }

        .suggestion-title {
            font-size: 14px;
            font-weight: 600;
            line-height: 1.3;
            margin-bottom: 2px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .suggestion-subtitle {
            font-size: 12px;
            color: hsl(var(--bc, 220 15% 40%) / 0.6);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Line clamp utility */
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        const LocationPicker = (() => {
            let map = null;
            let pendingLat = null;
            let pendingLng = null;
            let geocodeTimer = null;
            let searchTimer = null;
            let hasExistingLocation = false;
            let isFirstTime = true;
            let selectedSuggestionIndex = -1;

            const ROUTES = {
                locationStatus: '{{ route('user.location.status') }}',
                locationUpdate: '{{ route('user.location.update') }}',
            };

            const CSRF = () => document.querySelector('meta[name="csrf-token"]')?.content || '';

            // ── Init ──────────────────────────────────────────────
            function init() {
                checkExistingLocation();
                setupSearchListeners();
            }

            function checkExistingLocation() {
                fetch(ROUTES.locationStatus, {
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CSRF()
                        },
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.has_location) {
                            hasExistingLocation = true;
                            isFirstTime = false;
                            pendingLat = data.latitude;
                            pendingLng = data.longitude;
                            reverseGeocode(data.latitude, data.longitude, addr => {
                                setLocBadge('on', addr || 'Lokasi tersimpan');
                            });
                            document.getElementById('locationTrigger').style.display = '';
                            document.getElementById('cancelLocationBtn').classList.remove('hidden');
                            document.dispatchEvent(new CustomEvent('locationReady'));
                        } else {
                            open(true);
                        }
                    })
                    .catch(() => {
                        open(true);
                    });
            }

            // ── Modal Open/Close ──────────────────────────────────
            function open(forced = false) {
                const modal = document.getElementById('locationModal');
                modal.classList.add('show');
                document.body.style.overflow = 'hidden';

                const closeBtn = document.getElementById('modalCloseBtn');
                const closeBtnDesktop = document.getElementById('modalCloseBtnDesktop');
                const cancelBtn = document.getElementById('cancelLocationBtn');

                if (forced || !hasExistingLocation) {
                    if (closeBtn) closeBtn.style.display = 'none';
                    if (closeBtnDesktop) closeBtnDesktop.style.display = 'none';
                    cancelBtn.classList.add('hidden');
                    isFirstTime = true;
                } else {
                    if (closeBtn) closeBtn.style.display = '';
                    if (closeBtnDesktop) closeBtnDesktop.style.display = '';
                    cancelBtn.classList.remove('hidden');
                }

                if (!map) {
                    setTimeout(() => initMap(), 400);
                } else {
                    setTimeout(() => {
                        map.invalidateSize();
                        if (pendingLat && pendingLng) {
                            map.setView([pendingLat, pendingLng], 16);
                        }
                    }, 200);
                }

                setTimeout(() => {
                    document.getElementById('locationSearchInput')?.focus();
                }, 600);
            }

            function close() {
                if (!hasExistingLocation) return;

                const modal = document.getElementById('locationModal');
                modal.classList.remove('show');
                document.body.style.overflow = '';
                isFirstTime = false;

                // Clear search
                clearSearch();
            }

            // ── Map ───────────────────────────────────────────────
            function initMap() {
                if (map) {
                    map.remove();
                    map = null;
                }

                const lat = pendingLat ?? -6.1754;
                const lng = pendingLng ?? 106.8272;

                map = L.map('locationMap', {
                    center: [lat, lng],
                    zoom: 16,
                    zoomControl: false,
                    attributionControl: false
                });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19,
                }).addTo(map);

                map.on('moveend', () => {
                    const center = map.getCenter();
                    updatePendingCoords(center.lat, center.lng);
                });

                updatePendingCoords(lat, lng);

                if (!hasExistingLocation) {
                    setTimeout(useGPS, 800);
                }
            }

            function updatePendingCoords(lat, lng) {
                pendingLat = lat;
                pendingLng = lng;

                clearTimeout(geocodeTimer);
                document.getElementById('addressPreview').innerHTML =
                    '<span class="loading loading-dots loading-xs text-warning"></span>';

                geocodeTimer = setTimeout(() => {
                    reverseGeocode(lat, lng, addr => {
                        document.getElementById('addressPreview').textContent =
                            addr || 'Alamat tidak ditemukan';
                    });
                }, 500);
            }

            function reverseGeocode(lat, lng, callback) {
                fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json&accept-language=id`, {
                        headers: {
                            'Accept-Language': 'id'
                        },
                    })
                    .then(r => r.json())
                    .then(data => {
                        const addr = data.display_name || null;
                        callback(addr);
                    })
                    .catch(() => callback(null));
            }

            // ── Search ────────────────────────────────────────────
            function setupSearchListeners() {
                const input = document.getElementById('locationSearchInput');
                const clearBtn = document.getElementById('clearSearchBtn');
                const suggestions = document.getElementById('searchSuggestions');

                input.addEventListener('input', function(e) {
                    const query = e.target.value.trim();

                    if (query) {
                        clearBtn.classList.remove('hidden');
                    } else {
                        clearBtn.classList.add('hidden');
                        suggestions.classList.add('hidden');
                        selectedSuggestionIndex = -1;
                        return;
                    }

                    clearTimeout(searchTimer);
                    if (query.length < 3) {
                        suggestions.classList.add('hidden');
                        selectedSuggestionIndex = -1;
                        return;
                    }

                    searchTimer = setTimeout(() => searchLocation(query), 400);
                });

                input.addEventListener('keydown', function(e) {
                    const items = suggestions.querySelectorAll('.suggestion-item');

                    if (e.key === 'ArrowDown') {
                        e.preventDefault();
                        selectedSuggestionIndex = Math.min(selectedSuggestionIndex + 1, items.length - 1);
                        updateSuggestionHighlight(items);
                    } else if (e.key === 'ArrowUp') {
                        e.preventDefault();
                        selectedSuggestionIndex = Math.max(selectedSuggestionIndex - 1, 0);
                        updateSuggestionHighlight(items);
                    } else if (e.key === 'Enter') {
                        e.preventDefault();
                        if (selectedSuggestionIndex >= 0 && items[selectedSuggestionIndex]) {
                            items[selectedSuggestionIndex].click();
                        }
                    } else if (e.key === 'Escape') {
                        suggestions.classList.add('hidden');
                        input.blur();
                        selectedSuggestionIndex = -1;
                    }
                });

                // Close dropdown when clicking outside
                document.addEventListener('click', function(e) {
                    if (!e.target.closest('#searchSuggestions') &&
                        !e.target.closest('#locationSearchInput')) {
                        suggestions.classList.add('hidden');
                        selectedSuggestionIndex = -1;
                    }
                });
            }

            function searchLocation(query) {
                const suggestions = document.getElementById('searchSuggestions');

                fetch(`https://nominatim.openstreetmap.org/search?q=${encodeURIComponent(query)}&format=json&limit=5&countrycodes=id&accept-language=id`, {
                        headers: {
                            'Accept-Language': 'id'
                        },
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (!data.length) {
                            suggestions.innerHTML = `
                                <div class="px-4 py-8 text-center">
                                    <svg class="w-8 h-8 mx-auto mb-2 text-base-content/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                    <p class="text-sm text-base-content/50">Alamat tidak ditemukan</p>
                                    <p class="text-xs text-base-content/30 mt-1">Coba kata kunci lain</p>
                                </div>`;
                            suggestions.classList.remove('hidden');
                            return;
                        }

                        selectedSuggestionIndex = -1;
                        suggestions.innerHTML = data.map((item) => {
                            const parts = item.display_name.split(',');
                            const title = parts[0].trim();
                            const subtitle = parts.slice(1, 3).join(',').trim();

                            return `
                                <div class="suggestion-item" 
                                    data-lat="${item.lat}" data-lon="${item.lon}"
                                    onclick="LocationPicker.selectSuggestion(${item.lat}, ${item.lon}, '${escapeHtml(item.display_name)}')"
                                    onmouseenter="this.classList.add('active')" 
                                    onmouseleave="this.classList.remove('active')">
                                    <div class="suggestion-icon">
                                        <svg class="w-5 h-5 text-base-content/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                    </div>
                                    <div class="suggestion-content">
                                        <p class="suggestion-title">${title}</p>
                                        ${subtitle ? `<p class="suggestion-subtitle">${subtitle}</p>` : ''}
                                    </div>
                                </div>
                            `;
                        }).join('');

                        suggestions.classList.remove('hidden');
                    })
                    .catch(() => {
                        suggestions.innerHTML = `
                            <div class="px-4 py-6 text-center text-sm text-error">Gagal mencari alamat</div>`;
                        suggestions.classList.remove('hidden');
                    });
            }

            function selectSuggestion(lat, lon, displayName) {
                document.getElementById('locationSearchInput').value = displayName;
                document.getElementById('searchSuggestions').classList.add('hidden');
                document.getElementById('clearSearchBtn').classList.remove('hidden');
                selectedSuggestionIndex = -1;

                map.flyTo([lat, lon], 17, {
                    duration: 1
                });
                updatePendingCoords(lat, lon);
                document.getElementById('addressPreview').textContent = displayName;
            }

            function clearSearch() {
                const input = document.getElementById('locationSearchInput');
                input.value = '';
                document.getElementById('clearSearchBtn').classList.add('hidden');
                document.getElementById('searchSuggestions').classList.add('hidden');
                selectedSuggestionIndex = -1;
            }

            function updateSuggestionHighlight(items) {
                items.forEach((item, index) => {
                    if (index === selectedSuggestionIndex) {
                        item.classList.add('active');
                        item.scrollIntoView({
                            block: 'nearest'
                        });
                    } else {
                        item.classList.remove('active');
                    }
                });
            }

            // ── GPS ────────────────────────────────────────────────
            function useGPS() {
                if (!navigator.geolocation) {
                    showToast('Browser tidak mendukung GPS', 'error');
                    return;
                }

                const gpsBtn = document.getElementById('gpsBtn');
                const gpsBtnText = document.getElementById('gpsBtnText');
                const gpsBtnSpinner = document.getElementById('gpsBtnSpinner');

                gpsBtn.disabled = true;
                gpsBtnText.textContent = 'Mendapatkan lokasi...';
                gpsBtnSpinner.classList.remove('hidden');

                showMapLoading('Mendapatkan lokasi GPS...');

                navigator.geolocation.getCurrentPosition(
                    pos => {
                        hideMapLoading();
                        gpsBtn.disabled = false;
                        gpsBtnText.textContent = 'Gunakan Lokasi Saya Saat Ini';
                        gpsBtnSpinner.classList.add('hidden');

                        const lat = pos.coords.latitude;
                        const lng = pos.coords.longitude;

                        map.flyTo([lat, lng], 17, {
                            duration: 1.5
                        });
                        updatePendingCoords(lat, lng);

                        document.getElementById('locationSearchInput').value = '📍 Lokasi Saya Saat Ini';
                        document.getElementById('clearSearchBtn').classList.remove('hidden');
                        document.getElementById('searchSuggestions').classList.add('hidden');
                    },
                    err => {
                        hideMapLoading();
                        gpsBtn.disabled = false;
                        gpsBtnText.textContent = 'Gunakan Lokasi Saya Saat Ini';
                        gpsBtnSpinner.classList.add('hidden');

                        const msgs = {
                            1: 'Izin GPS ditolak. Buka pengaturan browser.',
                            2: 'Sinyal GPS tidak tersedia',
                            3: 'Timeout GPS'
                        };
                        showToast(msgs[err.code] || 'GPS gagal', 'error');
                    }, {
                        timeout: 15000,
                        maximumAge: 60000,
                        enableHighAccuracy: true
                    }
                );
            }

            // ── Confirm ────────────────────────────────────────────
            function confirm() {
                if (pendingLat === null) {
                    showToast('Pilih lokasi terlebih dahulu', 'warning');
                    return;
                }

                const btn = document.getElementById('confirmLocationBtn');
                btn.disabled = true;
                btn.innerHTML = '<span class="loading loading-spinner loading-sm"></span> Menyimpan...';

                fetch(ROUTES.locationUpdate, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': CSRF(),
                        },
                        body: JSON.stringify({
                            latitude: pendingLat,
                            longitude: pendingLng
                        }),
                    })
                    .then(r => r.json().then(data => ({
                        status: r.status,
                        data
                    })))
                    .then(({
                        status,
                        data
                    }) => {
                        btn.disabled = false;
                        btn.innerHTML = `
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Konfirmasi Lokasi
                        `;

                        if (status !== 200) {
                            showToast(data.message || 'Gagal menyimpan lokasi', 'error');
                            return;
                        }

                        hasExistingLocation = true;
                        const addrText = document.getElementById('addressPreview').textContent;
                        setLocBadge('on', addrText || 'Lokasi tersimpan');

                        document.getElementById('locationTrigger').style.display = '';
                        document.getElementById('cancelLocationBtn').classList.remove('hidden');

                        close();

                        document.dispatchEvent(new CustomEvent('locationReady'));
                        document.dispatchEvent(new CustomEvent('locationUpdated', {
                            detail: {
                                lat: pendingLat,
                                lng: pendingLng
                            }
                        }));
                    })
                    .catch(() => {
                        btn.disabled = false;
                        btn.innerHTML = `
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Konfirmasi Lokasi
                        `;
                        showToast('Koneksi gagal. Coba lagi.', 'error');
                    });
            }

            // ── Helpers ────────────────────────────────────────────
            function setLocBadge(state, text) {
                const dot = document.getElementById('locDot');
                const textEl = document.getElementById('locText');
                if (state === 'on') {
                    dot.className = 'w-2 h-2 rounded-full bg-success';
                } else {
                    dot.className = 'w-2 h-2 rounded-full bg-error animate-pulse';
                }
                textEl.textContent = text;
            }

            function showMapLoading(text) {
                document.getElementById('mapLoadingText').textContent = text;
                const el = document.getElementById('mapLoadingOverlay');
                el.classList.remove('hidden');
                el.classList.add('flex');
            }

            function hideMapLoading() {
                const el = document.getElementById('mapLoadingOverlay');
                el.classList.add('hidden');
                el.classList.remove('flex');
            }

            function showToast(msg, type = 'info') {
                const colors = {
                    error: 'alert-error',
                    warning: 'alert-warning',
                    info: 'alert-info',
                    success: 'alert-success'
                };
                const toast = document.createElement('div');
                toast.className = 'toast toast-top toast-center z-[9999]';
                toast.innerHTML = `<div class="alert ${colors[type]} text-sm py-2 px-4 shadow-lg">${msg}</div>`;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            return {
                init,
                open,
                close,
                confirm,
                useGPS,
                selectSuggestion,
                clearSearch
            };
        })();

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => LocationPicker.init());
        } else {
            LocationPicker.init();
        }

        // Debug: Log that LocationPicker is available
        console.log('LocationPicker object created:', typeof LocationPicker);
    </script>
@endpush

