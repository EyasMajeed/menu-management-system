@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h3>{{ $menu->name }}</h3>
    @include('partials.menu-tabs', ['menu' => $menu])

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('menus.modifier-groups.create', $menu->id) }}" class="btn btn-black rounded-circle p-0"
           style="width: 50px; height: 50px; display: flex; justify-content: center; align-items: center;"
           title="Create Modifier Group">
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
                                <th>Modifier Group</th> {{-- Changed from Name --}}
                                <th>Type</th>
                                <th>Active in</th> {{-- Changed from Items Count --}}
                                <th>Total Modifier Number</th> {{-- Changed from Modifiers Count --}}
                                <th>Publish Status</th> {{-- NEW: Added Publish Status column --}}
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($modifierGroups as $group) {{-- Changed $modifierGroup to $group for consistency with previous updates --}}
                            <tr onclick="handleRowClick(event, '{{ route('menus.modifier-groups.edit', ['menu' => $menu->id, 'modifier_group' => $group->id]) }}')"
                                style="cursor: pointer;"> 
                                <td>{{ $group->name['en'] ?? 'N/A' }}</td>
                                <td><span class="badge bg-secondary">{{ ucfirst($group->type) }}</span></td>
                                <td>
                                    {{ $group->items->count() }} Items {{-- Display count of associated items --}}
                                </td>
                                <td>{{ $group->modifiers->count() }} Modifiers</td>
                                <td> {{-- Display Publish Status --}}
                                    <div>
                                        <span class="badge px-3 py-2 rounded-pill text-black" style="
                                            background-color: {{
                                                $group->status === 'Active' ? '#2DFFB4' :
                                                ($group->status === 'Inactive' ? '#FFA500' : '#808080')
                                            }};
                                            font-weight: 450;
                                            font-size: 1rem;
                                            padding: 0.75rem 1.5rem;
                                            border-radius: 10px">
                                            {{ ucfirst( $group->status ) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center">
                                        <a href="{{ route('menus.modifier-groups.edit', ['menu' => $menu->id, 'modifier_group' => $group->id]) }}"
                                            class="btn btn-dark rounded-circle p-0"
                                            style="width: 30px; height: 30px; display: flex; justify-content: center; align-items: center;"
                                            title="Edit Modifier Group">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-muted py-3">No modifier groups found for this menu.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- JavaScript for search functionality --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchModifierGroupInput');
        const searchButton = document.getElementById('searchModifierGroupButton');
        const tableBody = document.getElementById('modifierGroupTableBody');
        const rows = tableBody.querySelectorAll('tr');

        function filterTable() {
            const searchTerm = searchInput.value.toLowerCase();
            rows.forEach(row => {
                // Assuming Modifier Group Name is in the first column (index 0)
                const modifierGroupName = row.children[0].textContent.toLowerCase(); 
                if (modifierGroupName.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        searchButton.addEventListener('click', filterTable);
        searchInput.addEventListener('keyup', filterTable); // Filter as user types
    });

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

