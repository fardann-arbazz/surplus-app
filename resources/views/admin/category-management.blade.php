@extends('layouts.admin')

@section('title', 'Category Management - Rantangku')

@section('content')

    <div>

        {{-- HEADER --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold">
                    Category Management
                </h1>

                <p class="text-sm text-base-content/50 mt-1">
                    Manage food categories for all sellers
                </p>
            </div>

            <button onclick="openCreateModal()" class="btn btn-warning btn-sm gap-2">

                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>

                Add Category
            </button>
        </div>

        {{-- ALERT --}}
        @if (session('success'))
            <div class="alert alert-success mb-4">
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- SEARCH --}}
        <div class="card bg-base-100 border border-base-200 shadow-sm mb-6">
            <div class="card-body p-4">

                <form method="GET">

                    <div class="relative">

                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-base-content/40" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">

                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>

                        <input type="text" name="search" value="{{ $search }}" placeholder="Search category..."
                            class="input input-bordered w-full pl-10 focus:input-warning">

                    </div>

                </form>

            </div>
        </div>

        {{-- TABLE --}}
        <div class="card bg-base-100 border border-base-200 shadow-sm">

            <div class="overflow-x-auto">

                <table class="table">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Category</th>
                            <th>Products</th>
                            <th>Created</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($category as $cat)
                            <tr>

                                <td>
                                    {{ $category->firstItem() + $loop->index }}
                                </td>

                                <td>
                                    <div class="font-semibold">
                                        {{ $cat->name }}
                                    </div>
                                </td>

                                <td>
                                    <span class="badge badge-outline">
                                        {{ $cat->product_count }} products
                                    </span>
                                </td>

                                <td class="text-sm text-base-content/60">
                                    {{ $cat->created_at->format('d M Y') }}
                                </td>

                                <td>

                                    <div class="flex items-center justify-center gap-2">

                                        <button
                                            onclick="openEditModal(
                                            '{{ $cat->id }}',
                                            '{{ $cat->name }}'
                                        )"
                                            class="btn btn-ghost btn-xs">

                                            Edit
                                        </button>

                                        <button
                                            onclick="openDeleteModal(
                                            '{{ $cat->id }}',
                                            '{{ $cat->name }}'
                                        )"
                                            class="btn btn-ghost btn-xs text-error">

                                            Delete
                                        </button>

                                    </div>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="5" class="text-center py-16">

                                    <div class="flex flex-col items-center">

                                        <h3 class="font-bold text-lg">
                                            No Categories
                                        </h3>

                                        <p class="text-sm text-base-content/50">
                                            Category data not found
                                        </p>

                                    </div>

                                </td>
                            </tr>
                        @endforelse

                    </tbody>

                </table>

            </div>

            {{-- PAGINATION --}}
            @if ($category->hasPages())
                <div class="p-4 border-t border-base-200">
                    {{ $category->links() }}
                </div>
            @endif

        </div>

    </div>

    {{-- CREATE / EDIT MODAL --}}
    <dialog id="categoryModal" class="modal">

        <div class="modal-box">

            <h3 class="font-bold text-lg mb-4" id="modalTitle">
                Add Category
            </h3>

            <form id="categoryForm" method="POST">

                @csrf

                <div id="methodContainer"></div>

                <div class="form-control flex flex-col gap-2">

                    <label class="label">
                        <span class="label-text">
                            Category Name
                        </span>
                    </label>

                    <input type="text" name="name" id="categoryName"
                        class="input input-bordered focus:input-warning w-full" required>

                </div>

                <div class="modal-action">

                    <button type="button" onclick="categoryModal.close()" class="btn">

                        Cancel
                    </button>

                    <button type="submit" class="btn btn-warning">

                        Save
                    </button>

                </div>

            </form>

        </div>

    </dialog>

    {{-- DELETE MODAL --}}
    <dialog id="deleteModal" class="modal">

        <div class="modal-box">

            <h3 class="font-bold text-lg mb-2">
                Delete Category
            </h3>

            <p class="text-sm text-base-content/60 mb-4">
                Are you sure want to delete
                <strong id="deleteCategoryName"></strong>?
            </p>

            <form id="deleteForm" method="POST">

                @csrf
                @method('DELETE')

                <div class="modal-action">

                    <button type="button" onclick="deleteModal.close()" class="btn">

                        Cancel
                    </button>

                    <button type="submit" class="btn btn-error">

                        Delete
                    </button>

                </div>

            </form>

        </div>

    </dialog>

@endsection

@push('scripts')
    <script>
        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Add Category';

            document.getElementById('categoryForm').action =
                "{{ route('admin.category.store') }}";

            document.getElementById('methodContainer').innerHTML = '';

            document.getElementById('categoryName').value = '';

            categoryModal.showModal();
        }

        function openEditModal(id, name) {
            document.getElementById('modalTitle').textContent = 'Edit Category';

            document.getElementById('categoryForm').action =
                `/admin/category/${id}`;

            document.getElementById('methodContainer').innerHTML =
                '@method('PUT')';

            document.getElementById('categoryName').value = name;

            categoryModal.showModal();
        }

        function openDeleteModal(id, name) {
            document.getElementById('deleteCategoryName').textContent = name;

            document.getElementById('deleteForm').action =
                `/admin/category/${id}`;

            deleteModal.showModal();
        }
    </script>
@endpush
