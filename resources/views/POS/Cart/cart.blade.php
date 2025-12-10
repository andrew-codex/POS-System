@extends('layouts.app')

@section('title', 'Cart')

@section('content')
<link rel="stylesheet" href="{{ asset('/css/POS/cart.css') }}">

<div class="sale-content">

    <input id="barcode-input" type="text" autocomplete="off" style="opacity:0; position:absolute; pointer-events:none;">


    <div class="product-content">

        <h2 class="title">Products</h2>
        <p class="subtitle">Select products or scan barcodes to add to cart</p>


        <form method="GET">
            <div class="filters-row">

                <input type="search" name="search" class="filter-input" placeholder="Search products..."
                    value="{{ request('search') }}">
                <select name="category" id="category-select" class="filter-input" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->category_name }}
                    </option>
                    @endforeach
                </select>
                <button class="btn btn-primary" type="submit">Search</button>
                @if(request('search'))
                <a href="{{ route('pos.cart') }}" class="btn btn-secondary">Clear</a>
                @endif
            </div>
        </form>


        <div class="products-grid">
            @foreach($filteredProducts as $product)
            <div class="product-card" data-id="{{ $product->id }}" data-name="{{ $product->product_name }}"
                data-price="{{ $product->product_price }}" data-description="{{ $product->product_description }}"
                data-stock="{{ $product->stock?->quantity ?? 0 }}" @if(!$product->stock || $product->stock->quantity <=
                    0) style="opacity:0.5; pointer-events:none;" @endif>

                    <h4>{{ $product->product_name }}</h4>
                    <small>{{ $product->product_description }}</small>
                    <p>₱{{ number_format($product->product_price, 2) }}</p>
                    @if($product->stock && $product->stock->quantity > 10)
                    <small class="text-success">Stock: {{ $product->stock->quantity }}</small>
                    @elseif($product->stock && $product->stock->quantity > 0 && $product->stock->quantity <= 10) <small
                        class="text-warning">Low Stock: {{ $product->stock->quantity }}</small>
                    @else
                        <small class="text-danger">Out of Stock</small>
                    @endif
            </div>
            @endforeach
        </div>

    </div>


    <div class="cart-content">

        <h3 class="cart-title">Cart</h3>

        <div id="cart-items"></div>

        <div class="totals">
            <div class="row">
                <span>Subtotal</span>
                <span id="subtotal">₱0.00</span>
            </div>
            <div class="total-row">
                <span>Total</span>
                <span id="total">₱0.00</span>
            </div>
        </div>

        <button class="btn-complete" id="open-payment">Complete Sale</button>


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
                        <label for="total" class="form-label">Total Amount:</label>
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
                        <label for="change" class="form-label">Change:</label>
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


<script src="{{ asset('/Js/cart.js') }}"></script>
@endsection