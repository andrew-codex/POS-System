@extends('layouts.app')

@section('title', 'All Batches')

@section('content')
<link rel="stylesheet" href="{{ asset('/css/Inventory/batches.css') }}">
<div class="content">
    <div class="header d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('inventory.stock') }}" class="btn-secondary">
                <i class="bi bi-arrow-left"></i>
            </a>
            <div class="title-section">
                <h2 class="page-title">All Stock Batches</h2>
               
            </div>
        </div>
    </div>

    <div class="modern-card">
        <div class="table-responsive">
            <table class="modern-table">
                <thead>
                    <tr>
                        <th>Batch #</th>
                        <th>Product</th>
                        <th>Received Date</th>
                        <th>Initial Qty</th>
                        <th>Remaining</th>
                        <th>Unit Cost</th>
                        <th class="text-end">Value</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($batches as $batch)
                    <tr>
                        <td class="fw-bold text-slate">{{ $batch->batch_number }}</td>
                        <td class="fw-medium text-dark">{{ optional($batch->product)->product_name }}</td>
                        <td>{{ optional($batch->received_date)->format('M d, Y') }}</td>
                        <td>{{ $batch->quantity_initial }}</td>
                        <td>
                            <span class="badge-status {{ $batch->quantity_remaining > 0 ? 'status-completed' : 'status-canceled' }}">
                                {{ $batch->quantity_remaining }}
                            </span>
                        </td>
                        <td class="text-slate">₱{{ number_format($batch->purchase_price, 2) }}</td>
                        <td class="text-end fw-bold text-primary">
                            ₱{{ number_format($batch->quantity_remaining * $batch->purchase_price, 2) }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">No batches found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="footer-summary d-flex justify-content-between align-items-center">
            <div class="total-value-box">
                <span class="label">Total Inventory Value:</span>
                <span class="value">₱{{ number_format($totalValue, 2) }}</span>
            </div>
            <div class="pagination-wrapper">
                {{ $batches->links() }}
            </div>
        </div>
    </div>
</div>
@endsection