@extends('layouts.app')

@section('title', 'Categories')

@section('content')
<link rel="stylesheet" href="{{asset('/css/Inventory/categories.css')}}">

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
        <!-- Live Search Input (No Form Submission) -->
        <div class="search-bar">
            <input type="text" id="searchInput" class="form-control" placeholder="Search categories..."
                value="{{ request('search') ?? '' }}">
        </div>
    </div>

    <div class="table-content">
        <table class="data-table">
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

        <!-- Empty State Message -->
        <div id="emptyState" style="display: none; text-align: center; padding: 40px;">
            <p style="font-size: 16px; color: #999;">No categories found.</p>
        </div>
    </div>

    <div class="pagination-links">
        <div class="result-links">
            <!-- Show result count only if categories exist -->
            @if($categories->total() > 0)
            <span id="resultCount">
                Showing {{ $categories->firstItem() }} to {{ $categories->lastItem() }} of {{ $categories->total() }} categories
            </span>
            @else
            <!-- Hidden by default, shown by jQuery when filtering returns results -->
            <span id="resultCount" style="display: none;"></span>
            @endif
        </div>

        <!-- Show pagination only if categories exist -->
        @if($categories->hasPages())
        <div>
            {{ $categories->appends(request()->query())->links('pagination::simple-bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{asset('/js/categories_page.js')}}"></script>
@endpush