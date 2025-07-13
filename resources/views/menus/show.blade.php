@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>{{ $menu->name }}</h3>
    </div>

    @include('partials.menu-tabs', ['menu' => $menu])


    {{-- Branches Information --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <p class="mb-0">
            <strong>Branches:</strong>
            @foreach($menu->branches as $branch)
            <span class="badge bg-dark me-1">{{ $branch->name }}</span>
            @endforeach
        </p>
        {{-- Edit Menu Button --}}
        <a href="{{ route('menus.edit', $menu->id) }}" class="btn btn-dark rounded-circle p-0"
            style="width: 30px; height: 30px; display: flex; justify-content: center; align-items: center;"
            title="Edit Menu">
            <i class="bi bi-pencil-fill fs-6"></i>
        </a>
    </div>


    {{-- Tab Contents --}}
    <div class="tab-content mt-3" id="menuTabsContent">
        @foreach($categories as $category)
        <div class="mt-1 ms-2 me-2 mb-5">
            <div class="card shadow">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3"> {{ $category->name ['en'] ?? ''}} </h5>
                    <div class="text-center">
                        <table class="table custom-row-spacing-table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Modifier Group</th>
                                    <th>Price</th>
                                    <th>Sync Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider color:grey">
                                @forelse($category->items as $item)
                                <tr onclick="handleRowClick(event, '{{ route('menus.items.edit', ['menu' => $menu->id, 'item' => $item->id]) }}')"
                                    style="cursor: pointer;">

                                    <td>
                                        {{ $item->name['en'] ?? '' }}
                                    </td>

                                    <td>
                                        {{ $item->modifierGroups->count() }}
                                    </td>

                                    <td>
                                        {{ $item->price }}
                                    </td>

                                    <td>
                                        <div>
                                            <span class="badge px-3 py-2 rounded-pill text-black" style="
                                            background-color: {{
                                            $item['status']  === 'Active' ? '#E0F7F0' :
                                            ($item['status']  === 'Inactive' ? '#808080' :  '#000000')
                                            }};  

                                            font-weight: 450;
                                            font-size: 1rem;
                                            padding: 0.75rem 1.5rem
                                            border radius: 10px">
                                                {{ ucfirst( $item['status'] ) }}
                                            </span>
                                        </div>
                                    </td>


                                    <td class="text-center">
                                        <div class="d-flex justify-content-center align-items-center">
                                            <a href="{{ route('menus.items.edit', ['menu' => $menu->id, 'item' => $item->id]) }}"
                                                class="btn btn-dark rounded-circle p-0"
                                                style="width: 30px; height: 30px; display: flex; justify-content: center; align-items: center;">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        </div>


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
        @endforeach

    </div>
    </form>
</div>



</div>
</div>
</div>
@endsection