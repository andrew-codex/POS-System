    @extends('layouts.app')

    @section('title', 'Categories')

    @section('content')
    <link rel="stylesheet" href="{{asset('/css/Inventory/categories.css')}}">
    <script src="{{asset('/js/categories_page.js')}}"></script>
    <div class="content">
        <div class="header">
            <div>
                <h2 class="title">Categories</h2>
                <p class="subtitle">Manage your categories here</p>
            </div>
            <div>
                <a class="btn-primary" href="{{route('categories.create')}}">
                    <i class="bi bi-plus"></i>Add Category
                </a>
            </div>
        </div>

        <div class="table-header">
            <form action="{{ route('inventory.categories') }}" method="GET">
                <div class="search-bar">
                    <input type="text" name="search" class="form-control" placeholder="Search categories..."
                        value="{{ request('search') }}">
                    <button class="btn btn-primary p-2" type="submit">Search</button>
                    @if(request('search'))
                    <a href="{{ route('inventory.categories') }}" class="btn btn-secondary">Clear</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="table-content">
            <table>
                <thead>
                    <tr>
                        <th>Category Name</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($categories as $category)
                    <tr>
                        <td>{{ $category->category_name }}</td>
                        <td>{{ $category->category_description }}</td>
                        <td>
                            <a href="{{ route('categories.edit', $category->id) }}" class="btn-edit" role="button">
                                <i class="bi bi-pencil"></i>Edit
                            </a>

                            <form id="delete-form-{{ $category->id }}"
                                action="{{ route('categories.destroy', $category->id) }}" method="POST"
                                style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete('delete-form-{{ $category->id }}')"
                                    class="btn-delete">
                                    <i class="bi bi-trash"></i>Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pagination-links">
            <div class="result-links">
                @if($categories->total() > 0)
                <span>
                    Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} of
                    {{ $categories->total() }} categories
                </span>
                @else
                <span class="result-links no-categories">No categories found.</span>
                @endif
            </div>
            <div>
                {{ $categories->appends(request()->query())->links('pagination::simple-bootstrap-5') }}
            </div>
        </div>
    </div>
    @endsection