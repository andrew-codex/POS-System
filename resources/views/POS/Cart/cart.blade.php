@extends('layouts.app')

@section('title', 'Cart')

@section('content')
<link rel="stylesheet" href="{{ asset('/css/POS/cart.css') }}">

<div class="sale-content">

    <input id="barcode-input" type="text" autocomplete="off" class="barcode-input-hidden">


    <div class="product-content">

        <h2 class="title">Products</h2>
        <p class="subtitle">Select products or scan barcodes to add to cart</p>


        <div class="filters-row">

            <input type="search" id="search-input" class="filter-input" placeholder="Search products..."
                autocomplete="off">

            <select name="category" id="category-select" class="filter-input">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                <option value="{{ $category->id }}">
                    {{ $category->category_name }}
                </option>
                @endforeach
            </select>
        </div>


        @if($filteredProducts->isEmpty())
        <div id="empty-state" class="empty-state">
            <i class="bi bi-box-seam"></i>
            <h4>No products available</h4>
            <p>Add products to your inventory to get started</p>
        </div>
        @else
        <div class="products-grid" id="products-grid">
            @foreach($filteredProducts as $product)
            @php
            $stockQuantity = $product->stock?->quantity ?? 0;
            $isOutOfStock = $stockQuantity <= 0; @endphp <div
                class="product-card {{ $isOutOfStock ? 'product-card-disabled' : '' }}" data-id="{{ $product->id }}"
                data-name="{{ $product->product_name }}" data-price="{{ $product->product_price }}"
                data-description="{{ $product->product_description }}" data-stock="{{ $stockQuantity }}"
                data-category="{{ $product->category_id }}">

                <h4>{{ $product->product_name }}</h4>
                <small>{{ $product->product_description }}</small>
                <p>₱{{ number_format($product->product_price, 2) }}</p>
                @if($stockQuantity > 10)
                <small class="text-success">Stock: {{ $stockQuantity }}</small>
                @elseif($stockQuantity > 0 && $stockQuantity <= 10) <small class="text-warning">Low Stock:
                    {{ $stockQuantity }}</small>
                    @else
                    <small class="text-danger">Out of Stock</small>
                    @endif
        </div>
        @endforeach
    </div>

    <div id="empty-state" class="empty-state" style="display: none;">
        <i class="bi bi-search"></i>
        <h4>No products found</h4>
    </div>
    @endif

</div>


<div class="cart-content">

    <h3 class="cart-title">Cart</h3>

    <div id="cart-items"></div>

    <div class="totals" id="cart-totals">
        <div class="row">
            <span>Subtotal</span>
            <span id="subtotal">₱0.00</span>
        </div>
        <div class="total-row">
            <span>Total</span>
            <span id="total">₱0.00</span>
        </div>
    </div>

    <button class="btn-complete" id="open-payment">Pay</button>


</div>

</div>


<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">


            <form id="sale-form" action="{{ route('pos.sales.store') }}" method="POST">
                @csrf


                <div class="modal-header">
                    <h5 class="modal-title">Enter Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="mb-3">
                        <label for="form-total" class="form-label">Total Amount:</label>
                        <h3 id="payment-total">₱0.00</h3>
                        <input type="hidden" name="total" id="form-total">
                    </div>

                    <div class="mb-3">
                        <label for="amount_received" class="form-label">Amount Received:</label>
                        <input type="number" id="payment-amount" class="form-control" min="0" step="0.01"
                            placeholder="Enter amount" name="amount_received" required>
                    </div>

                    <div class="denoms mb-3">
                        <button type="button" class="btn btn-secondary denom-btn" data-value="20">₱20</button>
                        <button type="button" class="btn btn-secondary denom-btn" data-value="50">₱50</button>
                        <button type="button" class="btn btn-secondary denom-btn" data-value="100">₱100</button>
                        <button type="button" class="btn btn-secondary denom-btn" data-value="500">₱500</button>
                        <button type="button" class="btn btn-secondary denom-btn" data-value="1000">₱1000</button>
                    </div>

                    <div class="mb-3">
                        <label for="form-change" class="form-label">Change:</label>
                        <h3 id="payment-change">₱0.00</h3>
                        <input type="hidden" name="change" id="form-change">
                    </div>


                    <input type="hidden" name="cart" id="form-cart">

                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="confirm-payment">Complete Sale</button>
                </div>

            </form>
        </div>
    </div>
</div>

<script>
window.saleSuccess = @json(session('success') ?? '');
</script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="{{ asset('/Js/cart.js') }}"></script>
@endsection