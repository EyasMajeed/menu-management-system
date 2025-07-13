@extends('layouts.app')

@section('content')
<div class="container mt-5">
    {{-- Top Bar with Button --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>All Menus</h4>
        <a href="{{ route('menus.create') }}" class="btn btn-success">+ Create New Menu</a>
    </div>

    {{-- Menus Table or Cards --}}
    <div class="mt-5 ms-2 me-2">
        <div class="text-center">
            <div class="card shadow">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table custom-row-spacing-table">
                            <thead>
                                <tr>
                                    <th>Menu Name</th>
                                    <th>Brand</th>
                                    <th>Branch</th>
                                    <th>Menu Type</th>
                                    <th>Edit</th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider color:grey">
                                @foreach($menus as $menu)
                                <tr onclick="handleRowClick(event, '{{ route('menus.show', $menu->id) }}')"
                                    style="cursor: pointer; py-3" >
                                    <td>{{ $menu->name }}</td>
                                    <td>{{ $menu->brand->name ?? 'N/A' }}</td>
                                    <td>
                                        @foreach($menu->branches as $branch)
                                        <span class="badge bg-secondary">{{ $branch->name }}</span>
                                        @endforeach
                                    </td>

                                    <td>{{ $menu->menu_type ?? 'N/A' }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('menus.edit', $menu->id) }}"
                                            class="btn btn-sm btn-primary me-1">
                                            <i class="bi bi-pencil"></i>
                                        </a>

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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection