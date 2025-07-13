@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Create Modifier</h5>
            <a href="{{ route('menus.modifiers.index', $menu->id) }}" class="btn btn-sm btn-light">← Back to Modifiers</a>
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

            <form method="POST" action="{{ route('menus.modifiers.store', $menu->id) }}">
                @csrf

                <div class="mb-3">
                    <label for="name_en" class="form-label">Modifier Name (English) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name_en" name="name_en" value="{{ old('name_en') }}" required>
                    @error('name_en') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="name_ar" class="form-label">Modifier Name (Arabic) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="name_ar" name="name_ar" value="{{ old('name_ar') }}" required>
                    @error('name_ar') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Price (SAR) <span class="text-danger">*</span></label>
                    <input type="number" class="form-control" id="price" name="price" step="0.01" value="{{ old('price') }}" required>
                    @error('price') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description (Optional)</label>
                    <textarea class="form-control" id="description" name="description" rows="2">{{ old('description') }}</textarea>
                    @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="modifier_group_id" class="form-label">Assign to Modifier Group (Optional)</label>
                    <select class="form-select" id="modifier_group_id" name="modifier_group_id">
                        <option value="">-- Select a Group --</option>
                        @forelse($modifierGroups as $group)
                            <option value="{{ $group->id }}" {{ old('modifier_group_id') == $group->id ? 'selected' : '' }}>
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
                        @forelse($branches as $branch)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="branch_ids[]" value="{{ $branch->id }}" id="branch-{{ $branch->id }}"
                                    {{ in_array($branch->id, old('branch_ids', [])) ? 'checked' : '' }}>
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

                <button type="submit" class="btn btn-dark mt-3">Create Modifier</button>
            </form>
        </div>
    </div>
</div>
@endsection
