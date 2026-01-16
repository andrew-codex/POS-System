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
            <form action="{{ route('pos.sales') }}" method="GET" class="filter-form">

                <input type="text" name="search" placeholder="Search Sales..." class="input-field"
                    value="{{ request('search') }}">

                <select name="status" class="input-field" onchange="this.form.submit()">
                    <option value="" @if(request('status')=='' ) selected @endif>All Statuses</option>
                    <option value="completed" @if(request('status')=='completed' ) selected @endif>Completed</option>
                    <option value="pending" @if(request('status')=='pending' ) selected @endif>Pending</option>
                    <option value="canceled" @if(request('status')=='canceled' ) selected @endif>Canceled</option>
                    <option value="refunded" @if(request('status')=='refunded' ) selected @endif>Refunded</option>
                </select>

                <input type="date" name="start_date" class="input-field" value="{{ request('start_date') }}">

                <input type="date" name="end_date" class="input-field" value="{{ request('end_date') }}">

                <button class="btn-primary">Search</button>

                @if(request('search') || request('status') || request('start_date') || request('end_date'))
                <a href="{{ route('pos.sales') }}" class="btn-secondary">Clear</a>
                @endif
            </form>
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
                <tbody>
                    @foreach($sales as $sale)
                    <tr>
                        <td>{{ $sale->invoice_no }}</td>
                        <td>{{ $sale->created_at}}</td>
                        <td>{{ $sale->items->count() }}</td>
                        <td>{{ $sale->cashier->name }}</td>
                        <td>{{ $sale->total_amount }}</td>
                        <td class="badge-status">
                            @if($sale->status == 'completed')
                            <span class="badge-completed">Completed</span>
                            @elseif($sale->status == 'pending')
                            <span class="badge-pending">Pending</span>
                            @elseif($sale->status == 'canceled')
                            <span class="badge-canceled">Canceled</span>
                            @elseif($sale->status == 'exchanged')
                            <span class="badge-exchanged">Exchanged</span>
                            @elseif($sale->status == 'refunded')
                            <span class="badge-refunded">Refunded</span>
                            @elseif($sale->status == 'partially_refunded')
                            <span class="badge-partially-refunded">Partially Refunded</span>    
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('sales.refunds.index', $sale->id) }}" class="btn btn-sm btn-primary">View Refunds</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        <div class="pagination-links">
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
               {{ $sales->appends(request()->query())->links('pagination::simple-bootstrap-5') }}
            </div>
        </div>
        </div>
    </div>
</div>

@endsection