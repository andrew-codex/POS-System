@extends('layouts.app')

@section('title', 'Create Stock')

@section('content')
<link rel="stylesheet" href="{{ asset('/css/Inventory/create_stock.css') }}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

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
        <label for="product_id">Product <span class="required">*</span></label>
        <select name="product_id" id="product_id" class="form-control" required>
            <option value="">Select Product</option>
            @foreach($products as $product)
                <option value="{{ $product->id }}" 
                    {{ old('product_id', $productId) == $product->id ? 'selected' : '' }}
                    data-price="{{ $product->product_price }}">
                    {{ $product->product_name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="form-group">
        <label for="quantity">Quantity <span class="required">*</span></label>
        <input type="number" name="quantity" id="quantity" class="form-control" 
               value="{{ old('quantity') }}" min="1" required>
    </div>

    <div class="form-group">
        <label for="purchase_price">Purchase Price (per unit)</label>
        <input type="number" name="purchase_price" id="purchase_price" 
               class="form-control" value="{{ old('purchase_price') }}" 
               step="0.01" min="0" required>
        <small class="form-text text-muted">
            Current selling price: <span id="selling-price">-</span>
        </small>
    </div>

    <div class="form-group">
        <label>Total Cost:</label>
        <div class="alert alert-info">
            <strong id="total-cost">₱0.00</strong>
        </div>
    </div>

            <div class="form-actions">
                <button type="button" id="createStockButton" class="btn-primary"
                    onclick="confirmCreate('create-stock-form')">
                    <i class="bi bi-plus"></i> Create Stock
                </button>
            </div>
        </form>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="{{ asset('/js/create_stock.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const productSelect = document.getElementById('product_id');
    const quantityInput = document.getElementById('quantity');
    const purchasePriceInput = document.getElementById('purchase_price');
    const sellingPriceSpan = document.getElementById('selling-price');
    const totalCostDiv = document.getElementById('total-cost');

    function updateCalculations() {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const sellingPrice = selectedOption.dataset.price || 0;
        const quantity = parseFloat(quantityInput.value) || 0;
        const purchasePrice = parseFloat(purchasePriceInput.value) || 0;
        const totalCost = quantity * purchasePrice;

        sellingPriceSpan.textContent = '₱' + parseFloat(sellingPrice).toFixed(2);
        totalCostDiv.textContent = '₱' + totalCost.toFixed(2);
    }

    productSelect.addEventListener('change', updateCalculations);
    quantityInput.addEventListener('input', updateCalculations);
    purchasePriceInput.addEventListener('input', updateCalculations);

    updateCalculations();
});
</script>

@endsection