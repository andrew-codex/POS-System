@extends('layouts.app')

@section('title', 'Edit Stock')

@section('content')
<link rel="stylesheet" href="{{ asset('/css/Inventory/edit_stock.css') }}">
<script src="{{ asset('/js/edit_stock.js') }}"></script>

<div class="content">
    <div class="header">
        <div>
            <a href="{{ route('inventory.stock') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
        </div>
        <div class="title-section">
            <h2>Edit Stock</h2>
            <p class="subtitle text-muted">Update product quantity and manage FIFO batches</p>
        </div>
    </div>

    <div class="alert alert-info">
        <strong>FIFO:</strong> Decreasing quantity consumes the oldest batches first; increasing quantity creates an adjustment batch for the added amount.
    </div>

    <div class="form-container">
        <form id="edit-stock-form" action="{{ route('stock.update', $stock->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="product_id">Product Name</label>
                <input type="text" value="{{ $stock->product->product_name }}" disabled>
                <input type="hidden" name="product_id" value="{{ $stock->product_id }}">
            </div>

            <div class="form-group">
                <label for="quantity">Quantity <span class="required">*</span></label>
                <input type="number" name="quantity" value="{{ $stock->quantity }}" required>
            </div>

      

            @if(isset($batches) && $batches->count() > 0)
            <div class="form-group full-width">
                <label class="mb-3">Available Batches (FIFO Queue)</label>
                <div class="modern-table-container">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Batch #</th>
                                <th>Received Date</th>
                                <th>Initial Qty</th>
                                <th>Remaining</th>
                                <th>Unit Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($batches as $batch)
                            <tr>
                                <td class="fw-bold text-slate">{{ $batch->batch_number }}</td>
                                <td>{{ $batch->received_date->format('M d, Y') }}</td>
                                <td>{{ $batch->quantity_initial }}</td>
                                <td><span class="badge-qty">{{ $batch->quantity_remaining }}</span></td>
                                <td class="fw-bold text-primary">₱{{ number_format($batch->purchase_price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="fifo-notice"><i class="bi bi-info-circle"></i> Decreasing quantity will consume stock from the oldest batches first.</p>
            </div>
            @endif

            <div class="form-actions">
                <button type="button" class="btn-primary" onclick="confirmEdit('edit-stock-form')">
                    Update Stock
                </button>
            </div>
        </form>
    </div>
</div>
@endsection