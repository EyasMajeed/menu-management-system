@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Create New Menu</h5>
            <a href="{{ route('menus.index') }}" class="btn btn-sm btn-light">← Back to Menus</a>
        </div>

        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>There were some problems with your input:</strong>
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form method="POST" action="{{ route('menus.store') }}">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Menu Name</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                    @error('name') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description (Optional)</label>
                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="brand_id" class="form-label">Brand</label>
                    <select class="form-select" id="brand_id" name="brand_id" required>
                        <option value="">Select a Brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('brand_id') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="branches" class="form-label">Assign to Branches</label>
                    <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                        @forelse($branches as $branch)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="branch_ids[]" value="{{ $branch->id }}" id="branch-{{ $branch->id }}"
                                    {{ in_array($branch->id, old('branch_ids', [])) ? 'checked' : '' }}>
                                <label class="form-check-label" for="branch-{{ $branch->id }}">
                                    {{ $branch->name['en'] ?? $branch->name ?? 'N/A' }}
                                </label>
                            </div>
                        @empty
                            <p class="text-muted">No branches available. Please create branches first.</p>
                        @endforelse
                    </div>
                    @error('branch_ids') <div class="text-danger">{{ $message }}</div> @enderror
                    @error('branch_ids.*') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="cuisine_type" class="form-label">Cuisine Type (Optional)</label>
                    <input type="text" class="form-control" id="cuisine_type" name="cuisine_type" value="{{ old('cuisine_type') }}">
                    @error('cuisine_type') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="menu_type" class="form-label">Menu Type (Optional)</label>
                    <input type="text" class="form-control" id="menu_type" name="menu_type" value="{{ old('menu_type') }}">
                    @error('menu_type') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <button type="submit" class="btn btn-dark mt-3">Create Menu</button>
            </form>
        </div>
    </div>
</div>
@endsection
