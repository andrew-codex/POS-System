@extends('layouts.app')

@section('title', 'Products')

@section('content')

<link rel="stylesheet" href="{{asset('/css/Inventory/products.css')}}">

<div class="content">
    <div class="header">
        <div>
            <h2 class="title">Products</h2>
            <p class="subtitle">Manage your products here</p>
        </div>
        <div>
            <a class="btn-primary" href="{{ route('products.create') }}">
                <i class="bi bi-plus"></i>Add Product
            </a>
        </div>
    </div>

    <div class="table-header">
        <div class="search-bar">

            <input type="text" id="searchInput" class="form-control" placeholder="Search products..." value="{{ request('search') ?? '' }}">

            <div class="filter-bar">
                <select id="category-filter">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->category_name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="table-content">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Barcode</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr data-category="{{ $product->category_id ?? '' }}">
                    <td>{{ $product->product_name }}</td>
                    <td>{{ $product->product_description }}</td>
                    <td>{{ $product->product_price }}</td>
                    <td>{{ $product->product_barcode }}</td>
                    <td>{{ $product->category->category_name ?? 'N/A' }}</td>
                    <td>
                        <a href="{{ route('products.edit', ['id' => $product->id]) }}" class="btn-edit" role="button">
                            <i class="bi bi-pencil"></i>Edit
                        </a>

                        <form id="delete-form-{{ $product->id }}"
                            action="{{ route('products.destroy', ['id' => $product->id]) }}" method="POST"
                            style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="button" onclick="confirmDelete('delete-form-{{ $product->id }}')"
                                class="btn-delete">
                                <i class="bi bi-trash"></i>Delete
                            </button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>


        <div id="emptyState" class="empty-state">
            <p class="empty-message">No products found.</p>
        </div>
    </div>


    <div class="pagination-links">
        <div class="result-links">

            @if(!$products->isEmpty())
            <span id="resultCount">
                Showing {{ $products->firstItem() }} to {{ $products->lastItem() }} of {{ $products->total() }} products
            </span>
            @else

            <span id="resultCount" style="display: none;"></span>
            @endif
        </div>

        @if($products->hasPages())
        <div>
            {{ $products->appends(request()->query())->links('pagination::simple-bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{asset('/js/products_page.js')}}"></script>
@endpush