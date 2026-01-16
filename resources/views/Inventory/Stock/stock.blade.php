@extends('layouts.app')

@section('title', 'Stock')

@section('content')

<link rel="stylesheet" href="{{asset('/css/Inventory/stock.css')}}">
<script src="{{asset('/js/stock_page.js')}}"></script>

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
        <form action="{{ route('inventory.stock') }}" method="GET">
            <div class="search-bar">
                <input type="text" name="search" class="form-control" placeholder="Search stock..."
                    value="{{'' . request('search', '') }}">

                <div class="filter-bar">
                    <label for="categoryFilter"></label>
                    <select id="categoryFilter" name="category" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                            {{ request('category') == $category->id ? 'selected' : '' }}>{{ $category->category_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <button class="btn btn-primary" type="submit">Search</button>
                @if(request('search'))
                <a href="{{ route('inventory.stock') }}" class="btn btn-secondary">Clear</a>
                @endif
            </div>
        </form>
    </div>


    <div class="stock-container">

        <table class="stock-table">
            <thead>
                <tr>
                    <th>Product Name</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="stockTableBody">
                @foreach($stocks as $stock)
                <tr data-category="{{ $stock->product?->category_id }}">
                    <td>{{ $stock->product->product_name }}</td>
                    <td>{{ $stock->product->category->category_name }}</td>
                    <td>{{ $stock->quantity }}</td>
                    <td>
                        <a href="{{ route('stock.edit', $stock->id) }}" class="btn-edit" role="button">
                            <i class="bi bi-pencil"></i>Edit
                        </a>


                        <a class="btn-add-stock"
                            href="{{ route('stock.create', ['product_id' => $stock->product->id]) }}">
                            <i class="bi bi-plus"></i> Add Stock
                        </a>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="pagination-links">
        <div class="result-links">
            @if($stocks->total() > 0)
            <span>
                Showing {{ $stocks->firstItem() }} to {{ $stocks->lastItem() }} of {{ $stocks->total() }} stocks
            </span>
            @else
            <span class="result-links no-products">No stocks found.</span>
            @endif
        </div>
        <div>
            {{ $stocks->appends(request()->query())->links('pagination::simple-bootstrap-5') }}
        </div>
    </div>
</div>

@endsection