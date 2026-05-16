    @extends('layouts.seller')

    @section('title', 'Menu Management - Rantangku')

    @section('content')
        <div>
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-xl lg:text-2xl font-bold">Menu Management</h1>
                    <p class="text-sm text-base-content/50 mt-1">Manage your regular menu and surplus food</p>
                </div>
                <button onclick="document.getElementById('foodSlideover').showModal()" class="btn btn-warning gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Add New Food
                </button>
            </div>

            @if (session('success'))
                <div role="alert" class="alert alert-success">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @error('surplus')
                <div class="alert alert-error mb-3">
                    {{ $message }}
                </div>
            @enderror

            <!-- Tabs -->
            <div class="flex gap-1 bg-base-100 border border-base-200 shadow-sm rounded-xl p-1 mb-6 w-fit">
                <button class="tab-btn tab-active" data-tab="regular" onclick="switchTab('regular')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>
                    Regular Menu
                    <span class="badge badge-sm ml-1.5">{{ $regularMenus->count() ?? 0 }}</span>
                </button>
                <button class="tab-btn" data-tab="surplus" onclick="switchTab('surplus')">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Surplus Menu
                    <span class="badge badge-warning badge-sm ml-1.5">{{ $surplusMenus->count() ?? 0 }}</span>
                </button>
            </div>

            <!-- ============ REGULAR MENU TAB ============ -->
            <div id="tab-regular" class="tab-content block">
                @if (isset($regularMenus) && count($regularMenus) > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach ($regularMenus as $menu)
                            <div
                                class="card bg-base-100 border border-base-200 shadow-sm hover:shadow-md transition-all group">
                                <!-- Food Image -->
                                <figure class="relative h-48 overflow-hidden">
                                    <img src="{{ $menu->productImg->first()?->img_url ?? 'https://via.placeholder.com/400x300?text=No+Image' }}"
                                        alt="{{ $menu->name }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

                                    <!-- Status Badge -->
                                    <div class="absolute top-3 left-3">
                                        @if ($menu->is_active)
                                            <span class="badge badge-success badge-sm gap-1">
                                                <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                                                Active
                                            </span>
                                        @else
                                            <span class="badge badge-ghost badge-sm bg-base-300/80">Inactive</span>
                                        @endif
                                    </div>

                                    <!-- Image Count -->
                                    <div
                                        class="absolute top-3 right-3 bg-black/50 backdrop-blur text-white text-xs px-2 py-1 rounded-full">
                                        {{ $menu->productImg->count() ?? 0 }}
                                    </div>
                                </figure>

                                <!-- Card Body -->
                                <div class="card-body p-4">
                                    <span
                                        class="badge badge-outline badge-xs mb-2">{{ $menu->category->name ?? 'Uncategorized' }}</span>

                                    <h3 class="card-title text-base font-semibold mb-1 truncate">{{ $menu->name }}</h3>

                                    <p class="text-xs text-base-content/50 line-clamp-2 mb-3">
                                        {{ $menu->description ?? 'No description' }}
                                    </p>

                                    <div class="flex items-center justify-between mb-3">
                                        <span class="text-lg font-bold text-warning">
                                            Rp {{ number_format($menu->price, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    <!-- Card Actions -->
                                    <div class="card-actions justify-end gap-2 pt-2 border-t border-base-200">
                                        <button
                                            onclick="openEditModal({{ $menu->id }}, '{{ $menu->name }}', '{{ $menu->price }}', '{{ $menu->category_id }}', '{{ $menu->description }}', {{ $menu->is_active ? 'true' : 'false' }})"
                                            class="btn btn-ghost btn-xs gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </button>
                                        <button
                                            onclick="openSurplusModal({
                                                id: {{ $menu->id }},
                                                name: '{{ $menu->name }}',
                                                price: {{ $menu->price }},
                                                image: '{{ $menu->productImg->first()?->img_url ?? 'https://via.placeholder.com/60' }}'
                                            })"
                                            class="btn btn-warning btn-xs btn-outline gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            Surplus
                                        </button>
                                        <div class="dropdown dropdown-end">
                                            <label tabindex="0" class="btn btn-ghost btn-xs btn-square">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <circle cx="12" cy="5" r="2" />
                                                    <circle cx="12" cy="12" r="2" />
                                                    <circle cx="12" cy="19" r="2" />
                                                </svg>
                                            </label>
                                            <ul tabindex="0"
                                                class="dropdown-content z-1 menu p-2 shadow-lg bg-base-100 rounded-box w-40 border border-base-200">
                                                <li><a onclick="document.getElementById('deleteConfirmModal').showModal()"
                                                        class="cursor-pointer">
                                                        {{ $menu->is_active ? 'Deactivate' : 'Activate' }}
                                                    </a></li>
                                                <li><a onclick="openDeleteModal({{ $menu->id }}, '{{ $menu->name }}')"
                                                        class="text-error cursor-pointer">Delete</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Empty State -->
                    <div class="text-center py-16">
                        <div class="w-24 h-24 bg-base-200 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-12 h-12 text-base-content/30" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-base-content/50 mb-1">No Menu Yet</h3>
                        <p class="text-sm text-base-content/40 mb-4">Start adding your delicious food to the menu!</p>
                        <button onclick="document.getElementById('foodSlideover').showModal()"
                            class="btn btn-warning gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            Add Your First Food
                        </button>
                    </div>
                @endif
            </div>

            <!-- ============ SURPLUS MENU TAB ============ -->
            <div id="tab-surplus" class="tab-content hidden">
                @if (isset($surplusMenus) && count($surplusMenus) > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                        @foreach ($surplusMenus as $surplus)
                            <div class="card bg-base-100 border border-warning/30 shadow-sm hover:shadow-md transition-all group relative"
                                data-surplus-id="{{ $surplus->id }}">
                                <!-- Surplus Ribbon -->
                                <div class="absolute top-0 right-0 z-10">
                                    <div
                                        class="bg-warning text-warning-content text-xs font-bold px-4 py-1.5 rounded-bl-xl shadow-md">
                                        SURPLUS
                                    </div>
                                </div>

                                <!-- Food Image -->
                                <figure class="relative h-48 overflow-hidden">
                                    <img src="{{ $surplus->product->productImg->first()?->img_url ?? 'https://via.placeholder.com/400x300?text=No+Image' }}"
                                        alt="{{ $surplus->product->name ?? 'Surplus Food' }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">

                                    <!-- Timer Badge -->
                                    <div
                                        class="absolute bottom-3 left-3 bg-black/70 backdrop-blur text-white text-xs px-2.5 py-1 rounded-full flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ \Carbon\Carbon::parse($surplus->expired_at)->diffForHumans() }}
                                    </div>
                                </figure>

                                <!-- Card Body -->
                                <div class="card-body p-4">
                                    <span
                                        class="badge badge-outline badge-xs mb-2">{{ $surplus->product->category->name ?? 'Uncategorized' }}</span>

                                    <h3 class="card-title text-base font-semibold mb-1 truncate">
                                        {{ $surplus->product->name ?? 'Surplus Food' }}</h3>

                                    <!-- Price Comparison -->
                                    <div class="flex items-baseline gap-2 mb-1">
                                        <span class="text-lg font-bold text-warning">
                                            Rp {{ number_format($surplus->discount_price, 0, ',', '.') }}
                                        </span>
                                        <span class="text-sm text-base-content/40 line-through">
                                            Rp {{ number_format($surplus->initial_price, 0, ',', '.') }}
                                        </span>
                                    </div>

                                    <!-- Discount Badge -->
                                    @php
                                        $discount =
                                            $surplus->initial_price > 0
                                                ? round(
                                                    (($surplus->initial_price - $surplus->discount_price) /
                                                        $surplus->initial_price) *
                                                        100,
                                                )
                                                : 0;
                                    @endphp
                                    <span class="badge badge-error badge-sm mb-2">🔥 {{ $discount }}% OFF</span>

                                    <!-- Stock Progress -->
                                    <div class="mb-3">
                                        <div class="flex items-center justify-between text-xs text-base-content/50 mb-1.5">
                                            <span>Stock</span>
                                            <span><strong>{{ $surplus->remaining_quantity }}</strong> /
                                                {{ $surplus->quantity }}</span>
                                        </div>
                                        <progress class="progress progress-warning w-full h-2"
                                            value="{{ $surplus->remaining_quantity }}"
                                            max="{{ $surplus->quantity }}"></progress>
                                    </div>

                                    <!-- Status -->
                                    <div class="flex items-center justify-between text-xs mb-3">
                                        @if ($surplus->status == 'active')
                                            <span class="badge badge-success badge-xs gap-1 surplus-status-badge">
                                                <span class="w-1 h-1 bg-white rounded-full"></span> Available
                                            </span>
                                        @elseif($surplus->status == 'sold_out')
                                            <span class="badge badge-error badge-xs surplus-status-badge">Sold Out</span>
                                        @else
                                            <span class="badge badge-ghost badge-xs surplus-status-badge">Expired</span>
                                        @endif


                                        @if ($surplus->pickup_start_at && $surplus->pickup_end_at)
                                            <span class="text-base-content/40">
                                                🕐
                                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $surplus->pickup_start_at)->format('H:i') }}
                                                -
                                                {{ \Carbon\Carbon::createFromFormat('H:i:s', $surplus->pickup_end_at)->format('H:i') }}
                                            </span>
                                        @endif
                                    </div>

                                    <!-- Card Actions -->
                                    <div class="card-actions justify-end gap-2 pt-2 border-t border-base-200">
                                        <button
                                            onclick="openSurplusEditModal({
                                                id: {{ $surplus->id }},
                                                productName: '{{ addslashes($surplus->product->name ?? '') }}',
                                                image: '{{ $surplus->product->productImg->first()?->img_url ?? 'https://via.placeholder.com/60' }}',
                                                initialPrice: {{ $surplus->initial_price }},
                                                discountPrice: {{ $surplus->discount_price }},
                                                quantity: {{ $surplus->quantity }},
                                                remainingQuantity: {{ $surplus->remaining_quantity }},
                                                status: '{{ $surplus->status }}',
                                                pickupStart: '{{ $surplus->pickup_start_at }}',
                                                pickupEnd: '{{ $surplus->pickup_end_at }}',
                                                expiredAt: '{{ $surplus->expired_at }}'
                                            })"
                                            class="btn btn-ghost btn-xs gap-1">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </button>
                                        <div class="dropdown dropdown-end">
                                            <label tabindex="0" class="btn btn-ghost btn-xs btn-square">
                                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <circle cx="12" cy="5" r="2" />
                                                    <circle cx="12" cy="12" r="2" />
                                                    <circle cx="12" cy="19" r="2" />
                                                </svg>
                                            </label>
                                            <ul tabindex="0"
                                                class="dropdown-content z-1 menu p-2 shadow-lg bg-base-100 rounded-box w-44 border border-base-200">
                                                <li>
                                                    <a onclick="openDeleteSurplusModal({
                                                            id: {{ $surplus->id }},
                                                            productName: '{{ addslashes($surplus->product->name ?? '') }}',
                                                            image: '{{ $surplus->product->productImg->first()?->img_url ?? 'https://via.placeholder.com/60' }}',
                                                            discountPrice: {{ $surplus->discount_price }},
                                                            remainingQuantity: {{ $surplus->remaining_quantity }}
                                                        })"
                                                        class="text-error cursor-pointer">Delete</a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <!-- Empty State Surplus -->
                    <div class="text-center py-16">
                        <div class="w-24 h-24 bg-warning/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-12 h-12 text-warning/50" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-base-content/50 mb-1">No Surplus Menu Yet</h3>
                        <p class="text-sm text-base-content/40 mb-4">Convert your regular menu items to surplus for quick
                            sales!</p>
                        <button onclick="switchTab('regular')" class="btn btn-warning btn-outline gap-2">
                            Browse Regular Menu
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- ============ ADD FOOD MODAL ============ -->
        <dialog id="foodSlideover" class="modal modal-bottom sm:modal-middle">
            <div class="modal-box max-w-lg p-0 overflow-hidden">
                <!-- Modal Header -->
                <div class="bg-orange-500 p-5 text-warning-content">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold">Add New Food</h3>
                        <button onclick="document.getElementById('foodSlideover').close()"
                            class="btn btn-ghost btn-sm btn-circle text-warning-content">✕</button>
                    </div>
                </div>

                <!-- Modal Body -->
                <form action="{{ route('seller.menu-create') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="p-5 space-y-4 max-h-[70vh] overflow-y-auto">
                        <!-- Image Upload -->
                        <div>
                            <label class="label py-1"><span class="label-text font-medium text-sm">Food Images <span
                                        class="text-base-content/40">(Max 8)</span></span></label>
                            <div class="border-2 border-dashed border-base-300 rounded-xl p-6 text-center cursor-pointer hover:border-warning transition-colors"
                                onclick="document.getElementById('imageInput').click()">
                                <input type="file" id="imageInput" multiple accept="image/*" class="hidden"
                                    name="images[]">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-10 h-10 text-base-content/30" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-sm font-medium">Click to upload</p>
                                    <p class="text-xs text-base-content/40">JPG, PNG up to 2MB each</p>
                                </div>
                            </div>
                            <!-- Preview Container -->
                            <div id="imagePreviewContainer" class="flex gap-2 mt-3 flex-wrap"></div>
                        </div>

                        <!-- Food Name -->
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text font-medium text-sm">Food Name
                                    *</span></label>
                            <input name="name" type="text"
                                class="input input-bordered w-full focus:input-warning text-sm"
                                placeholder="e.g., Nasi Goreng Special">
                        </div>

                        <!-- Category -->
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text font-medium text-sm">Category
                                    *</span></label>
                            <select name="category_id" class="select select-bordered w-full focus:select-warning text-sm">
                                <option value="">Select category</option>
                                @foreach ($category as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Description -->
                        <div class="form-control">
                            <label class="label py-1"><span
                                    class="label-text font-medium text-sm">Description</span></label>
                            <textarea name="description" class="textarea textarea-bordered w-full focus:textarea-warning text-sm h-20"
                                placeholder="Describe your food..."></textarea>
                        </div>

                        <!-- Price -->
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text font-medium text-sm">Price (Rp)
                                    *</span></label>
                            <input name="price" type="number"
                                class="input input-bordered w-full focus:input-warning text-sm" placeholder="25000"
                                min="0">
                        </div>

                        <!-- Status Toggle -->
                        <div class="flex items-center justify-between bg-base-200 rounded-xl p-3">
                            <div>
                                <p class="text-sm font-medium">Active</p>
                                <p class="text-xs text-base-content/50">Make this food available for customers</p>
                            </div>
                            <input name="is_active" type="hidden" value="0">
                            <input name="is_active" type="checkbox" class="toggle toggle-warning" checked
                                value="1">
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-error mb-4 mx-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Modal Footer -->
                    <div class="p-5 pt-0 flex gap-3">
                        <button type="button" onclick="document.getElementById('foodSlideover').close()"
                            class="btn btn-ghost flex-1">Cancel</button>
                        <button type="submit" class="btn btn-warning flex-1 gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Save Food
                        </button>
                    </div>
                </form>
            </div>

            <div class="modal-backdrop">
                <button type="button" onclick="document.getElementById('foodSlideover').close()">close</button>
            </div>
        </dialog>

        {{-- EDIT FOOD MODAL --}}
        <dialog id="foodUpdateSlideover" class="modal modal-bottom sm:modal-middle">
            <div class="modal-box max-w-lg p-0 overflow-hidden">
                <!-- Modal Header -->
                <div class="bg-orange-500 p-5 text-warning-content">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold">Update Food</h3>
                        <button onclick="document.getElementById('foodUpdateSlideover').close()"
                            class="btn btn-ghost btn-sm btn-circle text-warning-content">✕</button>
                    </div>
                </div>

                <!-- Modal Body -->
                <form id="editForm" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="p-5 space-y-4 max-h-[70vh] overflow-y-auto">
                        <!-- Image Upload -->
                        <div>
                            <label class="label py-1"><span class="label-text font-medium text-sm">Food Images <span
                                        class="text-base-content/40">(Max 8)</span></span></label>
                            <div class="border-2 border-dashed border-base-300 rounded-xl p-6 text-center cursor-pointer hover:border-warning transition-colors"
                                onclick="document.getElementById('imageInput').click()">
                                <input type="file" id="imageInputEdit" multiple accept="image/*" class="hidden"
                                    name="images[]">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-10 h-10 text-base-content/30" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <p class="text-sm font-medium">Click to upload</p>
                                    <p class="text-xs text-base-content/40">JPG, PNG up to 2MB each</p>
                                </div>
                            </div>
                            <!-- Preview Container -->
                            <div id="imagePreviewContainerEdit" class="flex gap-2 mt-3 flex-wrap"></div>
                        </div>

                        <!-- Food Name -->
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text font-medium text-sm">Food Name
                                    *</span></label>
                            <input name="name" type="text" id="editName"
                                class="input input-bordered w-full focus:input-warning text-sm"
                                placeholder="e.g., Nasi Goreng Special">
                        </div>

                        <!-- Category -->
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text font-medium text-sm">Category
                                    *</span></label>
                            <select name="category_id" id="editCategory"
                                class="select select-bordered w-full focus:select-warning text-sm">
                                <option value="">Select category</option>
                                @foreach ($category as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Description -->
                        <div class="form-control">
                            <label class="label py-1"><span
                                    class="label-text font-medium text-sm">Description</span></label>
                            <textarea id="editDescription" name="description"
                                class="textarea textarea-bordered w-full focus:textarea-warning text-sm h-20" placeholder="Describe your food..."></textarea>
                        </div>

                        <!-- Price -->
                        <div class="form-control">
                            <label class="label py-1"><span class="label-text font-medium text-sm">Price (Rp)
                                    *</span></label>
                            <input id="editPrice" name="price" type="number"
                                class="input input-bordered w-full focus:input-warning text-sm" placeholder="25000"
                                min="0">
                        </div>

                        <!-- Status Toggle -->
                        <div class="flex items-center justify-between bg-base-200 rounded-xl p-3">
                            <div>
                                <p class="text-sm font-medium">Active</p>
                                <p class="text-xs text-base-content/50">Make this food available for customers</p>
                            </div>
                            <input name="is_active" type="hidden" value="0">
                            <input name="is_active" type="checkbox" class="toggle toggle-warning" id="editActive"
                                checked value="1">
                        </div>
                    </div>

                    @if ($errors->any())
                        <div class="alert alert-error mb-4 mx-4">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Modal Footer -->
                    <div class="p-5 pt-0 flex gap-3">
                        <button type="button" onclick="document.getElementById('foodUpdateSlideover').close()"
                            class="btn btn-ghost flex-1">Cancel</button>
                        <button type="submit" class="btn btn-warning flex-1 gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Update Food
                        </button>
                    </div>
                </form>
            </div>

            <div class="modal-backdrop">
                <button type="button" onclick="document.getElementById('foodUpdateSlideover').close()">close</button>
            </div>
        </dialog>

        <!-- ============ UPDATE SURPLUS MODAL ============ -->
        <dialog id="surplusUpdateSlideover" class="modal modal-bottom sm:modal-middle">
            <div class="modal-box max-w-lg p-0 overflow-hidden">
                <!-- Modal Header -->
                <div class="bg-amber-500 p-5 text-white">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold">Update Surplus Deal</h3>
                        <button type="button" onclick="document.getElementById('surplusUpdateSlideover').close()"
                            class="btn btn-ghost btn-sm btn-circle text-white">✕</button>
                    </div>
                </div>

                <form id="surplusUpdateForm" method="POST" action="">
                    @csrf
                    @method('PUT')

                    <div class="p-5 space-y-4 max-h-[70vh] overflow-y-auto">

                        <!-- Selected Food Info (readonly preview) -->
                        <div class="bg-base-200 rounded-xl p-4 flex items-center gap-3">
                            <img id="surplusUpdateImage" src="" alt="Food"
                                class="w-14 h-14 rounded-xl object-cover">
                            <div>
                                <p id="surplusUpdateName" class="text-sm font-semibold"></p>
                                <p class="text-xs text-base-content/50">Original price:
                                    <span id="surplusUpdatePriceDisplay"></span>
                                </p>
                            </div>
                        </div>

                        <!-- Original Price (readonly) -->
                        <div class="form-control">
                            <label class="label py-1">
                                <span class="label-text font-medium text-sm">Original Price</span>
                            </label>
                            <input type="text" id="surplusUpdateOriginalPriceDisplay"
                                class="input input-bordered w-full bg-base-200 text-sm" readonly>
                            <input type="hidden" name="initial_price" id="surplusUpdateInitialPrice">
                        </div>

                        <!-- Surplus Price -->
                        <div class="form-control">
                            <label class="label py-1">
                                <span class="label-text font-medium text-sm">Surplus Price (Rp) *</span>
                            </label>
                            <input type="number" name="discount_price" id="surplusUpdateCurrentPrice"
                                class="input input-bordered w-full focus:input-warning text-sm" placeholder="15000"
                                min="0" required>
                            <label class="label py-1">
                                <span class="label-text-alt text-warning">💡 Suggested: 40-60% of original price</span>
                            </label>
                        </div>

                        <!-- Quantity & Remaining -->
                        <div class="grid grid-cols-2 gap-3">
                            <div class="form-control">
                                <label class="label py-1">
                                    <span class="label-text font-medium text-sm">Total Quantity *</span>
                                </label>
                                <input type="number" name="quantity" id="surplusUpdateQuantity"
                                    class="input input-bordered w-full focus:input-warning text-sm" placeholder="10"
                                    min="1" required>
                            </div>
                            <div class="form-control">
                                <label class="label py-1">
                                    <span class="label-text font-medium text-sm">Remaining *</span>
                                </label>
                                <input type="number" name="remaining_quantity" id="surplusUpdateRemaining"
                                    class="input input-bordered w-full focus:input-warning text-sm" placeholder="10"
                                    min="0" required>
                            </div>
                        </div>

                        <!-- Pickup Time Range -->
                        <div class="grid grid-cols-2 gap-3">
                            <div class="form-control">
                                <label class="label py-1">
                                    <span class="label-text font-medium text-sm">Pickup Start *</span>
                                </label>
                                <input type="time" name="pickup_start_at" id="surplusUpdatePickupStart"
                                    class="input input-bordered w-full focus:input-warning text-sm" required>
                            </div>
                            <div class="form-control">
                                <label class="label py-1">
                                    <span class="label-text font-medium text-sm">Pickup End *</span>
                                </label>
                                <input type="time" name="pickup_end_at" id="surplusUpdatePickupEnd"
                                    class="input input-bordered w-full focus:input-warning text-sm" required>
                            </div>
                        </div>

                        <!-- Expiry -->
                        <div class="form-control">
                            <label class="label py-1">
                                <span class="label-text font-medium text-sm">Expiry Date *</span>
                            </label>
                            <input type="datetime-local" name="expired_at" id="surplusUpdateExpiredAt"
                                class="input input-bordered w-full focus:input-warning text-sm" required>
                        </div>

                    </div>

                    <!-- Modal Footer -->
                    <div class="p-5 pt-0 flex gap-3">
                        <button type="button" onclick="document.getElementById('surplusUpdateSlideover').close()"
                            class="btn btn-ghost flex-1">Cancel</button>
                        <button type="submit" class="btn btn-warning flex-1 gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Update Surplus
                        </button>
                    </div>
                </form>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>

        <!-- ============ MAKE SURPLUS MODAL ============ -->
        <dialog id="surplusSlideover" class="modal modal-bottom sm:modal-middle">
            <div class="modal-box max-w-lg p-0 overflow-hidden">
                <!-- Modal Header -->
                <div class="bg-amber-500 p-5 text-white">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <h3 class="text-lg font-bold">Create Surplus Deal</h3>
                        </div>
                        <button type="button" onclick="document.getElementById('surplusSlideover').close()"
                            class="btn btn-ghost btn-sm btn-circle text-white">✕</button>
                    </div>
                </div>

                <!-- Wrap semua dalam form -->
                <form id="surplusForm" method="POST" action="">
                    @csrf

                    <!-- Modal Body -->
                    <div class="p-5 space-y-4 max-h-[70vh] overflow-y-auto">

                        <input type="hidden" name="product_id" id="surplusProductId">

                        <!-- Selected Food Info -->
                        <div class="bg-base-200 rounded-xl p-4 flex items-center gap-3">
                            <img id="surplusImage" src="" alt="Food"
                                class="w-14 h-14 rounded-xl object-cover">
                            <div>
                                <p id="surplusName" class="text-sm font-semibold"></p>
                                <p class="text-xs text-base-content/50">
                                    Original price: <span id="surplusPriceDisplay"></span>
                                </p>
                            </div>
                        </div>

                        <!-- Initial Price (readonly, nilai dinamis) -->
                        <div class="form-control">
                            <label class="label py-1">
                                <span class="label-text font-medium text-sm">Original Price</span>
                            </label>
                            <input type="text" id="surplusOriginalPriceDisplay"
                                class="input input-bordered w-full bg-base-200 text-sm" readonly>
                            <!-- hidden untuk dikirim ke server -->
                            <input type="hidden" name="initial_price" id="surplusInitialPrice">
                        </div>

                        <!-- Surplus Price -->
                        <div class="form-control">
                            <label class="label py-1">
                                <span class="label-text font-medium text-sm">Surplus Price (Rp) *</span>
                            </label>
                            <input type="number" name="discount_price" id="surplusCurrentPrice"
                                class="input input-bordered w-full focus:input-warning text-sm" placeholder="15000"
                                min="0" required>
                            <label class="label py-1">
                                <span class="label-text-alt text-warning">
                                    💡 Suggested: 40-60% of original price
                                </span>
                            </label>
                        </div>

                        <!-- Quantity -->
                        <div class="grid grid-cols-2 gap-3">
                            <div class="form-control">
                                <label class="label py-1">
                                    <span class="label-text font-medium text-sm">Quantity *</span>
                                </label>
                                <input type="number" name="quantity" id="surplusQuantity"
                                    class="input input-bordered w-full focus:input-warning text-sm" placeholder="10"
                                    min="1" required oninput="syncRemaining(this.value)">
                            </div>
                            <div class="form-control">
                                <label class="label py-1">
                                    <span class="label-text font-medium text-sm">Remaining</span>
                                </label>
                                <!-- remaining = quantity saat pertama dibuat -->
                                <input type="number" name="remaining_quantity" id="surplusRemaining"
                                    class="input input-bordered w-full bg-base-200 text-sm" readonly>
                            </div>
                        </div>

                        <!-- Pickup Time Range -->
                        <div class="grid grid-cols-2 gap-3">
                            <div class="form-control">
                                <label class="label py-1">
                                    <span class="label-text font-medium text-sm">Pickup Start *</span>
                                </label>
                                <input type="time" name="pickup_start_at"
                                    class="input input-bordered w-full focus:input-warning text-sm" required>
                            </div>
                            <div class="form-control">
                                <label class="label py-1">
                                    <span class="label-text font-medium text-sm">Pickup End *</span>
                                </label>
                                <input type="time" name="pickup_end_at"
                                    class="input input-bordered w-full focus:input-warning text-sm" required>
                            </div>
                        </div>

                        <!-- Expiry -->
                        <div class="form-control">
                            <label class="label py-1">
                                <span class="label-text font-medium text-sm">Expiry Date *</span>
                            </label>
                            <input type="datetime-local" name="expired_at"
                                class="input input-bordered w-full focus:input-warning text-sm" required>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="p-5 pt-0 flex gap-3">
                        <button type="button" onclick="document.getElementById('surplusSlideover').close()"
                            class="btn btn-ghost flex-1">Cancel</button>
                        <button type="submit" class="btn btn-warning flex-1 gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Create Surplus
                        </button>
                    </div>
                </form>

            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>

        <!-- ============ DELETE CONFIRMATION MODAL ============ -->
        <dialog id="deleteConfirmModal" class="modal">
            <div class="modal-box max-w-md text-center">
                <div class="w-16 h-16 bg-error/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                <h3 class="text-lg font-bold mb-2">Delete Menu?</h3>
                <p class="text-sm text-base-content/60 mb-2">Apakah Anda yakin ingin menghapus item <span
                        id="name-item"></span> ini?</p>
                <p class="text-xs text-base-content/40 mb-6">Tindakan ini tidak dapat dibatalkan.</p>
                <div class="flex gap-3 justify-center">
                    <button type="button" onclick="document.getElementById('deleteConfirmModal').close()"
                        class="btn btn-ghost">Cancel</button>
                    <form id="formDelete" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-error gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Delete Permanently
                        </button>
                    </form>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>

        <!-- ============ DELETE SURPLUS CONFIRMATION MODAL ============ -->
        <dialog id="deleteSurplusModal" class="modal">
            <div class="modal-box max-w-md text-center">
                <div class="w-16 h-16 bg-warning/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-warning" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <h3 class="text-lg font-bold mb-2">Hapus Surplus?</h3>

                <!-- Preview item yang akan dihapus -->
                <div class="bg-base-200 rounded-xl p-3 flex items-center gap-3 text-left mb-3">
                    <img id="deleteSurplusImage" src="" alt=""
                        class="w-12 h-12 rounded-lg object-cover shrink-0">
                    <div class="min-w-0">
                        <p id="deleteSurplusName" class="text-sm font-semibold truncate"></p>
                        <p id="deleteSurplusPrice" class="text-xs text-base-content/50 mt-0.5"></p>
                    </div>
                </div>

                <p class="text-sm text-base-content/60 mb-2">
                    Apakah Anda yakin ingin menghapus surplus ini?
                </p>
                <p class="text-xs text-base-content/40 mb-6">Tindakan ini tidak dapat dibatalkan.</p>

                <div class="flex gap-3 justify-center">
                    <button type="button" onclick="document.getElementById('deleteSurplusModal').close()"
                        class="btn btn-ghost">Cancel</button>

                    <form id="formDeleteSurplus" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-error gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            Hapus Surplus
                        </button>
                    </form>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop">
                <button>close</button>
            </form>
        </dialog>

    @endsection

    @push('scripts')
        <script>
            // Ambil store_id seller yang login 
            const storeId = {{ auth()->user()->stores->first()->id }};

            const pusher = new Pusher('{{ env('PUSHER_APP_KEY') }}', {
                cluster: '{{ env('PUSHER_APP_CLUSTER') }}'
            });

            const channel = pusher.subscribe(`store.${storeId}.surplus`);

            channel.bind('status.updated', function(data) {
                updateSurplusCardStatus(data.id, data.status);
            });

            function updateSurplusCardStatus(surplusId, newStatus) {
                // Cari badge status di card yang sesuai
                const badge = document.querySelector(`[data-surplus-id="${surplusId}"] .surplus-status-badge`);
                if (!badge) return;

                // Map status ke tampilan badge
                const statusMap = {
                    active: {
                        class: 'badge-success',
                        label: 'Available'
                    },
                    sold_out: {
                        class: 'badge-error',
                        label: 'Sold Out'
                    },
                    expired: {
                        class: 'badge-ghost',
                        label: 'Expired'
                    },
                };

                const config = statusMap[newStatus];
                if (!config) return;

                // Reset semua class lama, terapkan yang baru
                badge.className = `badge badge-xs surplus-status-badge ${config.class}`;
                badge.textContent = config.label;

                // Visual feedback — flash card border sebentar
                const card = badge.closest('.card');
                if (card) {
                    card.style.outline = '2px solid #f97316';
                    setTimeout(() => card.style.outline = '', 2000);
                }
            }

            // ============ TAB SWITCHING ============
            function switchTab(tabName) {
                // Update tab buttons
                document.querySelectorAll('.tab-btn').forEach(btn => {
                    btn.classList.remove('tab-active');
                    if (btn.dataset.tab === tabName) {
                        btn.classList.add('tab-active');
                    }
                });

                // Update tab content
                document.querySelectorAll('.tab-content').forEach(content => {
                    content.classList.add('hidden');
                    content.classList.remove('block');
                });

                const target = document.getElementById('tab-' + tabName);
                if (target) {
                    target.classList.remove('hidden');
                    target.classList.add('block');
                }
            }

            const updateUrlTemplate = "{{ route('seller.menu-update', ':productId') }}";
            const deleteUrlTemplate = "{{ route('seller.menu-delete', ':productId') }}";

            // ============ SHOW MODAL DELETE ============
            function openDeleteModal(id, name) {
                const modal = document.getElementById('deleteConfirmModal')

                const actionUrl = deleteUrlTemplate.replace(':productId', id)
                document.getElementById('formDelete').action = actionUrl;
                document.getElementById('name-item').textContent = name;

                modal.showModal()
            }

            // ============ SHOW MODAL EDIT ============
            function openEditModal(id, name, price, categoryId, description, isActive) {
                const modal = document.getElementById('foodUpdateSlideover');

                // set action
                const actionUrl = updateUrlTemplate.replace(':productId', id);
                document.getElementById('editForm').action = actionUrl;

                // fill data
                document.getElementById('editName').value = name;
                document.getElementById('editPrice').value = price;
                document.getElementById('editDescription').value = description ?? '';
                document.getElementById('editCategory').value = categoryId;
                document.getElementById('editActive').checked = isActive;

                modal.showModal();
            }

            const surplusUpdateUrlTemplate = "{{ route('seller.surplus-update', ':surplusId') }}";

            // ============ SHOW MODAL EDIT SUPRLUS ============
            function openSurplusEditModal(surplus) {
                // Set action URL dengan ID surplus
                const actionUrl = surplusUpdateUrlTemplate.replace(':surplusId', surplus.id);
                document.getElementById('surplusUpdateForm').action = actionUrl;

                // Preview info (readonly)
                document.getElementById('surplusUpdateName').textContent = surplus.productName;
                document.getElementById('surplusUpdatePriceDisplay').textContent = formatRupiah(surplus.initialPrice);
                document.getElementById('surplusUpdateOriginalPriceDisplay').value = formatRupiah(surplus.initialPrice);
                document.getElementById('surplusUpdateInitialPrice').value = surplus.initialPrice;
                document.getElementById('surplusUpdateImage').src = surplus.image;

                // Isi field form
                document.getElementById('surplusUpdateCurrentPrice').value = surplus.discountPrice;
                document.getElementById('surplusUpdateQuantity').value = surplus.quantity;
                document.getElementById('surplusUpdateRemaining').value = surplus.remainingQuantity;

                // Format datetime-local: "2025-05-10T14:30"
                document.getElementById('surplusUpdatePickupStart').value = toDatetimeLocal(surplus.pickupStart);
                document.getElementById('surplusUpdatePickupEnd').value = toDatetimeLocal(surplus.pickupEnd);
                document.getElementById('surplusUpdateExpiredAt').value = toDatetimeLocal(surplus.expiredAt);

                document.getElementById('surplusUpdateSlideover').showModal();
            }

            // Helper: ubah "2025-05-10 14:30:00" → "2025-05-10T14:30"
            function toDatetimeLocal(datetimeStr) {
                if (!datetimeStr) return '';
                return datetimeStr.replace(' ', 'T').slice(0, 16);
            }

            const deleteSurplusUrlTemplate = "{{ route('seller.surplus-delete', ':surplusId') }}";

            // ============ SHOW MODAL DELETE SUPRLUS ============
            function openDeleteSurplusModal(surplus) {
                const actionUrl = deleteSurplusUrlTemplate.replace(':surplusId', surplus.id);
                document.getElementById('formDeleteSurplus').action = actionUrl;

                document.getElementById('deleteSurplusName').textContent = surplus.productName;
                document.getElementById('deleteSurplusPrice').textContent = formatRupiah(surplus.discountPrice) +
                    ' · stok ' + surplus.remainingQuantity + ' tersisa';
                document.getElementById('deleteSurplusImage').src = surplus.image;

                document.getElementById('deleteSurplusModal').showModal();
            }

            // ============ IMAGE PREVIEW ============
            function initImageUpload(inputId, containerId) {
                const imageInput = document.getElementById(inputId);
                const previewContainer = document.getElementById(containerId);
                let selectedFiles = [];

                function syncFilesToInput() {
                    const dt = new DataTransfer();
                    selectedFiles.forEach(f => dt.items.add(f));
                    imageInput.files = dt.files;
                }

                imageInput.addEventListener('change', function() {
                    const files = Array.from(this.files);
                    const remaining = 8 - selectedFiles.length;
                    const filesToAdd = files.slice(0, remaining);

                    filesToAdd.forEach(file => {
                        if (!file.type.startsWith('image/')) return;
                        if (file.size > 2 * 1024 * 1024) return;

                        const index = selectedFiles.length;
                        selectedFiles.push(file);

                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const div = document.createElement('div');
                            div.className = 'relative w-16 h-16 rounded-lg overflow-hidden flex-shrink-0';
                            div.dataset.index = index;
                            div.innerHTML = `
                    <img src="${e.target.result}" class="w-full h-full object-cover">
                    <button type="button"
                        class="absolute top-0 right-0 bg-error text-white w-5 h-5 rounded-bl-lg flex items-center justify-center text-xs"
                        onclick="removeImageFrom('${containerId}', this, ${index})">✕</button>
                `;
                            previewContainer.appendChild(div);
                        };
                        reader.readAsDataURL(file);
                    });

                    syncFilesToInput();
                });

                // Simpan removeImage per container
                window[`_removeFrom_${containerId}`] = function(btn, index) {
                    selectedFiles.splice(index, 1);
                    btn.parentElement.remove();
                    syncFilesToInput();

                    document.querySelectorAll(`#${containerId} [data-index]`).forEach((div, i) => {
                        div.dataset.index = i;
                        div.querySelector('button').setAttribute('onclick',
                            `removeImageFrom('${containerId}', this, ${i})`);
                    });
                };
            }

            // ============ SHOW MODAL MAKE SURPLUS ============
            const surplusUrlTemplate = "{{ route('seller.surplus-create') }}";

            function openSurplusModal(product) {
                const modal = document.getElementById('surplusSlideover');

                // Set form action ke route surplus
                document.getElementById('surplusForm').action = surplusUrlTemplate;

                // Set preview info
                document.getElementById('surplusName').textContent = product.name;
                document.getElementById('surplusPriceDisplay').textContent = formatRupiah(product.price);

                // Set original price: display + hidden
                document.getElementById('surplusOriginalPriceDisplay').value = formatRupiah(product.price);
                document.getElementById('surplusInitialPrice').value = product.price;

                // Set image
                document.getElementById('surplusImage').src = product.image;

                // Set hidden product_id
                document.getElementById('surplusProductId').value = product.id;

                // Suggest surplus price (50% dari harga asli)
                document.getElementById('surplusCurrentPrice').value = Math.round(product.price * 0.5);

                // Reset quantity & remaining
                document.getElementById('surplusQuantity').value = '';
                document.getElementById('surplusRemaining').value = '';

                modal.showModal();
            }

            // Sync remaining = quantity saat input berubah
            function syncRemaining(value) {
                document.getElementById('surplusRemaining').value = value;
            }

            // helper rupiah
            function formatRupiah(number) {
                return 'Rp ' + new Intl.NumberFormat('id-ID').format(number);
            }

            // Global dispatcher
            window.removeImageFrom = function(containerId, btn, index) {
                window[`_removeFrom_${containerId}`]?.(btn, index);
            };

            document.addEventListener('DOMContentLoaded', function() {
                // Init untuk Add modal
                initImageUpload('imageInput', 'imagePreviewContainer');

                // Init untuk Edit modal
                initImageUpload('imageInputEdit', 'imagePreviewContainerEdit');
            });

            // Close modals with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    document.querySelectorAll('dialog[open]').forEach(d => d.close());
                }
            });

            @if ($errors->any())
                document.addEventListener('DOMContentLoaded', function() {
                    document.getElementById('foodSlideover').showModal();
                });
            @endif
        </script>

        <style>
            /* Tab Styles */
            .tab-btn {
                display: inline-flex;
                align-items: center;
                padding: 0.5rem 1rem;
                border-radius: 0.75rem;
                font-size: 0.875rem;
                font-weight: 500;
                color: #64748b;
                transition: all 0.2s;
                gap: 0.25rem;
            }

            .tab-btn:hover {
                color: #f97316;
                background: #fff7ed;
            }

            .tab-btn.tab-active {
                background: #f97316;
                color: white;
            }

            .tab-btn.tab-active .badge {
                background: rgba(255, 255, 255, 0.2);
                color: white;
                border: none;
            }

            /* Line Clamp */
            .line-clamp-2 {
                display: -webkit-box;
                -webkit-line-clamp: 2;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }

            /* Modal Backdrop */
            dialog::backdrop {
                background: rgba(0, 0, 0, 0.5);
                backdrop-filter: blur(2px);
            }

            dialog.modal {
                padding: 0;
                border: none;
                background: transparent;
            }

            /* Progress Bar */
            progress.progress-warning::-webkit-progress-value {
                background-color: #f97316;
            }

            progress.progress-warning::-moz-progress-bar {
                background-color: #f97316;
            }
        </style>
    @endpush
