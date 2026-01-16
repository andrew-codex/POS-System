<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>Sales by Product</h4>
    <a href="{{ route('pos.reports.pdf.sales_by_product', ['tab'=>'sales']) }}"
       class="btn btn-danger btn-sm">
       <i class="bi bi-box-arrow-up-right"></i> Export PDF
    </a>
</div>

<table class="table table-bordered table-striped table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>Product</th>
            <th>Total Quantity Sold</th>
            <th class="text-end">Total Sales</th>
        </tr>
    </thead>
    <tbody>
        @forelse($salesByProduct as $row)
        <tr>
            <td>{{ $row['product_name'] }}</td>
            <td>{{ $row['total_quantity'] }}</td>
            <td class="text-end">{{ $row['total_sales']}}</td>
        </tr>
        @empty
        <tr>
            <td colspan="3" class="text-center">No records found.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="d-flex justify-content-center">
    {{ $salesByProduct->withQueryString()->links('pagination::bootstrap-5') }}
</div>
