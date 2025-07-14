@extends('layouts.app')

@section('content')
<div class="container mt-5">

    {{-- Include the menu tabs partial --}}
    @include('partials.menu-tabs', ['menu' => $menu])


    {{-- Branches Information, Edit Menu Button, and Single Sync Button --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="mb-0">
            <strong>Branches:</strong>
            @foreach($menu->branches as $branch)
            <span class="badge bg-dark me-1">{{ $branch->name['en'] ?? $branch->name }}</span>
            @endforeach
        </p>
        <div class="d-flex align-items-center">
            {{-- Single Sync Menu Button --}}
            <button type="button" class="btn btn-dark rounded-pill me-2" data-bs-toggle="modal" data-bs-target="#syncMenuModal">
                Sync Menu
            </button>

            {{-- Edit Menu Button --}}
            <a href="{{ route('menus.edit', $menu->id) }}" class="btn btn-dark rounded-circle p-0"
                style="width: 30px; height: 30px; display: flex; justify-content: center; align-items: center;"
                title="Edit Menu">
                <i class="bi bi-pencil-fill fs-6"></i>
            </a>
        </div>
    </div>


    {{-- Tab Contents (your existing content for categories, items, etc.) --}}
    <div class="tab-content mt-3" id="menuTabsContent">
        @foreach($categories as $category)
        <div class="mt-1 ms-2 me-2 mb-5">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3"> {{ $category->name ['en'] ?? ''}} </h5>
                    <div class="text-center">
                        <table class="table custom-row-spacing-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Modifier Group</th>
                                    <th>Price</th>
                                    <th>Sync Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider color:grey">
                                @forelse($category->items as $item)
                                <tr onclick="handleRowClick(event, '{{ route('menus.items.edit', ['menu' => $menu->id, 'item' => $item->id]) }}')"
                                    style="cursor: pointer;">

                                    <td>
                                        {{ $item->name['en'] ?? '' }}
                                    </td>

                                    <td>
                                        {{ $item->modifierGroups->count() }}
                                    </td>

                                    <td>
                                        {{ $item->price }}
                                    </td>

                                    <td>
                                        <div>
                                            <span class="badge px-3 py-2 rounded-pill text-black" style="
                                            background-color: {{
                                            $item['status'] === 'Active' ? '#E0F7F0' :
                                            ($item['status'] === 'Inactive' ? '#808080' : '#000000')
                                            }}; 
                                            font-weight: 450;
                                            font-size: 1rem;
                                            padding: 0.75rem 1.5rem;
                                            border-radius: 10px;">
                                                {{ ucfirst( $item['status'] ) }}
                                            </span>
                                        </div>
                                    </td>


                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <a href="{{ route('menus.items.edit', ['menu' => $menu->id, 'item' => $item->id]) }}"
                                                class="btn btn-dark rounded-circle p-0"
                                                style="width: 30px; height: 30px; display: flex; justify-content: center; align-items: center;">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>


                                        <script>
                                            function handleRowClick(event, url) {
                                            // prevent navigation if the click was on a button or inside a form
                                            if (event.target.closest('form') || event.target.closest('a') || event.target.tagName === 'BUTTON') {
                                            event.stopPropagation();
                                            return;
                                            }
                                            window.location = url;
                                            }
                                        </script>
                                    </td>
                                </tr>

                                @empty
                                <tr>
                                    <td colspan="5" class="text-center">No items found for this category.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

    </div>
</div>

<!-- Sync Menu Modal -->
<div class="modal fade" id="syncMenuModal" tabindex="-1" aria-labelledby="syncMenuModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="syncMenuModalLabel">Sync Menu to Delivery Apps</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('menus.sync', ['menu' => $menu->id]) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <p>Select the branches you want to sync this menu to:</p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="selectAllBranches">
                        <label class="form-check-label" for="selectAllBranches">
                            Select All
                        </label>
                    </div>
                    <hr>
                    @forelse($menu->branches as $branch)
                        <div class="form-check">
                            <input class="form-check-input branch-checkbox" type="checkbox" name="branch_ids[]" value="{{ $branch->id }}" id="branchCheckbox{{ $branch->id }}">
                            <label class="form-check-label" for="branchCheckbox{{ $branch->id }}">
                                {{ $branch->name['en'] ?? $branch->name }}
                            </label>
                        </div>
                    @empty
                        <p class="text-muted">No branches found for this menu.</p>
                    @endforelse
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark">Sync Selected Branches</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCheckbox = document.getElementById('selectAllBranches');
        const branchCheckboxes = document.querySelectorAll('.branch-checkbox');

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function () {
                branchCheckboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });
        }
    });

    // Ensure handleRowClick is globally accessible if it's not already
    // This script block should ideally be in a separate JS file or pushed to a stack in app.blade.php
    function handleRowClick(event, url) {
        // prevent navigation if the click was on a button or inside a form
        if (event.target.closest('form') || event.target.closest('a') || event.target.tagName === 'BUTTON') {
            event.stopPropagation();
            return;
        }
        window.location = url;
    }
</script>
@endpush
@endsection
