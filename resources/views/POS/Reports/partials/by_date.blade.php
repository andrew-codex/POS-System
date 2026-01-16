<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Sales by Date</h4>
    <a href="{{ route('pos.reports.pdf.sales_by_date', ['from'=>request('from'), 'to'=>request('to'), 'tab'=>'sales']) }}"
       class="btn btn-danger btn-sm">
       <i class="bi bi-box-arrow-up-right"></i> Export PDF
    </a>
</div>

<form method="GET" class="row g-2 align-items-center mb-3">
    <input type="hidden" name="tab" value="{{ request('tab', 'sales') }}">
    
    <div class="col-auto">
        <label for="from-date" class="col-form-label">From:</label>
    </div>
    <div class="col-auto">
        <input id="from-date" type="date" name="from" value="{{ request('from') }}" class="form-control">
    </div>

    <div class="col-auto">
        <label for="to-date" class="col-form-label">To:</label>
    </div>
    <div class="col-auto">
        <input id="to-date" type="date" name="to" value="{{ request('to') }}" class="form-control">
    </div>

    <div class="col-auto">
        <button type="submit" class="btn btn-primary">Filter</button>
    </div>

    @if(request('from') || request('to'))
    <div class="col-auto">
        <a href="{{ route('pos.reports', ['tab' => 'sales']) }}" class="btn btn-secondary">Clear</a>
    </div>
    @endif
</form>

<table class="table table-bordered table-striped table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>Date</th>
            <th>Total Invoices</th>
            <th>Total Items Sold</th>
            <th class="text-end">Total Sales</th>
        </tr>
    </thead>
    <tbody>
        @forelse($salesByDate as $row)
        <tr>
            <td>{{ $row['sale_date'] }}</td>
            <td>{{ $row['total_invoices'] }}</td>
            <td>{{ $row['total_items_sold'] }}</td>
            <td class="text-end">{{ $row['total_sales'] }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="4" class="text-center">No records found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="d-flex justify-content-center">
    {{ $salesByDate->withQueryString()->links('pagination::bootstrap-5') }}
</div>
