<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Invoice Details</h4>
    <a href="{{ route('pos.reports.pdf.invoice_details', ['from'=>request('from'), 'to'=>request('to'), 'tab'=>'invoice']) }}"
       class="btn btn-danger btn-sm">
       <i class="bi bi-box-arrow-up-right"></i> Export PDF
    </a>
</div>

<div class="invoice-details-report mb-4 p-3">
    <form method="GET" class="row g-2 align-items-center mb-3">
        <input type="hidden" name="tab" value="{{ request('tab', 'invoice') }}">
        
        <div class="col-auto">
            <label for="from-invoice" class="col-form-label">From:</label>
        </div>
        <div class="col-auto">
            <input id="from-invoice" type="date" name="from" value="{{ request('from') }}" class="form-control">
        </div>

        <div class="col-auto">
            <label for="to-invoice" class="col-form-label">To:</label>
        </div>
        <div class="col-auto">
            <input id="to-invoice" type="date" name="to" value="{{ request('to') }}" class="form-control">
        </div>

        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>

        @if(request('from') || request('to'))
        <div class="col-auto">
            <a href="{{ route('pos.reports', ['tab' => 'invoice']) }}" class="btn btn-secondary">Clear</a>
        </div>
        @endif
    </form>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm align-middle">
            <thead class="table-light">
                <tr>
                    <th>Invoice No</th>
                    <th class="text-end">Total Amount</th>
                    <th class="text-end">Amount Received</th>
                    <th class="text-end">Change</th>
                    <th>Product</th>
                    <th class="text-end">Quantity</th>
                    <th class="text-end">Price</th>
                    <th class="text-end">Subtotal</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoiceDetails as $row)
                <tr>
                    <td>{{ $row['invoice_no'] }}</td>
                    <td class="text-end">{{ $row['total_amount'] }}</td>
                    <td class="text-end">{{ $row['amount_received'] }}</td>
                    <td class="text-end">{{ $row['change_amount'] }}</td>
                    <td>{{ $row['product_name'] }}</td>
                    <td class="text-end">{{ $row['quantity'] }}</td>
                    <td class="text-end">{{ $row['price'] }}</td>
                    <td class="text-end">{{ $row['subtotal'] }}</td>
                    <td>{{ $row['created_at'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">No records found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center">
        {{ $invoiceDetails->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
