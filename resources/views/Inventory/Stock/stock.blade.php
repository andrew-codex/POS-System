@extends('layouts.app')

@section('title', 'Stock')

@section('content')

<link rel="stylesheet" href="{{asset('/css/Inventory/stock.css')}}">

<div class="content">
    <div class="header">
        <div>
            <h2 class="title">Inventory Stock</h2>
            <p class="subtitle">Manage your product stock levels</p>
        </div>
        <div>
            <a class="btn-primary" href="{{ route('stock.create') }}">
                <i class="bi bi-plus"></i>Add Stock
            </a>
        </div>
    </div>

    <div class="table-header">
        <div class="search-bar">
            <input type="text" id="searchInput" class="form-control" placeholder="Search stock..."
                value="{{ request('search') ?? '' }}">

            <div class="filter-bar">
                <select id="categoryFilter">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="stock-container">
    
        <table class="stock-table data-table" style="display: {{ $stocks->isEmpty() ? 'none' : 'table' }};">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($stocks as $stock)
                <tr data-category="{{ $stock->product?->category_id }}">
                    <td>{{ $stock->product->product_name ?? '' }}</td>
                    <td>{{ $stock->product->category->category_name ?? '' }}</td>
                    <td><span
                            class="{{ $stock->quantity < 10 ? 'stock-low' : 'stock-normal' }}">{{ $stock->quantity }}</span>
                    </td>
                    <td>
                        <a href="{{ route('stock.edit', $stock->id) }}" class="btn-edit" role="button">
                            <i class="bi bi-pencil"></i>Edit
                        </a>

                        <a class="btn-add-stock"
                            href="{{ route('stock.create', ['product_id'  => $stock->product->id]) }}">
                            <i class="bi bi-plus"></i> Add Stock
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div id="emptyState" class="empty-state" style="display: {{ $stocks->isEmpty() ? 'block' : 'none' }};">
            <i class="bi bi-box-seam"></i>
            <p class="empty-state-text">No stocks found.</p>
        </div>
    </div>

    <div class="pagination-links">
        <div class="result-links">
            @if($stocks->total() > 0)
            <span id="resultCount">
                Showing {{ $stocks->firstItem() }} to {{ $stocks->lastItem() }} of {{ $stocks->total() }} stocks
            </span>
            @else
            <span id="resultCount" style="display: none;"></span>
            @endif
        </div>

        @if(!$stocks->isEmpty() && $stocks->hasPages())
        <div>
            {{ $stocks->appends(request()->query())->links('pagination::simple-bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{asset('/js/stock_page.js')}}"></script>
@endpush