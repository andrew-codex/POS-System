@extends('layouts.app')

@section('title', 'Refunds')

@section('content')
<link rel="stylesheet" href="{{ asset('/css/POS/refund.css') }}">
<div class="container">
    <div class="header d-flex align-items-center mb-4">
        <a href="{{ route('pos.sales') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h2 class="ms-3">Sales Refund Management</h2>
    </div>

    @if(session('error'))
    <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card mb-4">
        <div class="card-header">Refund for Sale #{{ $sale->invoice_no }}</div>
        <div class="card-body">
            <form action="{{ route('sales.refunds.store', ['sale' => $sale->id]) }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label>Refund Amount</label>
                    <input type="number" id="refund_amount" name="refund_amount" step="0.01"
                        class="form-control fw-bold text-primary" readonly required>
                </div>

                <div class="mb-3">
                    <label>Refund Type</label>
                    <select name="refund_type" class="form-control" required>
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="store_credit">Store Credit</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label>Reason (optional)</label>
                    <input type="text" name="refund_reason" class="form-control">
                </div>

                <div class="mb-3">
                    <h5>Refund Items</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Amount</th>
                                <th>Expired</th>
                                <th>Damaged</th>
                                <th>Changed</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $item)
                            <tr class="refund-row">
                                <td>{{ $item->product->product_name }}
                                    @if(in_array($item->product_id, $alreadyProcessed))
                                    <span class="badge bg-warning text-dark">Already Processed</span>
                                    @endif
                                </td>
                                <td>
                                    <input type="number" class="form-control refund-qty"
                                        name="items[{{ $item->id }}][quantity]" max="{{ $item->quantity }}" min="0"
                                        value="0" @if(in_array($item->product_id, $alreadyProcessed)) disabled @endif>
                                    <input type="hidden" class="refund-price" value="{{ $item->price }}">
                                    <input type="hidden" name="items[{{ $item->id }}][product_id]"
                                        value="{{ $item->product_id }}">
                                    <input type="hidden" name="items[{{ $item->id }}][price]"
                                        value="{{ $item->price }}">
                                </td>
                                <td>₱{{ number_format($item->price,2) }}</td>
                                <td class="refund-amount fw-bold text-primary">₱0.00</td>
                                <td>
                                    <input type="checkbox" name="items[{{ $item->id }}][is_expired]" value="1"
                                        @if(in_array($item->product_id, $alreadyProcessed)) disabled @endif>
                                </td>
                                <td>
                                    <input type="checkbox" name="items[{{ $item->id }}][is_damaged]" value="1"
                                        @if(in_array($item->product_id, $alreadyProcessed)) disabled @endif>
                                </td>
                                <td>
                                    <input type="checkbox" class="is-changed" name="items[{{ $item->id }}][is_changed]"
                                        value="1" @if(in_array($item->product_id, $alreadyProcessed)) disabled @endif>
                                </td>
                            </tr>

                            <tr class="exchange-row d-none bg-light">
                                <td colspan="7">
                                    <div class="row p-2">
                                        <div class="col-md-4">
                                            <label>Replace With</label>
                                            <select class="form-control new-product"
                                                name="items[{{ $item->id }}][new_product_id]">
                                                <option value="">-- Select --</option>
                                                @foreach($products as $p)
                                                <option value="{{ $p->id }}" data-price="{{ $p->product_price }}">
                                                    {{ $p->product_name }} - ₱{{ $p->product_price }}
                                                </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label>New Price</label>
                                            <input type="text" class="form-control new-price" readonly>
                                            <input type="hidden" name="items[{{ $item->id }}][new_price]"
                                                class="new-price-hidden">
                                        </div>
                                        <div class="col-md-4">
                                            <label>Difference</label>
                                            <input type="text" class="form-control difference fw-bold" readonly>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-primary">Process Refund</button>
                </div>
            </form>
        </div>
    </div>


    <div class="card mb-4">
        <div class="card-header">Refund Transactions</div>
        <div class="card-body">
            @forelse($refunds as $refund)
            <div class="mb-3 pb-3 p-3 border rounded shadow-sm bg-light">
                <h6>Refund #{{ $refund->id }} - Processed by {{ $refund->user->name ?? 'N/A' }}
                    ({{ $refund->created_at }})</h6>
                <p>
                    <strong>Amount:</strong> ₱{{ $refund->refund_amount }} |
                    <strong>Type:</strong> {{ ucfirst(str_replace('_',' ', $refund->refund_type)) }} |
                    <strong>Reason:</strong> {{ $refund->refund_reason ?? '-' }}
                    @if(!empty($refund->has_exchange))
                    | <span class="badge bg-info text-dark">Exchange Involved</span>
                    @endif
                </p>
            </div>
            @empty
            <p class="text-center text-muted">No refunds yet.</p>
            @endforelse


            @if(method_exists($refunds, 'links'))
            <div class="d-flex justify-content-center">{{ $refunds->links() }}</div>
            @endif
        </div>
    </div>

    <div class="card">
        <div class="card-header">Refund Items</div>
        <div class="card-body">
            @if(isset($refundItems) && $refundItems->count())
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Expired</th>
                        <th>Damaged</th>
                        <th>Changed</th>
                        <th>Status</th>
                        <th>Changed To</th>
                        <th>Old → New</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($refundItems as $item)
                    <tr>
                        <td>{{ $item->created_at }}</td>
                        <td>{{ $item->product->product_name ?? 'N/A' }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>₱{{ $item->price }}</td>
                        <td>{{ $item->is_expired ? 'Yes' : 'No' }}</td>
                        <td>{{ $item->is_damaged ? 'Yes' : 'No' }}</td>
                        <td>{{ $item->is_changed ? 'Yes' : 'No' }}</td>
                        <td>
                            <span class="badge-status status-pending" style="font-size: 0.7rem;">Already
                                Processed</span>

                            @if($item->is_changed)
                            <span class="badge-status status-exchanged">Exchanged</span>
                            @elseif($item->is_expired || $item->is_damaged)
                            <span class="badge-status status-canceled">Returned</span>
                            @else
                            <span class="badge-status status-completed">Refunded</span>
                            @endif
                        </td>
                        <td>{{ $item->newProduct->product_name ?? '-' }}</td>
                        <td>
                            @if($item->is_changed)
                            ₱{{ $item->price }} → ₱{{ $item->new_price }}
                            @else
                            -
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            @if(method_exists($refundItems, 'links'))
            <div class="d-flex justify-content-center">{{ $refundItems->links() }}</div>
            @endif
            @else
            <p class="text-center text-muted">No refund items yet.</p>
            @endif
        </div>
    </div>
</div>
<script src="{{asset('/Js/refund.js')}}"></script>
@endsection