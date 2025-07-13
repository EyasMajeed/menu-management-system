@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Create Item</h5>
            <a href="{{ route('menus.items.index', $menu->id) }}" class="btn btn-sm btn-light">← Back to Items</a>
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

            <form method="POST" action="{{ route('menus.items.store', $menu->id) }}">
                @csrf

                <div class="mb-3">
                    <label for="name_en" class="form-label">Item Name (English)</label>
                    <input type="text" class="form-control" id="name_en" name="name[en]" value="{{ old('name.en') }}" required>
                    @error('name.en') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="name_ar" class="form-label">Item Name (Arabic)</label>
                    <input type="text" class="form-control" id="name_ar" name="name[ar]" value="{{ old('name.ar') }}" required>
                    @error('name.ar') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="price" class="form-label">Price</label>
                    <input type="number" step="0.01" class="form-control" id="price" name="price" value="{{ old('price') }}" required>
                    @error('price') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    {{-- CORRECTED: Single description field as confirmed not multi-language --}}
                    <textarea class="form-control" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                {{-- Categories Checkbox List (Existing Code) --}}
                <div class="mb-3">
                    <label class="form-label d-block">Assign to Categories</label>
                    <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                        @forelse($categories as $category)
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="category_ids[]" value="{{ $category->id }}" id="category-{{ $category->id }}"
                                    {{ in_array($category->id, old('category_ids', [])) ? 'checked' : '' }}>
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
                        @forelse($modifierGroups as $group)
                            <div class="form-check mb-1">
                                <input class="form-check-input" type="checkbox" name="modifier_group_ids[]" value="{{ $group->id }}" id="modifier-group-{{ $group->id }}"
                                    {{ in_array($group->id, old('modifier_group_ids', [])) ? 'checked' : '' }}>
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

                {{-- Status Dropdown --}}
            <div class="mb-4">
                <label for="status" class="block text-gray-700 text-sm font-bold mb-2">Status:</label>
                <select name="status" id="status" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                    <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>

                <button type="submit" class="btn btn-dark mt-3">Create Item</button>
            </form>
        </div>
    </div>
</div>
@endsection
