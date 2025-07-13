@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Edit Modifier</h4>
        <form action="{{ route('menus.modifiers.destroy', ['menu' => $menu->id, 'modifier' => $modifier->id]) }}" method="POST"
            onsubmit="return confirm('Are you sure you want to delete this modifier? This action cannot be undone.')">
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

    <form method="POST" action="{{ route('menus.modifiers.update', ['menu' => $menu->id, 'modifier' => $modifier->id]) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name_en" class="form-label">Modifier Name (English) <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="name_en" name="name_en" value="{{ old('name_en', $modifier->name['en'] ?? '') }}" required>
            @error('name_en') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="name_ar" class="form-label">Modifier Name (Arabic) <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="name_ar" name="name_ar" value="{{ old('name_ar', $modifier->name['ar'] ?? '') }}" required>
            @error('name_ar') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">Price (SAR) <span class="text-danger">*</span></label>
            <input type="number" class="form-control" id="price" name="price" step="0.01" value="{{ old('price', $modifier->price ?? '') }}" required>
            @error('price') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description (Optional)</label>
            <textarea class="form-control" id="description" name="description" rows="2">{{ old('description', $modifier->description ?? '') }}</textarea>
            @error('description') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="modifier_group_id" class="form-label">Assign to Modifier Group (Optional)</label>
            <select class="form-select" id="modifier_group_id" name="modifier_group_id">
                <option value="">-- Select a Group --</option>
                @forelse($modifierGroups as $group)
                    <option value="{{ $group->id }}" {{ old('modifier_group_id', $modifier->modifier_group_id) == $group->id ? 'selected' : '' }}>
                        {{ $group->name['en'] ?? $group->name ?? 'N/A' }} ({{ ucfirst($group->type) }})
                    </option>
                @empty
                    <option value="">No modifier groups available for this menu.</option>
                @endforelse
            </select>
            @error('modifier_group_id') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        {{-- NEW: Direct Branch Assignment for Modifier --}}
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
                <option value="Active" {{ old('status', $modifier->status) == 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Inactive" {{ old('status', $modifier->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-dark mt-3 mb-5">Update Modifier</button>
    </form>
</div>
@endsection
