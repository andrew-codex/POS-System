@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="container-fluid mt-4">
    <h2 class="mb-3">Reports</h2>
    <ul class="nav nav-tabs" id="myTab" role="tablist">

        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="sales-tab" data-bs-toggle="tab" data-bs-target="#sales" type="button"
                role="tab" aria-controls="sales" aria-selected="true">Sales by Date & Product</button>
        </li>
        <li>
            <button class="nav-link" id="invoice-tab" data-bs-toggle="tab" data-bs-target="#invoice" type="button"
                role="tab" aria-controls="invoice" aria-selected="false">
                Invoice Details
            </button>
        </li>
    </ul>

    <div class="tab-content" id="myTabContent">



        <div class="tab-pane fade show active p-3" id="sales" role="tabpanel">
            <div class="report-sections mb-5 bg-white p-4 rounded-3 shadow-sm">
                <div class="mb-5">
                    @include('POS.Reports.partials.by_date')
                </div>

                <div class="mb-5">
                    @include('POS.Reports.partials.by_product')
                </div>
            </div>
        </div>


        <div class="tab-pane fade p-3" id="invoice" role="tabpanel">
            <div class="report-sections mb-5 bg-white p-4 rounded-3 shadow-sm">
                @include('POS.Reports.partials.invoice_details')
            </div>
        </div>

    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('/Js/reports.js') }}"></script>
@endsection
