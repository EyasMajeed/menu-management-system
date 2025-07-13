@extends('layouts.app')

@section('content')

<div class="container mt-5">
    @include('partials.menu-tabs', ['menu' => $menu])

    <div class="d-flex justify-content-end mb-3">
        <a href="{{ route('menus.items.create', $menu->id) }}" class="btn btn-black rounded-circle p-0 "
            style="width: 50px; height: 50px; display: flex; justify-content: center; align-items: center;"
            title="Create Item">
            <i class="bi bi-plus-circle-fill fs-3"></i>
        </a>
    </div>
    <div class="mt-1 ms-2 me-2">
        <div class="text-center">
            <div class="card shadow">
                <div class="card-body">
                    <table class="table custom-row-spacing-table">
                        <thead>
                            <th>Thumbnail</th>
                            <th>Name</th>
                            <th>Price</th>
                            <th>Item Categories</th>
                            <th>Branches</th>
                            <th>Publish Status</th>
                            <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody class="table-group-divider color:grey">
                            @foreach ($items as $item)
                            <tr onclick="handleRowClick(event, '{{ route('menus.items.edit', ['menu' => $menu->id, 'item' => $item->id]) }}')"
                                style="cursor: pointer;">
                                <td>

                                </td>

                                <td>
                                    {{ $item->name['en'] ?? '' }}
                                </td>

                                <td>
                                    {{ $item->price ?? '' }}
                                </td>

                                <td>
                                    {{ $item->categories->count() }}
                                </td>

                                <td>
                                    {{ $item->branches->count() }}
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


                                <td class=" text-center">
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
                        </tbody>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection