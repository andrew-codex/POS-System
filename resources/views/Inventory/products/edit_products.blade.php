
    @extends('layouts.app')


    @section('title', 'Edit Products')

    @section('content')
    <link rel="stylesheet" href="{{asset('/css/Inventory/edit_products.css')}}">
    <script src="{{asset('/js/edit_products.js')}}"></script>
    <div class="content">
        <div class="header">
            <div>
                <button class=" btn btn-secondary">
                    <a href="{{ route('inventory.products') }}">
                        <i class="bi bi-arrow-left"></i> 
                    </a>
                </button>
            </div>
            <div class="title-section"> 
                 <h2>Edit a Product</h2>
            </div>
        </div>

        <div class="form-container">
            <form id="edit-form" action="{{ route('products.update', $product->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="product-name">Name <span class="required">*</span></label>
                    <input type="text" id="product-name" name="product_name" value="{{ $product->product_name }}" required>   
                </div>
                <div class="form-group">
                    <label for="description">Description <span class="required">*</span></label>
                    <input type="text" id="description" name="product_description" value="{{ $product->product_description }}" required>
                </div>
                <div class="form-group">
                    <label for="price">Price <span class="required">*</span></label>
                    <input type="number" id="price" name="product_price" step="0.01" value="{{ $product->product_price }}" required>
                </div>
                <div class="form-group">
                    <label for="barcode">Barcode <span class="required">*</span></label>
                    <input type="text" id="barcode" name="product_barcode" value="{{ $product->product_barcode }}" required>
                </div>
                <div class="form-group">
                    <label for="category">Category</label>
                   <select name="category_id" id="category" required>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                {{ $category->category_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-primary" onclick="confirmEdit('edit-form')">Update Product</button>
                </div>
            </form>
       
        </div>
    </div>
    @endsection