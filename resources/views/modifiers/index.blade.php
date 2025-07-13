@extends('layouts.app')

@section('content')

<div class="container mt-5">
    @include('partials.menu-tabs', ['menu' => $menu])

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('menus.modifiers.create', $menu->id) }}" class="btn btn-black rounded-circle p-0"
            style="width: 50px; height: 50px; display: flex; justify-content: center; align-items: center;"
            title="New Modifier">
            <i class="bi bi-plus-circle-fill fs-3"></i>
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="mt-1 ms-2 me-2">
        <div class="text-center">
            <div class="card shadow">
                <div class="card-body">
                    <table class="table custom-row-spacing-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Modifier Group</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($modifiers as $modifier)
                            <tr onclick="handleRowClick(event, '{{ route('menus.modifiers.edit', ['menu' => $menu->id, 'modifier' => $modifier->id]) }}')"
                                style="cursor: pointer;">
                                <td>{{ $modifier->name['en'] ?? '' }}</td>
                                <td>{{ $modifier->price }} SAR</td>
                                <td>{{ $modifier->modifierGroup->name['en'] ?? 'Not Assigned' }}</td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <a href="{{ route('menus.modifiers.edit', ['menu' => $menu->id, 'modifier' => $modifier->id]) }}"
                                            class="btn btn-dark rounded-circle p-0"
                                            style="width: 30px; height: 30px; display: flex; justify-content: center; align-items: center;"
                                            title="Edit Modifier">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-muted py-3">No modifiers found for this menu.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript for row click --}}
<script>
    function handleRowClick(event, url) {
        if (event.target.closest('form') || event.target.closest('a') || event.target.tagName === 'BUTTON') {
            event.stopPropagation();
            return;
        }
        window.location = url;
    }
</script>

{{-- Custom CSS for table styling (reused from previous views) --}}
<style>
    .table-group-divider {
        border-top-color: #cccccc !important;
        border-top-width: 2px !important;
    }

    .custom-row-spacing-table th,
    .custom-row-spacing-table td {
        padding-top: 1rem;
        padding-bottom: 1rem;
    }
</style>
@endsection