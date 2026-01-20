@extends('layouts.app')

@section('title', 'Batches')

@section('content')


<div class="content">
    <div class="header">
        <div>
            <a href="{{ route('inventory.stock') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
        </div>
        <div class="title-section">
            <h2>Batches for {{ $product->product_name }}</h2>
            <p class="subtitle text-muted">Showing stock batches (FIFO queue) for this product.</p>
        </div>
    </div>

    <div class="alert alert-info">
        <strong>FIFO behavior:</strong> Batches are consumed in first-in, first-out order during sales or stock deductions. This page shows current batches and remaining quantities (read-only).
    </div>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Batch #</th>
                            <th>Received Date</th>
                            <th>Initial Qty</th>
                            <th>Remaining</th>
                            <th>Unit Cost</th>
                            <th>Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($batches as $batch)
                        <tr>
                            <td>{{ $batch->batch_number }}</td>
                            <td>{{ optional($batch->received_date)->format('M d, Y') }}</td>
                            <td>{{ $batch->quantity_initial }}</td>
                            <td>{{ $batch->quantity_remaining }}</td>
                            <td>₱{{ number_format($batch->purchase_price, 2) }}</td>
                            <td>₱{{ number_format($batch->quantity_remaining * $batch->purchase_price, 2) }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6">No batches found for this product.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    <strong>Total Inventory Value:</strong>
                    <span class="ms-2">₱{{ number_format($totalValue, 2) }}</span>
                </div>
                <div>
                    {{ $batches->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
