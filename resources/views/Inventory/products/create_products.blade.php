@extends('layouts.app')

@section('title', 'Create Products')

@section('content')
<link rel="stylesheet" href="{{ asset('/css/Inventory/create_products.css') }}">
<script src="{{ asset('/js/create_products.js') }}"></script>

<div class="content">
    <div class="header">
        <div>
            <a href="{{ route('inventory.products') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
        </div>
        <div class="title-section">
            <h2>Create a New Product</h2>
        </div>
    </div>


    <div class="form-container">
        <form id="create-product-form" action="{{ route('products.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="product_name">Product Name <span class="required">*</span></label>
                <input type="text" name="product_name" required>
            </div>

              <div class="form-group">
                <label for="initial_stock">Initial Stock <span class="required">*</span></label>
                <input type="number" name="initial_stock" required>
                <small class="form-text text-muted mt-1">
                    This will create an initial stock batch for the product. The system uses FIFO (first-in, first-out) when selling — older batches are consumed before newer ones.
                </small>
            </div>

            <div class="form-group">
                <label for="product_description">Description <span class="required">*</span></label>
                <input type="text" name="product_description" required>
            </div>

            <div class="form-group">
                <label for="product_price">Price <span class="required">*</span></label>
                <input type="number" name="product_price" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="product_barcode">Barcode <span class="required">*</span></label>
                <input type="text" name="product_barcode" required>
            </div>

            <div class="form-group">
                <label for="category_id">Category <span class="required">*</span></label>
                <select name="category_id" required>
                    <option value="" disabled selected> Select Category </option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div class="form-actions">
                
                <button type="button" class="btn-primary" onclick="confirmCreate('create-product-form')" id="createProductButton">
                    <i class="bi bi-plus-circle"></i> Create Product
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
