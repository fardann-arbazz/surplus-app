@extends('layouts.admin')

@section('title', 'Seller Management - Rantangku')

@push('styles')
    <style>
        .dropdown .dropdown-content {
            position: fixed !important;
        }
    </style>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
@endpush

@section('content')
    <div x-data="sellerManagement">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl lg:text-2xl font-bold">Seller Management</h1>
                <p class="text-sm text-base-content/50 mt-1">Approve and manage seller accounts</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" class="btn btn-outline btn-sm gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    Export
                </a>
            </div>
        </div>

        <!-- Search & Filter Bar -->
        <form method="GET" action="{{ route('admin.seller-management') }}"
            class="card bg-base-100 border border-base-200 shadow-sm mb-6" id="filterForm">
            <div class="card-body p-4">
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- Search -->
                    <div class="relative flex-1">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-base-content/40" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search seller name, owner, or email..."
                            class="input input-bordered w-full pl-10 text-sm focus:outline-none focus:border-warning">
                    </div>

                    <!-- Filter by Status -->
                    <select name="status"
                        class="select select-bordered text-sm focus:outline-none focus:border-warning w-full sm:w-44"
                        onchange="this.form.submit()">
                        <option value="all" {{ request('status') == 'all' ? 'selected' : '' }}>All Sellers</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>

                    <!-- Sort -->
                    <select name="sort"
                        class="select select-bordered text-sm focus:outline-none focus:border-warning w-full sm:w-44"
                        onchange="this.form.submit()">
                        <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                        <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                        <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                        <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                    </select>

                    <!-- Submit Button (hidden, form submits on select change) -->
                    <noscript>
                        <button type="submit" class="btn btn-warning btn-sm">Apply</button>
                    </noscript>
                </div>
            </div>
        </form>

        <!-- Sellers Table -->
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body p-0">
                <div class="overflow-x-auto overflow-y-visible">
                    <table class="table table-zebra table-pin-rows">
                        <thead>
                            <tr class="bg-base-200/50">
                                <th class="w-10">#</th>
                                <th>Restaurant</th>
                                <th class="hidden md:table-cell">Owner</th>
                                <th class="hidden lg:table-cell">Contact</th>
                                <th class="hidden xl:table-cell">Location</th>
                                <th class="hidden md:table-cell">Registered</th>
                                <th class="text-center">Active</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="-z-10">
                            @forelse($sellers as $index => $seller)
                                <tr class="hover" id="seller-row-{{ $seller->id }}">
                                    <td class="text-xs text-base-content/50">
                                        {{ $sellers->firstItem() + $index }}
                                    </td>
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="avatar">
                                                <div class="w-10 h-10 rounded-xl">
                                                    <img src="{{ $seller->image_url }}" alt="{{ $seller->name }}"
                                                        class="object-cover">
                                                </div>
                                            </div>
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold truncate max-w-45">{{ $seller->name }}
                                                </p>
                                                <p class="text-xs text-base-content/50 truncate max-w-45">
                                                    {{ $seller->user->name ?? 'Unknown Owner' }}
                                                </p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="hidden md:table-cell">
                                        <p class="text-sm">{{ $seller->user->name ?? '-' }}</p>
                                        <p class="text-xs text-base-content/50">{{ $seller->user->email ?? '-' }}</p>
                                    </td>
                                    <td class="hidden lg:table-cell">
                                        <div>
                                            <p class="text-xs">{{ $seller->user->email ?? '-' }}</p>
                                            <p class="text-xs text-base-content/50">{{ $seller->user->phone ?? '-' }}</p>
                                        </div>
                                    </td>
                                    <td class="hidden xl:table-cell">
                                        <p class="text-xs max-w-37.5 truncate" title="{{ $seller->address }}">
                                            {{ $seller->address ?? '-' }}
                                        </p>
                                    </td>
                                    <td class="hidden md:table-cell">
                                        <p class="text-xs text-base-content/50">{{ $seller->formatted_date }}</p>
                                    </td>
                                    <td class="text-center">
                                        <label class="inline-flex items-center cursor-pointer">
                                            <input type="checkbox" class="toggle toggle-warning toggle-sm seller-toggle"
                                                data-seller-id="{{ $seller->id }}"
                                                data-seller-name="{{ $seller->name }}"
                                                {{ $seller->is_active ? 'checked' : '' }}
                                                onchange="toggleSellerStatus(this)">
                                        </label>
                                        <span
                                            class="ml-2 text-xs {{ $seller->is_active ? 'text-success font-medium' : 'text-base-content/40' }}">
                                            {{ $seller->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="flex items-center justify-center gap-1">
                                            <!-- View Detail -->
                                            <button onclick="viewSellerDetail({{ $seller->id }})"
                                                class="btn btn-ghost btn-xs btn-square" title="View Detail">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>

                                            <!-- More Actions Dropdown -->
                                            <div class="dropdown dropdown-end ">
                                                <label tabindex="0" class="btn btn-ghost btn-xs btn-square">
                                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                                        <circle cx="12" cy="5" r="2" />
                                                        <circle cx="12" cy="12" r="2" />
                                                        <circle cx="12" cy="19" r="2" />
                                                    </svg>
                                                </label>
                                                <ul tabindex="-1"
                                                    class="dropdown-content z-9999 menu p-2 shadow-lg bg-base-100 rounded-box w-48 border border-base-200">
                                                    <li><a onclick="viewSellerDetail({{ $seller->id }})"
                                                            class="cursor-pointer">
                                                            View Details
                                                        </a></li>
                                                    <li><a href="#" class="cursor-pointer">
                                                            Edit
                                                        </a></li>
                                                    <div class="divider my-1"></div>
                                                    <li><a onclick="deleteSeller({{ $seller->id }}, '{{ $seller->name }}')"
                                                            class="text-error cursor-pointer">
                                                            Delete
                                                        </a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-16">
                                        <div class="flex flex-col items-center gap-3">
                                            <span class="text-5xl">🏪</span>
                                            <p class="text-base-content/50 font-medium">No sellers found</p>
                                            <p class="text-xs text-base-content/30">Try adjusting your search or filters
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($sellers->hasPages())
                    <div class="flex items-center justify-between p-4 border-t border-base-200">
                        <p class="text-xs text-base-content/50">
                            Showing {{ $sellers->firstItem() }} - {{ $sellers->lastItem() }} of {{ $sellers->total() }}
                            sellers
                        </p>
                        <div class="join">
                            {{ $sellers->appends(request()->query())->links('pagination::tailwind-join') }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- ============ SELLER DETAIL MODAL ============ -->
    <dialog id="sellerDetailModal" class="modal">
        <div class="modal-box max-w-2xl p-0 overflow-hidden" id="sellerDetailContent">
            <!-- Content loaded via AJAX -->
            <div class="flex items-center justify-center py-20">
                <span class="loading loading-spinner loading-lg text-warning"></span>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <!-- ============ DELETE CONFIRMATION MODAL ============ -->
    <dialog id="deleteConfirmModal" class="modal">
        <div class="modal-box max-w-md">
            <div class="text-center mb-4">
                <span class="text-5xl">⚠️</span>
            </div>
            <h3 class="text-lg font-bold text-center mb-2">Delete Seller?</h3>
            <p class="text-sm text-base-content/60 text-center mb-4" id="deleteMessage"></p>
            <div class="alert alert-warning alert-soft mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
                <span class="text-sm">This action cannot be undone.</span>
            </div>
            <div class="modal-action">
                <button onclick="closeModal('deleteConfirmModal')" class="btn btn-ghost">Cancel</button>
                <button onclick="confirmDelete()" class="btn btn-error gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete Permanently
                </button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop">
            <button>close</button>
        </form>
    </dialog>

    <!-- ============ TOAST CONTAINER ============ -->
    <div id="toastContainer" class="fixed top-20 right-4 z-50 space-y-2"></div>

@endsection

@push('scripts')
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>

    <script>
        // ============ GLOBAL STATE ============
        let deleteTargetId = null;
        let deleteTargetName = null;

        // ============ TOGGLE SELLER STATUS (AJAX) ============
        async function toggleSellerStatus(checkbox) {
            const sellerId = checkbox.dataset.sellerId;
            const sellerName = checkbox.dataset.sellerName;
            const isActive = checkbox.checked;
            const statusText = checkbox.closest('label').nextElementSibling;
            const row = document.getElementById('seller-row-' + sellerId);

            // Optimistic update
            if (isActive) {
                statusText.textContent = 'Active';
                statusText.className = 'ml-2 text-xs text-success font-medium';
                if (row) row.style.opacity = '1';
            } else {
                statusText.textContent = 'Inactive';
                statusText.className = 'ml-2 text-xs text-base-content/40';
                if (row) row.style.opacity = '0.7';
            }

            try {
                const response = await fetch('{{ route('admin.update-seller-status') }}', {
                    method: 'PATCH',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        seller_id: sellerId,
                        is_active: isActive
                    })
                });

                const data = await response.json();

                if (data.success) {
                    showToast(
                        isActive ?
                        `"${sellerName}" saat ini sudah aktif dan dapat menjual produk.` :
                        `"${sellerName}" telah dinonaktifkan.`,
                        isActive ? 'success' : 'warning'
                    );

                    // Update dropdown menu items in the row
                    updateActionMenu(sellerId, isActive);
                } else {
                    // Revert on failure
                    checkbox.checked = !isActive;
                    revertStatusDisplay(statusText, !isActive);
                    showToast(data.message || 'Failed to update status. Please try again.', 'error');
                }
            } catch (error) {
                console.error('Toggle error:', error);
                // Revert on error
                checkbox.checked = !isActive;
                revertStatusDisplay(statusText, !isActive);
                showToast('Network error. Please try again.', 'error');
            }
        }

        function revertStatusDisplay(element, isActive) {
            if (isActive) {
                element.textContent = 'Active';
                element.className = 'ml-2 text-xs text-success font-medium';
            } else {
                element.textContent = 'Inactive';
                element.className = 'ml-2 text-xs text-base-content/40';
            }
        }

        function updateActionMenu(sellerId, isActive) {
            // Find the dropdown menu for this row
            const row = document.getElementById('seller-row-' + sellerId);
            if (!row) return;

            const dropdownMenu = row.querySelector('.dropdown-content ul');
            if (!dropdownMenu) return;

            // Rebuild menu items
            let menuHTML = `
            <li><a onclick="viewSellerDetail(${sellerId})" class="cursor-pointer">View Details</a></li>
            <li><a href="#" class="cursor-pointer">Edit</a></li>
        `;

            if (isActive) {
                menuHTML += `
                <li><a onclick="deactivateSeller(${sellerId})" class="text-warning cursor-pointer">Deactivate</a></li>
            `;
            } else {
                menuHTML += `
                <li><a onclick="activateSeller(${sellerId})" class="text-success cursor-pointer">Activate</a></li>
            `;
            }

            menuHTML += `
            <div class="divider my-1"></div>
            <li><a onclick="deleteSeller(${sellerId})" class="text-error cursor-pointer">Delete</a></li>
        `;

            // Find the parent ul and update
            const menuUl = row.querySelector('.dropdown-content .menu');
            if (menuUl) {
                menuUl.innerHTML = menuHTML;
            }
        }

        // ============ ACTIVATE / DEACTIVATE VIA DROPDOWN ============
        async function activateSeller(id, name) {
            const checkbox = document.querySelector(`.seller-toggle[data-seller-id="${id}"]`);
            if (checkbox && !checkbox.checked) {
                checkbox.checked = true;
                await toggleSellerStatus(checkbox);
            }
        }

        async function deactivateSeller(id, name) {
            const checkbox = document.querySelector(`.seller-toggle[data-seller-id="${id}"]`);
            if (checkbox && checkbox.checked) {
                checkbox.checked = false;
                await toggleSellerStatus(checkbox);
            }
        }

        // ============ VIEW SELLER DETAIL (AJAX) ============
        async function viewSellerDetail(id) {
            const modal = document.getElementById('sellerDetailModal');
            const content = document.getElementById('sellerDetailContent');

            // Show loading
            content.innerHTML = `
            <div class="flex items-center justify-center py-20">
                <span class="loading loading-spinner loading-lg text-warning"></span>
            </div>
        `;
            modal.showModal();

            try {
                const response = await fetch(`/admin/sellers/${id}/detail`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    content.innerHTML = buildDetailHTML(data.seller);

                    // Initialize map if cordinate exits
                    if (data.seller.latitude && data.seller.longitude) {
                        setTimeout(() => {
                            const mapContainer = document.getElementById('sellerMap');
                            if (mapContainer) {
                                const map = L.map('sellerMap').setView(
                                    [parseFloat(data.seller.latitude), parseFloat(data.seller.longitude)],
                                    15
                                );

                                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                                }).addTo(map);

                                const marker = L.marker([parseFloat(data.seller.latitude), parseFloat(data
                                        .seller.longitude)])
                                    .addTo(map)
                                    .bindPopup(`<b>${data.seller.name}</b><br>${data.seller.address || ''}`)
                                    .openPopup();
                            }
                        }, 100);
                    }

                    // Re-bind toggle in detail modal
                    const detailToggle = content.querySelector('#detailToggle');
                    if (detailToggle) {
                        detailToggle.addEventListener('change', async function() {
                            const sellerId = this.dataset.sellerId;
                            const mainToggle = document.querySelector(
                                `.seller-toggle[data-seller-id="${sellerId}"]`);
                            if (mainToggle) {
                                mainToggle.checked = this.checked;
                                await toggleSellerStatus(mainToggle);
                                // Update detail badge
                                updateDetailBadge(this.checked);
                            }
                        });
                    }
                } else {
                    content.innerHTML = `
                    <div class="text-center py-12">
                        <span class="text-4xl">😕</span>
                        <p class="text-base-content/50 mt-2">Failed to load seller details.</p>
                    </div>
                `;
                }
            } catch (error) {
                console.error('Detail error:', error);
                content.innerHTML = `
                <div class="text-center py-12">
                    <span class="text-4xl">😕</span>
                    <p class="text-base-content/50 mt-2">Network error. Please try again.</p>
                </div>
            `;
            }
        }

        function buildDetailHTML(seller) {
            const locationHTML = seller.latitude && seller.longitude ?
                `<div>
                    <p class="text-xs text-base-content/50 mb-2">📍 Location</p>
                    <div id="sellerMap" class="rounded-xl overflow-hidden border border-base-200" style="height: 280px;"></div>
                </div>
                ` :
                `
                <div class="bg-base-200 rounded-xl p-6 text-center">
                    <span class="text-3xl block mb-2">📍</span>
                    <p class="text-sm text-base-content/50">No location data available</p>
                </div>
                `;

            const sellerIsActive = seller.is_active ? `
            <button onclick="deactivateFromDetail(${seller.id}, '${seller.name}')" class="btn btn-warning">Deactivate</button>
            ` : `
            <button onclick="activateFromDetail(${seller.id}, '${seller.name}')" class="btn btn-success">Activate</button>;
                                                                            `
            return `
            <div class="bg-gradient-to-r from-slate-800 to-slate-700 p-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="avatar">
                            <div class="w-12 h-12 rounded-xl">
                                <img src="${seller.image_url}" alt="${seller.name}" class="object-cover">
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold">${seller.name}</h3>
                            <p class="text-slate-300 text-sm">${seller.user?.name || 'Unknown Owner'}</p>
                        </div>
                    </div>
                    <button onclick="closeModal('sellerDetailModal')" class="btn btn-ghost btn-sm btn-circle text-white">✕</button>
                </div>
            </div>

            <div class="p-6 space-y-6">
                <!-- Status Toggle -->
                <div class="flex items-center justify-between bg-base-200 rounded-xl p-4">
                    <div>
                        <p class="text-sm font-semibold">Account Status</p>
                        <p class="text-xs text-base-content/50" id="detailStatusLabel">
                            ${seller.is_active ? 'Seller is active and can sell products' : 'Seller is inactive. Toggle to activate.'}
                        </p>
                    </div>
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" 
                            id="detailToggle"
                            class="toggle toggle-warning"
                            data-seller-id="${seller.id}"
                            ${seller.is_active ? 'checked' : ''}>
                    </label>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="bg-base-200 rounded-xl p-4">
                        <p class="text-xs text-base-content/50 mb-1">Owner Name</p>
                        <p class="text-sm font-semibold">${seller.user?.name || '-'}</p>
                    </div>
                    <div class="bg-base-200 rounded-xl p-4">
                        <p class="text-xs text-base-content/50 mb-1">Email</p>
                        <p class="text-sm font-semibold">${seller.user?.email || '-'}</p>
                    </div>
                    <div class="bg-base-200 rounded-xl p-4">
                        <p class="text-xs text-base-content/50 mb-1">Phone</p>
                        <p class="text-sm font-semibold">${seller.user?.phone || '-'}</p>
                    </div>
                    <div class="bg-base-200 rounded-xl p-4">
                        <p class="text-xs text-base-content/50 mb-1">Registered</p>
                        <p class="text-sm font-semibold">${seller.formatted_date || '-'}</p>
                    </div>
                    <div class="bg-base-200 rounded-xl p-4 sm:col-span-2">
                        <p class="text-xs text-base-content/50 mb-1">Full Address</p>
                        <p class="text-sm font-semibold">${seller.address || '-'}</p>
                    </div>
                    <div class="bg-base-200 rounded-xl p-4 sm:col-span-2">
                        <p class="text-xs text-base-content/50 mb-2">Description</p>
                        <p class="text-sm">${seller.description || 'No description provided.'}</p>
                    </div>
                </div>

                <!-- Location Map -->
                ${locationHTML}
            </div>

            <div class="p-6 pt-0 flex gap-3 justify-end">
                <button onclick="closeModal('sellerDetailModal')" class="btn btn-ghost">Close</button>
                ${sellerIsActive}
            </div>
        `;
        }

        function updateDetailBadge(isActive) {
            const label = document.getElementById('detailStatusLabel');
            if (label) {
                label.textContent = isActive ?
                    'Seller is active and can sell products' :
                    'Seller is inactive. Toggle to activate.';
            }
        }

        async function activateFromDetail(id, name) {
            await activateSeller(id, name);
            // Refresh detail modal
            viewSellerDetail(id);
        }

        async function deactivateFromDetail(id, name) {
            await deactivateSeller(id, name);
            // Refresh detail modal
            viewSellerDetail(id);
        }

        // ============ DELETE SELLER ============
        function deleteSeller(id, name) {
            deleteTargetId = id;
            deleteTargetName = name;
            document.getElementById('deleteMessage').textContent =
                `Are you sure you want to permanently delete "${name}"? All associated data (menus, orders, etc.) will be removed.`;
            document.getElementById('deleteConfirmModal').showModal();
        }

        async function confirmDelete() {
            if (!deleteTargetId) return;

            const id = deleteTargetId;
            const name = deleteTargetName;

            try {
                const response = await fetch(`/admin/sellers/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Remove row from table
                    const row = document.getElementById('seller-row-' + id);
                    if (row) {
                        row.style.opacity = '0';
                        row.style.transform = 'scale(0.95)';
                        row.style.transition = 'all 0.3s ease';
                        setTimeout(() => row.remove(), 300);
                    }
                    showToast(`"${name}" deleted permanently.`, 'error');
                } else {
                    showToast(data.message || 'Failed to delete seller.', 'error');
                }
            } catch (error) {
                console.error('Delete error:', error);
                showToast('Network error. Please try again.', 'error');
            } finally {
                closeModal('deleteConfirmModal');
                deleteTargetId = null;
                deleteTargetName = null;
            }
        }

        // ============ UI HELPERS ============
        function closeModal(modalId) {
            document.getElementById(modalId).close();
        }

        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');

            const bgMap = {
                success: 'alert-success',
                error: 'alert-error',
                warning: 'alert-warning',
                info: 'alert-info'
            };

            toast.className = `alert shadow-lg max-w-sm ${bgMap[type] || 'alert-info'} animate-slide-in`;
            toast.innerHTML = `<span> ${message}</span>`;
            container.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }

        // ============ INITIALIZATION ============
        document.addEventListener('DOMContentLoaded', () => {
            // Close dropdowns when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.dropdown')) {
                    // DaisyUI handles this natively
                }
            });

            // Keyboard shortcut: Escape to close modals
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    document.querySelectorAll('dialog[open]').forEach(dialog => {
                        dialog.close();
                    });
                }
            });
        });
    </script>

    <style>
        .animate-slide-in {
            animation: slideInRight 0.3s ease-out;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        dialog::backdrop {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(2px);
        }

        dialog.modal {
            padding: 0;
            border: none;
            background: transparent;
        }

        dialog.modal .modal-box {
            max-height: 90vh;
            overflow-y: auto;
        }

        /* Smooth row removal */
        tr {
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        /* Toggle switch customization */
        .toggle:checked {
            background-color: #f97316;
            border-color: #f97316;
        }
    </style>
@endpush
