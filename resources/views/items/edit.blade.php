@extends('layouts.app')

@section('content')
<div class="container mt-5">
    {{-- Display Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Display Validation Errors --}}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Edit Item: {{ $item->name['en'] ?? $item->name ?? 'N/A' }}</h4>
        <form action="{{ route('menus.items.destroy', ['menu' => $menu->id, 'item' => $item->id]) }}" method="POST"
            onsubmit="return confirm('Are you sure you want to delete this item? This action cannot be undone.')">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-danger">🗑️ Delete</button>
        </form>
    </div>

    <form method="POST" action="{{ route('menus.items.update', ['menu' => $menu->id, 'item' => $item->id]) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Item Name (English)</label>
            {{-- FIX: Use name[en] for multi-language JSON field --}}
            <input type="text" name="name[en]" class="form-control" value="{{ old('name.en', $item->name['en'] ?? '') }}" required>
            @error('name.en') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Item Name (Arabic)</label>
            {{-- FIX: Use name[ar] for multi-language JSON field --}}
            <input type="text" name="name[ar]" class="form-control" value="{{ old('name.ar', $item->name['ar'] ?? '') }}" required>
            @error('name.ar') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $item->price ?? '') }}" required>
            @error('price') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            {{-- FIX: Revert to single description field as it's not multi-language --}}
            <textarea name="description" class="form-control" rows="3">{{ old('description', $item->description ?? '') }}</textarea>
            @error('description') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        {{-- Categories Checkbox List --}}
        <div class="mb-3">
            <label class="form-label d-block">Assign to Categories</label>
            <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                @php
                    // Get IDs of categories currently assigned to the item
                    $itemCategoryIds = old('category_ids', $item->categories->pluck('id')->toArray());
                @endphp
                @forelse($categories as $category)
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" name="category_ids[]" value="{{ $category->id }}" id="category-{{ $category->id }}"
                            {{ in_array($category->id, $itemCategoryIds) ? 'checked' : '' }}>
                        <label class="form-check-label" for="category-{{ $category->id }}">
                            {{ $category->name['en'] ?? $category->name ?? 'N/A' }}
                        </label>
                    </div>
                @empty
                    <p class="text-muted">No categories available for this menu.</p>
                @endforelse
            </div>
            @error('category_ids') <div class="text-danger">{{ $message }}</div> @enderror
            @error('category_ids.*') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        {{-- NEW: Modifier Groups Checkbox List --}}
        <div class="mb-3">
            <label class="form-label d-block">Assign to Modifier Groups (Optional)</label>
            <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                @php
                    // Get IDs of modifier groups currently assigned to the item
                    $itemModifierGroupIds = old('modifier_group_ids', $item->modifierGroups->pluck('id')->toArray());
                @endphp
                @forelse($modifierGroups as $group)
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" name="modifier_group_ids[]" value="{{ $group->id }}" id="modifier-group-{{ $group->id }}"
                            {{ in_array($group->id, $itemModifierGroupIds) ? 'checked' : '' }}>
                        <label class="form-check-label" for="modifier-group-{{ $group->id }}">
                            {{ $group->name['en'] ?? $group->name ?? 'N/A' }} ({{ ucfirst($group->type) }})
                        </label>
                    </div>
                @empty
                    <p class="text-muted">No modifier groups available for this menu.</p>
                @endforelse
            </div>
            @error('modifier_group_ids') <div class="text-danger">{{ $message }}</div> @enderror
            @error('modifier_group_ids.*') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        {{-- Branch Assignment (Existing Code) --}}
        <div class="mb-3">
            <label for="branch_ids" class="form-label">Assign to Branches</label>
            <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                @php
                    // Get IDs of branches currently assigned to the item
                    $itemBranchIds = old('branch_ids', $item->branches->pluck('id')->toArray());
                @endphp
                @forelse($branches as $branch)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="branch_ids[]" value="{{ $branch->id }}" id="branch-{{ $branch->id }}"
                            {{ in_array($branch->id, $itemBranchIds) ? 'checked' : '' }}>
                        <label class="form-check-label" for="branch-{{ $branch->id }}">
                            {{ $branch->name['en'] ?? $branch->name ?? 'N/A' }}
                        </label>
                    </div>
                @empty
                    <p class="text-muted">No branches available for this menu. Please create branches first.</p>
                @endforelse
            </div>
            @error('branch_ids') <div class="text-danger">{{ $message }}</div> @enderror
            @error('branch_ids.*') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        {{-- Status Field --}}
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status" required>
                <option value="Active" {{ old('status', $item->status) == 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Inactive" {{ old('status', $item->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-dark mt-3 mb-5">Update Item</button>
    </form>
</div>
@endsection
