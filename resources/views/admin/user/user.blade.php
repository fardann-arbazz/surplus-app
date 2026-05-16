@extends('layouts.admin')

@section('title', 'User Management - Rantangku')

@section('content')
    <div>
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-xl lg:text-2xl font-bold">User Management</h1>
                <p class="text-sm text-base-content/50 mt-1">Manage all registered users and their roles</p>
            </div>
            <button class="btn btn-outline btn-sm gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                Export
            </button>
        </div>

        <!-- Search & Filter Bar -->
        <div class="card bg-base-100 border border-base-200 shadow-sm mb-6">
            <div class="card-body p-4">
                {{-- Form GET — semua filter dikirim sebagai query string --}}
                <form method="GET" action="{{ route('admin.users.index') }}">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <!-- Search -->
                        <div class="relative flex-1">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-base-content/40"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <input type="text" name="search" placeholder="Search by name or email..."
                                value="{{ request('search') }}"
                                class="input input-bordered w-full pl-10 text-sm focus:outline-none focus:border-warning">
                        </div>

                        <!-- Filter by Role -->
                        <select name="role"
                            class="select select-bordered text-sm focus:outline-none focus:border-warning w-full sm:w-40">
                            <option value="all" @selected(request('role', 'all') === 'all')>All Roles</option>
                            <option value="buyer" @selected(request('role') === 'buyer')>Buyer</option>
                            <option value="seller" @selected(request('role') === 'seller')>Seller</option>
                            <option value="admin" @selected(request('role') === 'admin')>Admin</option>
                        </select>

                        <!-- Filter by Status -->
                        <select name="status"
                            class="select select-bordered text-sm focus:outline-none focus:border-warning w-full sm:w-40">
                            <option value="all" @selected(request('status', 'all') === 'all')>All Status</option>
                            <option value="active" @selected(request('status') === 'active')>Active</option>
                            <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
                            <option value="suspended" @selected(request('status') === 'suspended')>Suspended</option>
                        </select>

                        <!-- Submit -->
                        <button type="submit" class="btn btn-warning btn-sm sm:self-stretch">Filter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Users Table -->
        <div class="card bg-base-100 border border-base-200 shadow-sm">
            <div class="card-body p-0">
                <div class="overflow-x-auto">
                    <table class="table table-zebra table-pin-rows">
                        <thead>
                            <tr class="bg-base-200/50">
                                <th class="w-10">#</th>
                                <th>User</th>
                                <th class="hidden md:table-cell">Email</th>
                                <th class="hidden lg:table-cell">Role</th>
                                <th class="hidden lg:table-cell">Registered</th>
                                <th>Status</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($users as $index => $user)
                                @php
                                    $status = $user->status; // dari accessor getStatusAttribute
                                    $roleLabel = $user->role_label; // dari accessor getRoleLabelAttribute

                                    $roleClass = match ($user->role) {
                                        'admin' => 'badge-warning',
                                        'seller' => 'badge-info',
                                        default => 'badge-ghost', // user / buyer
                                    };

                                    $statusClass = match ($status) {
                                        'active' => 'badge-success',
                                        'suspended' => 'badge-error',
                                        default => 'badge-ghost', // inactive
                                    };
                                @endphp
                                <tr class="hover">
                                    {{-- Nomor urut mengikuti pagination --}}
                                    <td class="text-xs text-base-content/50">
                                        {{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}
                                    </td>

                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="min-w-0">
                                                <p class="text-sm font-semibold truncate max-w-37.5">{{ $user->name }}
                                                </p>
                                                <p class="text-xs text-base-content/50 md:hidden">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="hidden md:table-cell">
                                        <p class="text-sm">{{ $user->email }}</p>
                                    </td>

                                    <td class="hidden lg:table-cell">
                                        <span class="badge badge-sm {{ $roleClass }}">{{ $roleLabel }}</span>
                                    </td>

                                    <td class="hidden lg:table-cell">
                                        <p class="text-xs text-base-content/50">
                                            {{ $user->created_at->format('d M Y') }}
                                        </p>
                                    </td>

                                    <td>
                                        <span class="badge badge-sm {{ $statusClass }} gap-1">
                                            @if ($status === 'active')
                                                <span class="w-1.5 h-1.5 bg-white rounded-full"></span>
                                            @endif
                                            {{ ucfirst($status) }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="flex items-center justify-center gap-1">
                                            <!-- View Detail -->
                                            <button
                                                onclick="openDetailModal(
                                                    '{{ e($user->name) }}',
                                                    '{{ e($user->email) }}',
                                                    '{{ $roleLabel }}',
                                                    '{{ ucfirst($status) }}',
                                                    '{{ $user->created_at->format('d M Y') }}'
                                                )"
                                                class="btn btn-ghost btn-xs btn-square" title="View Detail">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>

                                            <!-- More Actions -->
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
                                                        <a onclick="openDetailModal(
                                                                '{{ e($user->name) }}',
                                                                '{{ e($user->email) }}',
                                                                '{{ $roleLabel }}',
                                                                '{{ ucfirst($status) }}',
                                                                '{{ $user->created_at->format('d M Y') }}'
                                                            )"
                                                            class="cursor-pointer">View Details</a>
                                                    </li>

                                                    @if ($status === 'active')
                                                        <li>
                                                            <a onclick="openSuspendModal({{ $user->id }}, '{{ e($user->name) }}')"
                                                                class="text-warning cursor-pointer">Suspend</a>
                                                        </li>
                                                    @elseif ($status === 'suspended')
                                                        <li>
                                                            <a onclick="openActivateModal({{ $user->id }}, '{{ e($user->name) }}')"
                                                                class="text-success cursor-pointer">Activate</a>
                                                        </li>
                                                    @endif

                                                    @unless ($user->isAdmin())
                                                        <div class="divider my-1"></div>
                                                        <li>
                                                            <a onclick="openDeleteModal({{ $user->id }}, '{{ e($user->name) }}')"
                                                                class="text-error cursor-pointer">Delete</a>
                                                        </li>
                                                    @endunless
                                                </ul>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-10 text-base-content/40">
                                        <svg class="w-10 h-10 mx-auto mb-2 opacity-40" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <p class="text-sm">No users found.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Info + Links -->
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 p-4 border-t border-base-200">
                    <p class="text-xs text-base-content/50">
                        Showing {{ $users->firstItem() ?? 0 }}–{{ $users->lastItem() ?? 0 }}
                        of {{ $users->total() }} users
                    </p>
                    {{-- DaisyUI-style pagination menggunakan paginator bawaan Laravel --}}
                    {{ $users->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </div>

    {{-- =========================================================== --}}
    {{-- Hidden Forms untuk aksi POST/PATCH/DELETE (CSRF safe)        --}}
    {{-- =========================================================== --}}

    {{-- Suspend Form --}}
    <form id="suspendForm" method="POST" class="hidden">
        @csrf
        @method('PATCH')
    </form>

    {{-- Activate Form --}}
    <form id="activateForm" method="POST" class="hidden">
        @csrf
        @method('PATCH')
    </form>

    {{-- Delete Form --}}
    <form id="deleteForm" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>

    <!-- ============ USER DETAIL MODAL ============ -->
    <dialog id="detailModal" class="modal">
        <div class="modal-box max-w-md">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold">User Details</h3>
                <button onclick="document.getElementById('detailModal').close()"
                    class="btn btn-ghost btn-sm btn-circle">✕</button>
            </div>

            <div class="space-y-3">
                <div class="bg-base-200 rounded-xl p-3">
                    <p class="text-xs text-base-content/50">Full Name</p>
                    <p class="text-sm font-semibold" id="detailName">-</p>
                </div>
                <div class="bg-base-200 rounded-xl p-3">
                    <p class="text-xs text-base-content/50">Email</p>
                    <p class="text-sm font-semibold" id="detailEmail">-</p>
                </div>
                <div class="bg-base-200 rounded-xl p-3">
                    <p class="text-xs text-base-content/50">Role</p>
                    <p class="text-sm font-semibold" id="detailRole">-</p>
                </div>
                <div class="bg-base-200 rounded-xl p-3">
                    <p class="text-xs text-base-content/50">Status</p>
                    <p class="text-sm font-semibold" id="detailStatus">-</p>
                </div>
                <div class="bg-base-200 rounded-xl p-3">
                    <p class="text-xs text-base-content/50">Registered</p>
                    <p class="text-sm font-semibold" id="detailDate">-</p>
                </div>
            </div>

            <div class="modal-action">
                <button onclick="document.getElementById('detailModal').close()"
                    class="btn btn-ghost btn-sm">Close</button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    <!-- ============ SUSPEND CONFIRMATION ============ -->
    <dialog id="suspendModal" class="modal">
        <div class="modal-box max-w-sm text-center">
            <h3 class="text-lg font-bold">Suspend User?</h3>
            <p class="text-sm text-base-content/60 mb-1">
                You are about to suspend <strong id="suspendUserName"></strong>.
            </p>
            <p class="text-sm text-base-content/60 mb-4">
                The user will not be able to access their account until reactivated.
            </p>
            <div class="flex gap-3 justify-center">
                <button type="button" onclick="document.getElementById('suspendModal').close()"
                    class="btn btn-ghost btn-sm">Cancel</button>
                <button type="button" onclick="submitSuspend()" class="btn btn-warning btn-sm">Suspend</button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    <!-- ============ ACTIVATE CONFIRMATION ============ -->
    <dialog id="activateModal" class="modal">
        <div class="modal-box max-w-sm text-center">
            <h3 class="text-lg font-bold">Activate User?</h3>
            <p class="text-sm text-base-content/60 mb-1">
                You are about to activate <strong id="activateUserName"></strong>.
            </p>
            <p class="text-sm text-base-content/60 mb-4">The user will regain full access to their account.</p>
            <div class="flex gap-3 justify-center">
                <button type="button" onclick="document.getElementById('activateModal').close()"
                    class="btn btn-ghost btn-sm">Cancel</button>
                <button type="button" onclick="submitActivate()" class="btn btn-success btn-sm">Activate</button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    <!-- ============ DELETE USER MODAL ============ -->
    <dialog id="deleteUserModal" class="modal">
        <div class="modal-box max-w-sm text-center">
            <div class="w-14 h-14 bg-error/10 rounded-full flex items-center justify-center mx-auto mb-3">
                <svg class="w-7 h-7 text-error" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                </svg>
            </div>
            <h3 class="text-lg font-bold">Delete User?</h3>
            <p class="text-sm text-base-content/60 mb-1">
                You are about to delete <strong id="deleteUserName"></strong>.
            </p>
            <p class="text-sm text-base-content/60 mb-4">
                This action cannot be undone. All associated data will be permanently removed.
            </p>
            <div class="flex gap-3 justify-center">
                <button type="button" onclick="document.getElementById('deleteUserModal').close()"
                    class="btn btn-ghost btn-sm">Cancel</button>
                <button type="button" onclick="submitDelete()" class="btn btn-error btn-sm gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete
                </button>
            </div>
        </div>
        <form method="dialog" class="modal-backdrop"><button>close</button></form>
    </dialog>

    <!-- Toast Container — diisi dari session flash -->
    <div id="toastContainer" class="fixed top-20 right-4 z-50 space-y-2"></div>

@endsection

@push('scripts')
    <script>
        // ============================================================
        // DETAIL MODAL
        // ============================================================
        function openDetailModal(name, email, role, status, date) {
            document.getElementById('detailName').textContent = name;
            document.getElementById('detailEmail').textContent = email;
            document.getElementById('detailRole').textContent = role;
            document.getElementById('detailStatus').textContent = status;
            document.getElementById('detailDate').textContent = date;
            document.getElementById('detailModal').showModal();
        }

        // ============================================================
        // SUSPEND MODAL
        // ============================================================
        let _suspendUserId = null;

        function openSuspendModal(userId, userName) {
            _suspendUserId = userId;
            document.getElementById('suspendUserName').textContent = userName;
            document.getElementById('suspendModal').showModal();
        }

        function submitSuspend() {
            if (!_suspendUserId) return;
            const form = document.getElementById('suspendForm');
            form.action = `/admin/users/${_suspendUserId}/suspend`;
            document.getElementById('suspendModal').close();
            form.submit();
        }

        // ============================================================
        // ACTIVATE MODAL
        // ============================================================
        let _activateUserId = null;

        function openActivateModal(userId, userName) {
            _activateUserId = userId;
            document.getElementById('activateUserName').textContent = userName;
            document.getElementById('activateModal').showModal();
        }

        function submitActivate() {
            if (!_activateUserId) return;
            const form = document.getElementById('activateForm');
            form.action = `/admin/users/${_activateUserId}/activate`;
            document.getElementById('activateModal').close();
            form.submit();
        }

        // ============================================================
        // DELETE MODAL
        // ============================================================
        let _deleteUserId = null;

        function openDeleteModal(userId, userName) {
            _deleteUserId = userId;
            document.getElementById('deleteUserName').textContent = userName;
            document.getElementById('deleteUserModal').showModal();
        }

        function submitDelete() {
            if (!_deleteUserId) return;
            const form = document.getElementById('deleteForm');
            form.action = `/admin/users/${_deleteUserId}`;
            document.getElementById('deleteUserModal').close();
            form.submit();
        }

        // ============================================================
        // TOAST — dari session flash Laravel
        // ============================================================
        @if (session('toast'))
            showToast(
                '{{ session('toast.message') }}',
                '{{ session('toast.type', 'success') }}'
            );
        @endif

        function showToast(message, type = 'success') {
            const container = document.getElementById('toastContainer');
            const bgMap = {
                success: 'alert-success',
                error: 'alert-error',
                warning: 'alert-warning',
            };
            const iconMap = {
                success: '✅',
                error: '❌',
                warning: '⚠️',
            };

            const toast = document.createElement('div');
            toast.className = `alert shadow-lg max-w-sm ${bgMap[type] ?? 'alert-info'} animate-slide-in`;
            toast.innerHTML = `<span>${iconMap[type] ?? 'ℹ️'} ${message}</span>`;
            container.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3500);
        }

        // Close semua modal saat tekan Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('dialog[open]').forEach(d => d.close());
            }
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
    </style>
@endpush
