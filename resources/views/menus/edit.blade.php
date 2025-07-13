@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Edit {{ $menu->name ?? '' }}</h4>
        <form action="{{ route('menus.destroy', $menu->id) }}" method="POST"
            onsubmit="return confirm('Are you sure you want to delete this menu? This action cannot be undone.')">
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
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form method="POST" action="{{ route('menus.update', $menu->id) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="name" class="form-label">Menu Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $menu->name ?? '') }}" required>
            @error('name') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description (Optional)</label>
            <textarea class="form-control" id="description" name="description" rows="3">{{ old('description', $menu->description ?? '') }}</textarea>
            @error('description') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="brand_id" class="form-label">Brand</label>
            <select class="form-select" id="brand_id" name="brand_id" required>
                <option value="">Select a Brand</option>
                @foreach($brands as $brand)
                    <option value="{{ $brand->id }}" {{ old('brand_id', $menu->brand_id) == $brand->id ? 'selected' : '' }}>
                        {{ $brand->name }}
                    </option>
                @endforeach
            </select>
            @error('brand_id') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="branches" class="form-label">Assign to Branches</label>
            <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                @php
                    $assignedBranchIds = old('branch_ids', $menu->branches->pluck('id')->toArray());
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
                    <p class="text-muted">No branches available for this brand.</p>
                @endforelse
            </div>
            @error('branch_ids') <div class="text-danger">{{ $message }}</div> @enderror
            @error('branch_ids.*') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="cuisine_type" class="form-label">Cuisine Type (Optional)</label>
            <input type="text" class="form-control" id="cuisine_type" name="cuisine_type" value="{{ old('cuisine_type', $menu->cuisine_type ?? '') }}">
            @error('cuisine_type') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label for="menu_type" class="form-label">Menu Type (Optional)</label>
            <input type="text" class="form-control" id="menu_type" name="menu_type" value="{{ old('menu_type', $menu->menu_type ?? '') }}">
            @error('menu_type') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        {{-- Status Field --}}
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status" required>
                <option value="Active" {{ old('status', $menu->status) == 'Active' ? 'selected' : '' }}>Active</option>
                <option value="Inactive" {{ old('status', $menu->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
            @error('status') <div class="text-danger">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-dark mt-3 mb-5">Update Menu</button>
    </form>
</div>
@endsection
