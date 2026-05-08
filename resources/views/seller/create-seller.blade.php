<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Register as Seller - Rantangku</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>

<body class="bg-orange-50/30 font-sans antialiased" x-data="sellerRegistration">

    <div class="min-h-screen flex flex-col">

        <!-- ============ HEADER ============ -->
        <header class="bg-white/80 backdrop-blur-lg shadow-sm border-b border-base-200 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <!-- Logo & Back -->
                    <div class="flex items-center gap-4">
                        <a href="{{ url('/') }}" class="btn btn-ghost btn-sm btn-circle">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                            </svg>
                        </a>
                        <div class="flex items-center gap-2">
                            <div>
                                <span class="text-lg font-bold">Rantangku</span>
                                <span class="text-xs text-base-content/50 block -mt-1">Seller Registration</span>
                            </div>
                        </div>
                    </div>

                    <!-- Header Right - Step Indicator -->
                    <div class="hidden sm:flex items-center gap-2">
                        <div class="text-sm text-base-content/60">Already a seller?</div>
                        <a href="{{ route('login') }}" class="btn btn-ghost btn-sm text-warning">Sign In</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- ============ PROGRESS STEPS (MOBILE) ============ -->
        <div class="lg:hidden bg-white border-b border-base-200 px-4 py-3">
            <ul class="steps steps-horizontal w-full text-xs">
                <li class="step step-warning" :class="{ 'step-warning': currentStep >= 1, 'step': currentStep < 1 }">
                    Info</li>
                <li class="step" :class="{ 'step-warning': currentStep >= 2, 'step': currentStep < 2 }">Location</li>
                <li class="step" :class="{ 'step-warning': currentStep >= 3 }">Confirm</li>
            </ul>
        </div>

        <!-- ============ MAIN CONTENT ============ -->
        <main class="flex-1 flex">
            <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-8">

                <!-- Desktop Layout: 2 Columns -->
                <div class="lg:grid lg:grid-cols-5 lg:gap-8">

                    <!-- ============ LEFT COLUMN - FORM ============ -->
                    <div class="lg:col-span-3">

                        <!-- Desktop Step Indicator -->
                        <div class="hidden lg:block mb-8">
                            <ul class="steps steps-horizontal w-full">
                                <li class="step step-warning"
                                    :class="{ 'step-warning': currentStep >= 1, 'step': currentStep < 1 }"
                                    @click="currentStep = 1">
                                    <span class="step-icon">1</span>
                                    <span>Restaurant Info</span>
                                </li>
                                <li class="step"
                                    :class="{ 'step-warning': currentStep >= 2, 'step': currentStep < 2 }"
                                    @click="currentStep = 2">
                                    <span class="step-icon">2</span>
                                    <span>Location</span>
                                </li>
                                <li class="step" :class="{ 'step-warning': currentStep >= 3 }"
                                    @click="currentStep = 3">
                                    <span class="step-icon">3</span>
                                    <span>Review & Submit</span>
                                </li>
                            </ul>
                        </div>

                        <!-- ============ STEP 1: RESTAURANT INFO ============ -->
                        <div x-show="currentStep === 1" x-transition>
                            <div class="card bg-base-100 border border-base-200 shadow-sm">
                                <div class="card-body p-6 lg:p-8">
                                    <h2 class="card-title text-xl mb-1">Restoran Information</h2>
                                    <p class="text-sm text-base-content/60 mb-6">Ceritakan tentang restoran Anda.</p>

                                    <form @submit.prevent="nextStep" class="space-y-5">
                                        <!-- Restaurant Name -->
                                        <div class="form-control">
                                            <label class="label" for="name">
                                                <span class="label-text font-semibold">Nama Restoran *</span>
                                            </label>
                                            <input type="text" id="name" x-model="form.name" required
                                                class="input input-bordered w-full focus:input-warning"
                                                placeholder="e.g., Warung Makan Sedap">
                                            @error('name')
                                                <label class="label"><span
                                                        class="label-text-alt text-error">{{ $message }}</span></label>
                                            @enderror
                                        </div>

                                        <!-- Description -->
                                        <div class="form-control">
                                            <label class="label" for="description">
                                                <span class="label-text font-semibold">Description *</span>
                                                <span class="label-text-alt text-base-content/50"
                                                    x-text="form.description.length + '/500'"></span>
                                            </label>
                                            <textarea id="description" x-model="form.description" required maxlength="500"
                                                class="textarea textarea-bordered w-full focus:textarea-warning h-28"
                                                placeholder="Jelaskan restoran Anda, jenis masakan, spesialisasi, dll.."></textarea>
                                            @error('description')
                                                <label class="label"><span
                                                        class="label-text-alt text-error">{{ $message }}</span></label>
                                            @enderror
                                        </div>

                                        <!-- Address -->
                                        <div class="form-control">
                                            <label class="label" for="address">
                                                <span class="label-text font-semibold">Alamat Lengkap *</span>
                                            </label>
                                            <textarea id="address" x-model="form.address" required
                                                class="textarea textarea-bordered w-full focus:textarea-warning h-20"
                                                placeholder="Alamat lengkap termasuk jalan, kota, dan kode pos."></textarea>
                                            @error('address')
                                                <label class="label"><span
                                                        class="label-text-alt text-error">{{ $message }}</span></label>
                                            @enderror
                                        </div>

                                        <!-- Image Upload -->
                                        <div class="form-control">
                                            <label class="label">
                                                <span class="label-text font-semibold">Restaurant Image *</span>
                                                <span class="label-text-alt text-base-content/50">Max 2MB,
                                                    JPG/PNG</span>
                                            </label>

                                            <!-- Upload Area -->
                                            <div x-show="!imagePreview"
                                                class="border-2 border-dashed border-base-300 rounded-xl p-8 text-center hover:border-warning transition-colors cursor-pointer"
                                                @click="$refs.imageInput.click()" @dragover.prevent @dragenter.prevent
                                                @drop.prevent="handleImageDrop($event)">
                                                <input type="file" x-ref="imageInput" @change="handleImageSelect"
                                                    accept="image/jpeg,image/png" class="hidden">
                                                <div class="flex flex-col items-center gap-3">
                                                    <div
                                                        class="w-16 h-16 bg-warning/10 rounded-full flex items-center justify-center">
                                                        <svg class="w-8 h-8 text-warning" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <p class="text-sm font-semibold">Click to upload or drag & drop
                                                        </p>
                                                        <p class="text-xs text-base-content/50">JPG or PNG up to 2MB
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Image Preview -->
                                            <div x-show="imagePreview" class="relative rounded-xl overflow-hidden">
                                                <img :src="imagePreview" alt="Preview"
                                                    class="w-full h-48 lg:h-56 object-cover">
                                                <button @click="removeImage"
                                                    class="btn btn-sm btn-circle btn-error absolute top-2 right-2 shadow-lg">✕</button>
                                            </div>
                                            @error('img_url')
                                                <label class="label"><span
                                                        class="label-text-alt text-error">{{ $message }}</span></label>
                                            @enderror
                                        </div>

                                        <!-- Next Button -->
                                        <div class="card-actions justify-end pt-2">
                                            <button type="submit" class="btn btn-warning gap-2">
                                                Next: Set Location
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                                </svg>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- ============ STEP 2: LOCATION ============ -->
                        <div x-show="currentStep === 2" x-transition>
                            <div class="card bg-base-100 border border-base-200 shadow-sm">
                                <div class="card-body p-6 lg:p-8">
                                    <h2 class="card-title text-xl mb-1">Lokasi Restoran</h2>
                                    <p class="text-sm text-base-content/60 mb-6">Cari alamat restoran Anda atau
                                        temukan di peta</p>

                                    <!-- Search Bar + Use My Location -->
                                    <div class="flex flex-col sm:flex-row gap-3 mb-4">
                                        <!-- Search Input dengan Autocomplete -->
                                        <div class="flex-1 relative" x-data="{ searchQuery: '', suggestions: [], showSuggestions: false, selectedIndex: -1 }">
                                            <div class="relative">
                                                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-base-content/40"
                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                                </svg>
                                                <input type="text" x-model="searchQuery"
                                                    placeholder="Search alamat atau nama tempat..."
                                                    class="input input-bordered w-full pl-10 pr-10 focus:input-warning text-sm"
                                                    @input.debounce.500ms="searchAddress($event.target.value)"
                                                    @keydown.escape="showSuggestions = false"
                                                    @keydown.arrow-down.prevent="if (selectedIndex < suggestions.length - 1) { selectedIndex++; scrollToSelected(); }"
                                                    @keydown.arrow-up.prevent="if (selectedIndex > 0) { selectedIndex--; scrollToSelected(); }"
                                                    @keydown.enter.prevent="if (selectedIndex >= 0) { selectSuggestion(suggestions[selectedIndex]); } else if (searchQuery.trim()) { searchAddress(searchQuery, true); }"
                                                    @focus="if (suggestions.length > 0) showSuggestions = true"
                                                    @click.outside="showSuggestions = false">
                                                <!-- Clear Button -->
                                                <button x-show="searchQuery"
                                                    @click="searchQuery = ''; suggestions = []; showSuggestions = false; $refs.searchInput.focus()"
                                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-base-content/40 hover:text-base-content/70">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>

                                            <!-- Search Suggestions Dropdown -->
                                            <div x-show="showSuggestions && suggestions.length > 0" x-transition
                                                class="absolute z-50 w-full mt-1 bg-base-100 border border-base-300 rounded-xl shadow-lg max-h-60 overflow-y-auto">
                                                <template x-for="(suggestion, index) in suggestions"
                                                    :key="index">
                                                    <div @click="selectSuggestion(suggestion)"
                                                        @mouseenter="selectedIndex = index"
                                                        :class="{ 'bg-warning/10': selectedIndex === index }"
                                                        class="px-4 py-3 cursor-pointer hover:bg-base-200 transition-colors border-b border-base-200 last:border-b-0">
                                                        <div class="flex items-start gap-3">
                                                            <svg class="w-4 h-4 text-base-content/40 mt-0.5 shrink-0"
                                                                fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                            </svg>
                                                            <div class="min-w-0">
                                                                <p class="text-sm font-medium truncate"
                                                                    x-text="suggestion.display_name.split(',')[0]"></p>
                                                                <p class="text-xs text-base-content/50 truncate"
                                                                    x-text="suggestion.display_name"></p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </template>
                                            </div>

                                            <!-- Loading Indicator -->
                                            <div x-show="searching"
                                                class="absolute z-50 w-full mt-1 bg-base-100 border border-base-300 rounded-xl shadow-lg p-4 text-center">
                                                <span class="loading loading-spinner loading-sm text-warning"></span>
                                                <span class="text-sm text-base-content/60 ml-2">Searching...</span>
                                            </div>

                                            <!-- No Results -->
                                            <div x-show="showSuggestions && suggestions.length === 0 && searchQuery.length > 2 && !searching"
                                                x-transition
                                                class="absolute z-50 w-full mt-1 bg-base-100 border border-base-300 rounded-xl shadow-lg p-4 text-center">
                                                <p class="text-sm text-base-content/50">Tidak ada hasil yang ditemukan.
                                                    Coba kata kunci yang berbeda.</p>
                                            </div>
                                        </div>

                                        <!-- Use My Location Button -->
                                        <button @click="detectLocation" :disabled="gpsStatus === 'detecting'"
                                            class="btn btn-outline btn-sm sm:btn-md gap-2 shrink-0"
                                            :class="{ 'btn-warning': gpsStatus === 'detecting' }">
                                            <span x-show="gpsStatus === 'detecting'"
                                                class="loading loading-spinner loading-sm"></span>
                                            <svg x-show="gpsStatus !== 'detecting'" class="w-4 h-4" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            <span class="hidden sm:inline">Gunakan Lokasi Saya</span>
                                            <span class="sm:hidden">GPS</span>
                                        </button>
                                    </div>

                                    <!-- GPS Status -->
                                    <div class="flex items-center gap-3 p-3 bg-info/10 rounded-xl mb-4"
                                        x-show="gpsStatus === 'detecting'">
                                        <span class="loading loading-spinner loading-sm text-info"></span>
                                        <p class="text-sm text-info font-medium">Mendeteksi lokasi Anda...</p>
                                    </div>
                                    <div class="flex items-center gap-3 p-3 bg-success/10 rounded-xl mb-4"
                                        x-show="gpsStatus === 'success'" x-transition>
                                        <span class="text-success">✅</span>
                                        <p class="text-sm text-success font-medium">Lokasi terdeteksi! Anda masih dapat
                                            mencari atau menyeret penanda.</p>
                                        <button @click="gpsStatus = 'idle'"
                                            class="btn btn-ghost btn-xs ml-auto">✕</button>
                                    </div>
                                    <div class="flex items-center gap-3 p-3 bg-warning/10 rounded-xl mb-4"
                                        x-show="gpsStatus === 'failed'" x-transition>
                                        <span class="text-warning">⚠️</span>
                                        <p class="text-sm text-warning font-medium">Lokasi tidak terdeteksi. Silakan

                                            cari alamat Anda sebagai gantinya.</p>
                                        <button @click="gpsStatus = 'idle'"
                                            class="btn btn-ghost btn-xs ml-auto">✕</button>
                                    </div>
                                    <div class="flex items-center gap-3 p-3 bg-success/10 rounded-xl mb-4"
                                        x-show="gpsStatus === 'searched'" x-transition>
                                        <span class="text-success">📍</span>
                                        <p class="text-sm text-success font-medium">Lokasi ditemukan! Seret penanda
                                            untuk
                                            penempatan yang tepat.</p>
                                        <button @click="gpsStatus = 'idle'"
                                            class="btn btn-ghost btn-xs ml-auto">✕</button>
                                    </div>

                                    <!-- Map Container -->
                                    <div id="map"
                                        class="w-full h-64 lg:h-80 rounded-xl border border-base-300 mb-4 z-0"></div>

                                    <!-- Coordinates Display - Manual Input -->
                                    <div class="mb-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-sm font-semibold">Coordinates</span>
                                            <button @click="toggleManualCoord"
                                                class="btn btn-ghost btn-xs text-warning gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                                <span x-text="showManualCoord ? 'Use Map' : 'Edit Manually'"></span>
                                            </button>
                                        </div>

                                        <!-- Readonly Display (default) -->
                                        <div x-show="!showManualCoord" class="grid grid-cols-2 gap-3">
                                            <div class="form-control">
                                                <label class="label py-1">
                                                    <span class="label-text text-xs font-medium">Latitude</span>
                                                </label>
                                                <input type="text" :value="form.latitude" readonly
                                                    class="input input-bordered input-sm w-full bg-base-200 text-sm font-mono">
                                            </div>
                                            <div class="form-control">
                                                <label class="label py-1">
                                                    <span class="label-text text-xs font-medium">Longitude</span>
                                                </label>
                                                <input type="text" :value="form.longitude" readonly
                                                    class="input input-bordered input-sm w-full bg-base-200 text-sm font-mono">
                                            </div>
                                        </div>

                                        <!-- Manual Input -->
                                        <div x-show="showManualCoord" class="grid grid-cols-2 gap-3">
                                            <div class="form-control">
                                                <label class="label py-1">
                                                    <span class="label-text text-xs font-medium">Latitude</span>
                                                </label>
                                                <input type="text" x-model="form.latitude"
                                                    @input="onManualCoordChange"
                                                    class="input input-bordered input-sm w-full focus:input-warning text-sm font-mono"
                                                    placeholder="-6.2088000">
                                            </div>
                                            <div class="form-control">
                                                <label class="label py-1">
                                                    <span class="label-text text-xs font-medium">Longitude</span>
                                                </label>
                                                <input type="text" x-model="form.longitude"
                                                    @input="onManualCoordChange"
                                                    class="input input-bordered input-sm w-full focus:input-warning text-sm font-mono"
                                                    placeholder="106.8456000">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Map Instructions -->
                                    <div
                                        class="flex items-center gap-2 text-xs text-base-content/50 bg-base-200 rounded-lg p-3">
                                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Cari alamat Anda, gunakan GPS, atau masukkan koordinat secara manual.
                                            Seret penanda untuk penempatan yang tepat.</span>
                                    </div>

                                    <!-- Step 2 Buttons -->
                                    <div class="card-actions justify-between pt-4">
                                        <button @click="currentStep = 1" class="btn btn-ghost gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                                            </svg>
                                            Back
                                        </button>
                                        <button @click="validateAndProceed" class="btn btn-warning gap-2">
                                            Next: Review
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ============ STEP 3: REVIEW & SUBMIT ============ -->
                        <div x-show="currentStep === 3" x-transition>
                            <div class="card bg-base-100 border border-base-200 shadow-sm">
                                <div class="card-body p-6 lg:p-8">
                                    <h2 class="card-title text-xl mb-1">Tinjau Aplikasi Anda</h2>
                                    <p class="text-sm text-base-content/60 mb-6">Harap verifikasi semua informasi
                                        sebelum
                                        mengirimkan</p>

                                    <!-- Review Details -->
                                    <div class="space-y-4">
                                        <!-- Image Preview -->
                                        <div class="rounded-xl overflow-hidden" x-show="imagePreview">
                                            <img :src="imagePreview" alt="Restaurant Image"
                                                class="w-full h-40 lg:h-48 object-cover">
                                        </div>

                                        <!-- Info Rows -->
                                        <div class="grid grid-cols-1 gap-3">
                                            <div class="bg-base-200 rounded-xl p-4">
                                                <p class="text-xs text-base-content/50 mb-1">Nama Restoran</p>
                                                <p class="text-sm font-semibold" x-text="form.name || '-'"></p>
                                            </div>
                                            <div class="bg-base-200 rounded-xl p-4">
                                                <p class="text-xs text-base-content/50 mb-1">Description</p>
                                                <p class="text-sm font-semibold" x-text="form.description || '-'"></p>
                                            </div>
                                            <div class="bg-base-200 rounded-xl p-4">
                                                <p class="text-xs text-base-content/50 mb-1">Alamat</p>
                                                <p class="text-sm font-semibold" x-text="form.address || '-'"></p>
                                            </div>
                                            <div class="bg-base-200 rounded-xl p-4">
                                                <p class="text-xs text-base-content/50 mb-1">Koordinat</p>
                                                <p class="text-sm font-mono"
                                                    x-text="form.latitude ? form.latitude + ', ' + form.longitude : '-'">
                                                </p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Agreement -->
                                    <div class="form-control mt-6">
                                        <label class="cursor-pointer justify-start flex gap-3">
                                            <input type="checkbox" x-model="form.agreed"
                                                class="checkbox checkbox-warning checkbox-sm" />
                                            <span class="label-text text-sm">Saya menyatakan bahwa semua informasi
                                                adalah benar
                                                dan saya menyetujui hal tersebut <a href="#"
                                                    class="text-warning font-medium">Seller Terms &
                                                    Conditions</a></span>
                                        </label>
                                    </div>

                                    <!-- Step 3 Buttons -->
                                    <div class="card-actions justify-between pt-4">
                                        <button @click="currentStep = 2" class="btn btn-ghost gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 17l-5-5m0 0l5-5m-5 5h12" />
                                            </svg>
                                            Back
                                        </button>
                                        <button @click="submitRegistration" :disabled="!form.agreed || submitting"
                                            class="btn btn-warning gap-2"
                                            :class="{ 'btn-disabled': !form.agreed || submitting }">
                                            <span x-show="!submitting">Submit Registration</span>
                                            <span x-show="submitting"
                                                class="loading loading-spinner loading-sm"></span>
                                            <span x-show="submitting">Submitting...</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Success State -->
                        <div x-show="currentStep === 4" x-transition>
                            <div class="card bg-base-100 border border-success/30 shadow-sm">
                                <div class="card-body p-8 lg:p-12 text-center">
                                    <div
                                        class="w-20 h-20 bg-success/10 rounded-full flex items-center justify-center mx-auto mb-6">
                                        <svg class="w-10 h-10 text-success" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <h2 class="text-2xl font-bold mb-2">Lamaran Dikirim! 🎉</h2>
                                    <p class="text-base-content/60 mb-6">
                                        Permohonan penjual Anda telah berhasil diajukan.<br>
                                        Kami akan meninjaunya dalam 1-2 hari kerja.
                                    </p>
                                    <div class="flex gap-3 justify-center">
                                        <a href="{{ url('/') }}" class="btn btn-ghost">Kembali ke Home</a>
                                        <a href="{{ route('seller.dashboard') }}" class="btn btn-warning">Pergi ke
                                            Dashboard</a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- ============ RIGHT COLUMN - SIDEBAR INFO ============ -->
                    <div class="hidden lg:block lg:col-span-2">
                        <div class="sticky top-24 space-y-4">
                            <!-- Info Card -->
                            <div class="card bg-base-100 border border-base-200 shadow-sm">
                                <div class="card-body p-6">
                                    <h3 class="card-title text-base">Kenapa Join Rantangku?</h3>
                                    <ul class="space-y-3 mt-2">
                                        <li class="flex gap-3">
                                            <span class="text-success shrink-0">✅</span>
                                            <span class="text-sm">Jangkau ribuan pelanggan yang lapar.</span>
                                        </li>
                                        <li class="flex gap-3">
                                            <span class="text-success shrink-0">✅</span>
                                            <span class="text-sm">Tingkat komisi rendah</span>
                                        </li>
                                        <li class="flex gap-3">
                                            <span class="text-success shrink-0">✅</span>
                                            <span class="text-sm">Dasbor manajemen pesanan yang mudah</span>
                                        </li>
                                        <li class="flex gap-3">
                                            <span class="text-success shrink-0">✅</span>
                                            <span class="text-sm">Proses pembayaran cepat & aman</span>
                                        </li>
                                        <li class="flex gap-3">
                                            <span class="text-success shrink-0">✅</span>
                                            <span class="text-sm">Pendaftaran gratis, tanpa biaya tersembunyi.</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            <!-- Map Preview (when on Step 2) -->
                            <div x-show="currentStep === 2" class="card bg-base-100 border border-base-200 shadow-sm">
                                <div class="card-body p-4">
                                    <h3 class="card-title text-sm">📍 Location Tips</h3>
                                    <ul class="text-xs text-base-content/70 space-y-2 mt-2">
                                        <li>• Pastikan penanda tersebut berada tepat di lokasi restoran Anda.</li>
                                        <li>• Lokasi yang akurat membantu pelanggan menemukan Anda.</li>
                                        <li>• Anda dapat memperbesar tampilan untuk penempatan yang lebih tepat.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- ============ MOBILE NAVIGATION (BOTTOM) ============ -->
        <div
            class="lg:hidden fixed bottom-0 left-0 right-0 bg-base-100 border-t border-base-200 z-30 safe-area-bottom">
            <div class="flex items-center justify-between px-4 py-3">
                <button x-show="currentStep > 1 && currentStep < 4" @click="currentStep--"
                    class="btn btn-ghost btn-sm">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                    Back
                </button>
                <div x-show="currentStep === 1" class="text-sm text-base-content/50">Step 1 of 3</div>
                <button x-show="currentStep === 1" @click="nextStep" class="btn btn-warning btn-sm ml-auto gap-2">
                    Next
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <button x-show="currentStep === 2" @click="validateAndProceed"
                    class="btn btn-warning btn-sm ml-auto gap-2">
                    Next
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>
                <button x-show="currentStep === 3" @click="submitRegistration" :disabled="!form.agreed || submitting"
                    class="btn btn-warning btn-sm ml-auto gap-2">
                    Submit
                </button>
            </div>
        </div>
    </div>

    <!-- ============ SUBMIT FORM (HIDDEN) ============ -->
    <form id="sellerForm" method="POST" action="{{ route('store.submit') }}" enctype="multipart/form-data"
        class="hidden">
        @csrf
        <input type="hidden" name="name" :value="form.name">
        <input type="hidden" name="description" :value="form.description">
        <input type="hidden" name="address" :value="form.address">
        <input type="file" name="img_url" x-ref="fileInput">
        <input type="hidden" name="latitude" :value="form.latitude">
        <input type="hidden" name="longitude" :value="form.longitude">
    </form>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('sellerRegistration', () => ({
                currentStep: 1,
                submitting: false,
                imagePreview: null,
                imageFile: null,
                gpsStatus: 'idle', // idle, detecting, success, failed, searched
                showManualCoord: false,
                searching: false,
                map: null,
                marker: null,
                searchTimeout: null,
                defaultLat: -6.2088, // Jakarta default
                defaultLng: 106.8456,

                form: {
                    name: '',
                    description: '',
                    address: '',
                    latitude: '',
                    longitude: '',
                    agreed: false,
                    image: null
                },

                init() {
                    // Initialize map when step 2 is shown
                    this.$watch('currentStep', (value) => {
                        if (value === 2 && !this.map) {
                            this.$nextTick(() => {
                                setTimeout(() => {
                                    this.initMap();
                                }, 200);
                            });
                        }
                        // Invalidate map size when returning to step 2
                        if (value === 2 && this.map) {
                            this.$nextTick(() => {
                                setTimeout(() => {
                                    this.map.invalidateSize();
                                }, 300);
                            });
                        }
                    });
                },

                initMap() {
                    // Destroy existing map if any
                    if (this.map) {
                        this.map.remove();
                    }

                    const initialLat = this.form.latitude ? parseFloat(this.form.latitude) : this
                        .defaultLat;
                    const initialLng = this.form.longitude ? parseFloat(this.form.longitude) : this
                        .defaultLng;

                    // Create map
                    this.map = L.map('map').setView([initialLat, initialLng], 16);

                    // Add tile layer (OpenStreetMap - Free, no API key needed)
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
                        maxZoom: 19
                    }).addTo(this.map);

                    // Custom marker icon
                    const markerIcon = L.icon({
                        iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                        iconRetinaUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon-2x.png',
                        shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                        iconSize: [25, 41],
                        iconAnchor: [12, 41],
                        popupAnchor: [1, -34],
                        shadowSize: [41, 41]
                    });

                    // Add draggable marker
                    this.marker = L.marker([initialLat, initialLng], {
                        draggable: true,
                        icon: markerIcon
                    }).addTo(this.map);

                    // Add popup to marker
                    this.marker.bindPopup(
                        '🏪 <strong>Restoran Anda</strong><br><small>Seret untuk menyesuaikan lokasi</small>'
                    ).openPopup();

                    // Update coordinates when marker is dragged
                    this.marker.on('dragend', () => {
                        const pos = this.marker.getLatLng();
                        this.form.latitude = pos.lat.toFixed(7);
                        this.form.longitude = pos.lng.toFixed(7);
                        this.gpsStatus = 'idle';
                    });

                    // Update coordinates when map is clicked (optional)
                    this.map.on('click', (e) => {
                        this.marker.setLatLng(e.latlng);
                        this.form.latitude = e.latlng.lat.toFixed(7);
                        this.form.longitude = e.latlng.lng.toFixed(7);
                        this.gpsStatus = 'idle';
                    });

                    // Set initial coordinates
                    if (!this.form.latitude) {
                        this.form.latitude = initialLat.toFixed(7);
                        this.form.longitude = initialLng.toFixed(7);
                    }

                    // Invalidate map size
                    setTimeout(() => {
                        this.map.invalidateSize();
                    }, 300);
                },

                // ============ SEARCH ADDRESS (Nominatim API) ============
                async searchAddress(query, immediateSelect = false) {
                    // Clear previous timeout
                    if (this.searchTimeout) {
                        clearTimeout(this.searchTimeout);
                    }

                    if (!query || query.trim().length < 3) {
                        // Need this to access Alpine component properties from nested scope
                        const self = this;
                        setTimeout(() => {
                            self.suggestions = [];
                            self.showSuggestions = false;
                            self.searching = false;
                        }, 0);
                        return;
                    }

                    const self = this;
                    this.searchTimeout = setTimeout(async () => {
                        self.searching = true;
                        self.showSuggestions = false;
                        self.suggestions = [];

                        try {
                            // Nominatim API (OpenStreetMap) - Free, rate limit 1 request/second
                            const response = await fetch(
                                `https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}&countrycodes=id&limit=5&addressdetails=1`, {
                                    headers: {
                                        'Accept-Language': 'id', // Prioritize Indonesian results
                                        'User-Agent': 'RantangkuApp/1.0' // Nominatim requires User-Agent
                                    }
                                }
                            );

                            if (!response.ok) throw new Error('Search failed');

                            const data = await response.json();

                            if (data && data.length > 0) {
                                self.suggestions = data;
                                self.showSuggestions = true;
                                self.selectedIndex = -1;

                                // If immediate select (Enter pressed with no suggestion selected)
                                if (immediateSelect && data.length > 0) {
                                    self.selectSuggestion(data[0]);
                                }
                            } else {
                                self.suggestions = [];
                                self.showSuggestions = true;
                            }
                        } catch (error) {
                            console.error('Search error:', error);
                            self.suggestions = [];
                            self.showSuggestions = true;
                        } finally {
                            self.searching = false;
                        }
                    }, 300);
                },

                selectSuggestion(suggestion) {
                    const lat = parseFloat(suggestion.lat);
                    const lng = parseFloat(suggestion.lon);

                    this.form.latitude = lat.toFixed(7);
                    this.form.longitude = lng.toFixed(7);

                    // Update map and marker
                    if (this.map && this.marker) {
                        this.marker.setLatLng([lat, lng]);
                        this.map.flyTo([lat, lng], 18, {
                            duration: 1.5
                        });
                        this.marker.openPopup();
                    }

                    // Update GPS status
                    this.gpsStatus = 'searched';

                    // Clear search
                    this.showSuggestions = false;
                    this.suggestions = [];
                    this.searchQuery = suggestion.display_name;

                    // Store the address in the form (optional - for reference)
                    this.searchedAddress = suggestion.display_name;
                },

                scrollToSelected() {
                    // Helper to scroll suggestion list
                    this.$nextTick(() => {
                        const container = document.querySelector('.max-h-60');
                        if (!container) return;
                        const selected = container.children[this.selectedIndex];
                        if (selected) {
                            selected.scrollIntoView({
                                block: 'nearest'
                            });
                        }
                    });
                },

                // ============ GPS DETECTION ============
                detectLocation() {
                    if (!navigator.geolocation) {
                        this.gpsStatus = 'failed';
                        return;
                    }

                    this.gpsStatus = 'detecting';

                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;

                            this.form.latitude = lat.toFixed(7);
                            this.form.longitude = lng.toFixed(7);

                            // Update map and marker
                            if (this.map && this.marker) {
                                this.marker.setLatLng([lat, lng]);
                                this.map.flyTo([lat, lng], 18, {
                                    duration: 1.5
                                });
                                this.marker.openPopup();
                            } else {
                                // If map not initialized yet, store for later
                                this.$nextTick(() => {
                                    if (this.map && this.marker) {
                                        this.marker.setLatLng([lat, lng]);
                                        this.map.setView([lat, lng], 18);
                                    }
                                });
                            }

                            this.gpsStatus = 'success';
                        },
                        (error) => {
                            console.log('Geolocation error:', error.message);
                            this.gpsStatus = 'failed';
                        }, {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 0
                        }
                    );
                },

                // ============ MANUAL COORDINATES ============
                toggleManualCoord() {
                    this.showManualCoord = !this.showManualCoord;
                },

                onManualCoordChange() {
                    const lat = parseFloat(this.form.latitude);
                    const lng = parseFloat(this.form.longitude);

                    if (!isNaN(lat) && !isNaN(lng) && this.map && this.marker) {
                        this.marker.setLatLng([lat, lng]);
                        this.map.flyTo([lat, lng], 16, {
                            duration: 1
                        });
                        this.gpsStatus = 'idle';
                    }
                },

                // ============ IMAGE HANDLING ============
                handleImageSelect(event) {
                    const file = event.target.files[0];
                    if (file) this.processImage(file);
                },

                handleImageDrop(event) {
                    const file = event.dataTransfer.files[0];
                    if (file) this.processImage(file);
                },

                processImage(file) {
                    // Validate file type
                    if (!['image/jpeg', 'image/png'].includes(file.type)) {
                        alert('Please upload a JPG or PNG image.');
                        return;
                    }

                    // Validate file size (max 2MB)
                    if (file.size > 2 * 1024 * 1024) {
                        alert('Image size must be less than 2MB.');
                        return;
                    }

                    this.imageFile = file;

                    // Create preview
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        this.imagePreview = e.target.result;
                    };
                    reader.readAsDataURL(file);
                },

                removeImage() {
                    this.imagePreview = null;
                    this.imageFile = null;
                    if (this.$refs.imageInput) {
                        this.$refs.imageInput.value = '';
                    }
                },

                // ============ STEP NAVIGATION ============
                nextStep() {
                    // Validate step 1
                    if (!this.form.name.trim()) {
                        alert('Please enter restaurant name.');
                        return;
                    }
                    if (!this.form.description.trim()) {
                        alert('Please enter description.');
                        return;
                    }
                    if (!this.form.address.trim()) {
                        alert('Please enter address.');
                        return;
                    }
                    if (!this.imageFile) {
                        alert('Please upload a restaurant image.');
                        return;
                    }
                    this.currentStep = 2;
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                },

                validateAndProceed() {
                    // Validate step 2
                    if (!this.form.latitude || !this.form.longitude) {
                        alert('Please set the location on the map.');
                        return;
                    }
                    this.currentStep = 3;
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                },

                // ============ SUBMISSION ============
                async submitRegistration() {
                    if (!this.form.agreed) {
                        alert('Please agree to the Terms & Conditions.');
                        return;
                    }

                    this.submitting = true;

                    const formData = new FormData();
                    formData.append('_token', '{{ csrf_token() }}');
                    formData.append('name', this.form.name);
                    formData.append('description', this.form.description);
                    formData.append('address', this.form.address);
                    formData.append('latitude', this.form.latitude);
                    formData.append('longitude', this.form.longitude);

                    if (this.imageFile) {
                        formData.append('img_url', this.imageFile);
                    }

                    try {
                        const response = await fetch('{{ route('store.submit') }}', {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        // Di submitRegistration()
                        if (response.ok) {
                            this.currentStep = 4;
                            window.scrollTo({
                                top: 0,
                                behavior: 'smooth'
                            });
                        } else if (response.status === 422) {
                            const data = await response.json();
                            // Tampilkan error validasi
                            const errors = Object.values(data.errors).flat().join('\n');
                            alert(errors);
                        } else {
                            const data = await response.json();
                            alert(data.message || 'An error occurred. Please try again.');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        alert('Network error. Please try again.');
                    } finally {
                        this.submitting = false;
                    }
                }
            }));
        });
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .safe-area-bottom {
            padding-bottom: env(safe-area-inset-bottom);
        }

        /* Leaflet z-index fix */
        .leaflet-container {
            z-index: 1;
        }
    </style>
</body>

</html>
