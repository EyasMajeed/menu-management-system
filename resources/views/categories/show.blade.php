@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center">
        <h4>Category Details</h4>
        <form action="{{ route('categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure?')" class="ms-auto">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
        </form>
    </div>

    <form>
        <div class="mb-3 mt-3">
            <label class="form-label">Category Name (English)</label>
            <input type="text" class="form-control" value="{{ $category->name['en'] ?? '' }}" disabled>
        </div>

        <div class="mb-3">
            <label class="form-label">Category Name (Arabic)</label>
            <input type="text" class="form-control" value="{{ $category->name['ar'] ?? '' }}" disabled>
        </div>

        @if($category->items && $category->items->count())
            <div class="mb-3">
                <label class="form-label">Items in this Category:</label>
                <ul class="list-group">
                    @foreach($category->items as $item)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $item->name['en'] ?? '' }}
                            <span class="badge bg-secondary">{{ $item->price }} SAR</span>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif

        <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-primary mt-3">Edit Category</a>
    </form>
</div>
@endsection
