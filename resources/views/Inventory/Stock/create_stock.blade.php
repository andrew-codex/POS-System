@extends('layouts.app')

@section('title', 'Create Stock')

@section('content')
<link rel="stylesheet" href="{{ asset('/css/Inventory/create_products.css') }}">
<script src="{{ asset('/js/create_stock.js') }}"></script>

<div class="content">
    <div class="header">
        <div>
            <a href="{{ route('inventory.stock') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
        </div>
        <div class="title-section">
            <h2>Create a New Stock</h2>
        </div>
    </div>

    <div class="form-container">
        <form id="create-stock-form" action="{{ route('stock.store') }}" method="POST">
            @csrf
            @method('POST')

            <div class="form-group">
                <label>Product Name<span class="required">*</span></label>
                @if(isset($productId))

                <input type="text" class="form-control"
                    value="{{ $products->firstWhere('id', $productId)->product_name }}" disabled>

                <input type="hidden" name="product_id" value="{{ $productId }}">

                @else

                <select name="product_id" class="form-group" required>
                    <option value="">Select a product</option>
                    @foreach ($products as $product)
                    <option value="{{ $product->id }}">{{ $product->product_name }}</option>
                    @endforeach
                </select>
                @endif
            </div>

            <div class="form-group">
                <label>Quantity <span class="required">*</span></label>
                <input type="number" name="quantity" required>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-primary" onclick="confirmCreate('create-stock-form')">Create
                    Stock</button>
            </div>
        </form>
    </div>
</div>


@endsection