@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<style>
.hover-scale:hover {
    transform: scale(1.05);
    transition: transform 0.4s ease-in-out;
    cursor: pointer;
}
</style>
<div class="container-fluid p-2">
    <h2 class="fw-bold">Dashboard</h2>
    <p class="text-muted">Welcome back!  <span class="fw-bold">{{ Auth::user()->name }}</span></p>

    <div class="row g-3">

        <div class="col-md-3">
            <div class="card p-3  hover-scale">
                <small class="text-muted fw-bold">Today's Sales</small>
                <h3 class="fw-bold">₱{{ number_format($totalSales ?? 0, 2) }}</h3>
                <small class="text-success fs-7">{{ $diffSales }} from yesterday</small>
            </div>
        </div>

        <div class="col-md-3  hover-scale">
            <div class="card p-3 shadow-sm">
                <small class="text-muted fw-bold">Transactions</small>
                <h3 class="fw-bold">0</h3>
                <small class="text-success fs-7">0 from yesterday</small>
            </div>
        </div>

        <div class="col-md-3 hover-scale">
            <div class="card p-3 shadow-sm">
                <small class="text-muted fw-bold">Items Sold</small>
                <h3 class="fw-bold">{{ $todayItems ?? 0}}</h3>
                <small class="fs-7">{{ $diffText }} Total items today</small>
            </div>
        </div>

        <div class="col-md-3 hover-scale">
            <div class="card p-3 shadow-sm border">
                <small class="text-muted fw-bold">Low Stock Items</small>
                <h3 class="fw-bold text-warning">{{ $lowStockItems->count() }}</h3>
                <small class="text-warning fs-7">Items need restocking</small>
            </div>
        </div>

    </div>

    <div class="row mt-4 g-3">

 
    <div class="col-lg-8">
        <div class="card shadow-sm p-3 mb-4">
            <h5 class="fw-bold mb-3">Top Selling Product Sales Over Time</h5>

            @if(count($dates) > 0)
                <canvas id="topSellingLineChart" height="120"></canvas>
            @else
                <p class="text-center text-muted">No sales data available.</p>
            @endif
        </div>
    </div>


        <div class="col-lg-4">

            <div class="card shadow-sm p-4 mb-3">
                <h5 class="fw-bold pb-2">Low Stock Alerts</h5>

                @if($lowStockItems->isEmpty())
                <div class="bg-light p-3 rounded text-left">
                    <strong class="text-success"><i class="bi bi-check-circle"></i> No low stock items</strong> <br>
                    <small class="text-muted">All products are sufficiently stocked.</small>
                </div>
                @else
                @foreach($lowStockItems as $item)
                <div class="p-2 rounded mb-3" style="background:#fef7e7; border:1px solid #f5e6c5;">
                    <strong class="text-muted">{{ $item->product->product_name }}</strong>
                    <div>
                        <small class="text-muted">{{ $item->quantity }} left in stock</small>
                    </div>

                </div>
                @endforeach
                @endif
            </div>


            <div class="card shadow-sm p-3">
                <h5 class="fw-bold mb-3">Quick Actions</h5>

                <a href="#" class="btn btn-outline-primary w-100 mb-2">
                    <i class="bi bi-cart"></i> Process a Sale
                </a>

                <a href="{{ route('inventory.products') }}" class="btn btn-outline-secondary w-100">
                    <i class="bi bi-box"></i> Manage Products
                </a>

            </div>
        </div>
    </div>
</div>

<script>
  @if(count($dates) > 0)
const saleDates = {!! json_encode($dates) !!};
const topSalesQuantity = {!! json_encode($quantities) !!};
const topProductName = "{{ $productName }}";

new Chart(document.getElementById('topSellingLineChart'), {
    type: 'line',
    data: {
        labels: saleDates,
        datasets: [{
            label: `${topProductName} - Quantity Sold`,
            data: topSalesQuantity,
            borderColor: 'rgba(13, 110, 253, 1)',
            backgroundColor: 'rgba(13, 110, 253, 0.2)',
            borderWidth: 2,
            tension: 0.3,
            fill: true,
            pointRadius: 4,
            pointBackgroundColor: 'rgba(13, 110, 253, 1)'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        },
        plugins: {
            legend: { display: true }
        }
    }
});
@endif
</script>
@endsection