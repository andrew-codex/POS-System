@extends('layouts.app')

@section('title', 'Sales')
@section('content')
<link rel="stylesheet" href="{{ asset('/css/POS/sale.css') }}">
<div class="sale-content">

    <div class="sales-header">
        <h2 class="title">Sales</h2>
        <p class="subtitle">Manage your sales transactions</p>
    </div>

    <div class="sales-body">
        <div class="filter-section">
            <div class="filter-form">
                <input type="text" id="search-input" placeholder="Search by invoice, amount, or cashier..."
                    class="input-field" autocomplete="off">

                <select id="status-filter" class="input-field">
                    <option value="">All Statuses</option>
                    <option value="completed">Completed</option>
                    <option value="pending">Pending</option>
                    <option value="canceled">Canceled</option>
                    <option value="refunded">Refunded</option>
                </select>

                <input type="date" id="start-date" class="input-field">
                <input type="date" id="end-date" class="input-field">

                <button id="clear-filters" class="btn-secondary">Clear</button>
            </div>
        </div>

        <div class="sales-table">
            <table>
                <thead>
                    <tr>
                        <th>Invoice Number</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Created By</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="sales-tbody">
                    @foreach($sales as $sale)
                    <tr>
                        <td>{{ $sale->invoice_no }}</td>
                        <td>{{ $sale->created_at->format('M d, Y h:i A') }}</td>
                        <td>{{ $sale->items_count }}</td>
                        <td>{{ $sale->cashier->name }}</td>
                        <td>₱{{ $sale->total_amount }}</td>
                        <td class="badge-status">
                            @include('POS.Sales.partials.status-badge', ['status' => $sale->status])
                        </td>
                        <td>
                            <a href="{{ route('sales.refunds.index', $sale->id) }}" class="btn btn-sm btn-primary">
                                View Refunds
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div id="empty-state" class="text-center py-5" style="display: none;">
                <i class="bi bi-receipt" style="font-size: 48px; color: #ccc;"></i>
                <h5 class="mt-3 text-muted">No sales found</h5>
                <p class="text-muted">Try adjusting your filters</p>
            </div>

            <div class="pagination-links" id="pagination-info">
                <div class="result-links">
                    @if($sales->total() > 0)
                    <span>
                        Showing {{ $sales->firstItem() }} to {{ $sales->lastItem() }} of {{ $sales->total() }} sales
                    </span>
                    @else
                    <span class="result-links no-sales">No sales found.</span>
                    @endif
                </div>
                <div>
                    {{ $sales->links('pagination::simple-bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
window.appConfig = {
    routes: {
        salesSearch: "{{ route('api.sales.search') }}",
        refundsIndex: "{{ route('sales.refunds.index', ':id') }}"
    }
};
</script>
<script src="{{ asset('js/sales.js') }}"></script>
@endpush