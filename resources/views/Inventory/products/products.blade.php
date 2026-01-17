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
        <div class="controls-wrapper">
            <div class="search-container">
                <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                <input type="text" id="searchInput" class="search-input" placeholder="Search products..." value="{{ request('search') ?? '' }}">
            </div>

            <div class="filter-container">
                <select id="category-filter" class="filter-select">
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

<div class="table-card">
    <div class="table-responsive">
        <table class="data-table modern-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Barcode</th>
                    <th>Category</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                <tr data-category="{{ $product->category_id ?? '' }}">
                    <td class="fw-bold">{{ $product->product_name }}</td>
                    <td class="text-muted">{{ $product->product_description }}</td>
                    <td class="price-cell">{{ $product->product_price }}</td>
                    <td><code class="barcode-tag">{{ $product->product_barcode }}</code></td>
                    <td>
                        <span class="category-badge">
                            {{ $product->category->category_name ?? 'N/A' }}
                        </span>
                    </td>
                    <td class="text-right">
                        <div class="action-group">
                            <a href="{{ route('products.edit', ['id' => $product->id]) }}" class="btn-icon edit" title="Edit">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <form id="delete-form-{{ $product->id }}"
                                action="{{ route('products.destroy', ['id' => $product->id]) }}" method="POST"
                                style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" onclick="confirmDelete('delete-form-{{ $product->id }}')"
                                    class="btn-icon delete" title="Delete">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div id="emptyState" class="empty-state" style="display: none;">
        <i class="bi bi-box-seam"></i>
        <p>No products match your search.</p>
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

  
    @if(!$products->isEmpty() && $products->hasPages())
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