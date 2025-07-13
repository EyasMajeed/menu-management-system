@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Create Category</h5>
            <a href="{{ route('menus.categories.index', $menu->id) }}" class="btn btn-sm btn-light">← Back to Categories</a>
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

            <form method="POST" action="{{ route('categories.store', $menu->id) }}">
                @csrf

                <div class="mb-3">
                    <label for="name_en" class="form-label">Category Name (English)</label>
                    <input type="text" class="form-control" id="name_en" name="name_en" value="{{ old('name_en') }}" required>
                    @error('name_en') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                <div class="mb-3">
                    <label for="name_ar" class="form-label">Category Name (Arabic)</label>
                    <input type="text" class="form-control" id="name_ar" name="name_ar" value="{{ old('name_ar') }}" required>
                    @error('name_ar') <div class="text-danger">{{ $message }}</div> @enderror
                </div>

                {{-- Item Assignment (Drag-and-drop for ordering, checkboxes for selection) --}}
                <div class="mb-3">
                    <label class="form-label d-block">Assign Items to Category (Drag to Reorder)</label>
                    <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                        <ul id="items-sortable" class="list-group">
                            @forelse($items as $item)
                                <li class="list-group-item d-flex align-items-center" data-id="{{ $item->id }}">
                                    <input class="form-check-input me-2" type="checkbox" name="item_ids[]" value="{{ $item->id }}" id="item-{{ $item->id }}"
                                        {{ in_array($item->id, old('item_ids', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label flex-grow-1" for="item-{{ $item->id }}">
                                        {{ $item->name['en'] ?? $item->name ?? 'N/A' }} ({{ $item->price }})
                                    </label>
                                    <i class="bi bi-grip-vertical handle ms-auto" style="cursor: grab;"></i>
                                </li>
                            @empty
                                <p class="text-muted">No items available for this menu. Please create items first.</p>
                            @endforelse
                        </ul>
                    </div>
                    @error('item_ids') <div class="text-danger">{{ $message }}</div> @enderror
                    @error('item_ids.*') <div class="text-danger">{{ $message }}</div> @enderror
                </div>
                <input type="hidden" name="item_order" id="item_order_input" value="{{ old('item_order', '[]') }}">

                {{-- NEW: Branch Assignment --}}
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

                <button type="submit" class="btn btn-dark mt-3">Create Category</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var el = document.getElementById('items-sortable');
        if (el) {
            var sortable = Sortable.create(el, {
                animation: 150,
                handle: '.handle', // Drag handle
                onEnd: function (evt) {
                    updateItemOrder();
                }
            });

            function updateItemOrder() {
                var itemOrder = [];
                el.querySelectorAll('li').forEach(function(itemElement, index) {
                    itemOrder.push(itemElement.dataset.id);
                });
                document.getElementById('item_order_input').value = JSON.stringify(itemOrder);
            }

            updateItemOrder();

            el.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox) {
                checkbox.addEventListener('change', updateItemOrder);
            });
        }
    });
</script>
@endsection
