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
        </div>
    </div>

    <div class="form-container">
        <form id="edit-stock-form" action="{{ route('stock.update', $stock->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="product_id">Product Name<span class="required">*</span></label>
                <input type="text" value="{{ $stock->product->product_name }}" disabled>
                <input type="hidden" name="product_id" value="{{ $stock->product_id }}">
            </div>

            <div class="form-group">
                <label for="quantity">Quantity <span class="required">*</span></label>
                <input type="number" name="quantity" value="{{ $stock->quantity }}" required>
            </div>

            @if(isset($batches) && $batches->count() > 0)
            <div class="form-group">
                <label>Available Batches (FIFO)</label>
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Batch #</th>
                                <th>Received</th>
                                <th>Initial Qty</th>
                                <th>Remaining</th>
                                <th>Unit Cost</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($batches as $batch)
                            <tr>
                                <td>{{ $batch->batch_number }}</td>
                                <td>{{ $batch->received_date->format('Y-m-d') }}</td>
                                <td>{{ $batch->quantity_initial }}</td>
                                <td>{{ $batch->quantity_remaining }}</td>
                                <td>{{ number_format($batch->purchase_price, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <small class="text-muted">Decreasing quantity will consume stock from the oldest batches first (FIFO).</small>
            </div>
            @endif

            <div class="form-group">
                <label for="adjustment_reason">Adjustment Reason <span class="required">*</span></label>
                <textarea name="adjustment_reason" id="adjustment_reason" class="form-control" rows="2" required>{{ old('adjustment_reason') }}</textarea>
            </div>

            <div class="form-actions">
                <button type="button" class="btn-primary" onclick="confirmEdit('edit-stock-form')">Update
                    Stock</button>
            </div>
        </form>
    </div>
</div>
@endsection