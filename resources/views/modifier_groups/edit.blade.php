@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Edit Modifier Group: {{ $modifierGroup->name['en'] ?? $modifierGroup->name ?? 'N/A' }}</h4>
        <form action="{{ route('menus.modifier-groups.destroy', ['menu' => $menu->id, 'modifierGroup' => $modifierGroup->id]) }}" method="POST"
            onsubmit="return confirm('Are you sure you want to delete this modifier group? This action cannot be undone.')">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-danger">🗑️ Delete</button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>There were some problems with your input:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('menus.modifier-groups.update', ['menu' => $menu->id, 'modifierGroup' => $modifierGroup->id]) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name_en" class="form-label">Group Name (English) <span class="text-danger">*</span></label>
            {{-- FIX: Use name[en] for multi-language JSON field --}}
            <input type="text" class="form-control" id="name_en" name="name[en]" value="{{ old('name.en', $modifierGroup->name['en'] ?? '') }}" required>
            @error('name.en') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="name_ar" class="form-label">Group Name (Arabic) <span class="text-danger">*</span></label>
            {{-- FIX: Use name[ar] for multi-language JSON field --}}
            <input type="text" class="form-control" id="name_ar" name="name[ar]" value="{{ old('name.ar', $modifierGroup->name['ar'] ?? '') }}" required>
            @error('name.ar') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">Type <span class="text-danger">*</span></label>
            <select class="form-select" id="type" name="type" required>
                <option value="optional" {{ old('type', $modifierGroup->type) == 'optional' ? 'selected' : '' }}>Optional</option>
                <option value="required" {{ old('type', $modifierGroup->type) == 'required' ? 'selected' : '' }}>Required</option>
            </select>
            @error('type') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        {{-- NEW: Items Checkbox List --}}
        <div class="mb-3">
            <label class="form-label d-block">Assign to Items (Optional)</label>
            <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                @php
                    // Get IDs of items currently assigned to this modifier group
                    $assignedItemIds = old('item_ids', $assignedItemIds); // Use the passed assignedItemIds
                @endphp
                @forelse($items as $item) {{-- $items will be passed from controller --}}
                    <div class="form-check mb-1">
                        <input class="form-check-input" type="checkbox" name="item_ids[]" value="{{ $item->id }}" id="item-{{ $item->id }}"
                            {{ in_array($item->id, $assignedItemIds) ? 'checked' : '' }}>
                        <label class="form-check-label" for="item-{{ $item->id }}">
                            {{ $item->name['en'] ?? $item->name ?? 'N/A' }} (Price: {{ $item->price }})
                        </label>
                    </div>
                @empty
                    <p class="text-muted">No items available for this menu.</p>
                @endforelse
            </div>
            @error('item_ids') <div class="text-danger">{{ $message }}</div> @enderror
            @error('item_ids.*') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        {{-- Branch Assignment (Existing Code) --}}
        <div class="mb-3">
            <label for="branch_ids" class="form-label">Assign to Branches</label>
            <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                @php
                    $assignedBranchIds = old('branch_ids', $assignedBranchIds);
                @endphp
                @forelse($branches as $branch)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="branch_ids[]" value="{{ $branch->id }}" id="branch-{{ $branch->id }}"
                            {{ in_array($branch->id, $assignedBranchIds) ? 'checked' : '' }}>
                        <label class="form-check-label" for="branch-{{ $branch->id }}">
                            {{ $branch->name['en'] ?? $branch->name ?? 'N/A' }}
                        </label>
                    </div>
                @empty
                    <p class="text-muted">No branches available for this menu.</p>
                @endforelse
            </div>
            @error('branch_ids') <div class="text-danger">{{ $message }}</div> @enderror
            @error('branch_ids.*') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-dark mt-3">Update Modifier Group</button>
    </form>
</div>
@endsection
