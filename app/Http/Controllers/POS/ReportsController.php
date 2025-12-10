<?php

namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\Sales;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Barryvdh\DomPDF\Facade\Pdf;
class ReportsController extends Controller
{
  
    public function index(Request $request)
    {

        $from = $request->from;
        $to = $request->to;
        $perPage = 10;
        $page = $request->get('page', 1);

       
        $salesByDateCollection = Cache::remember("sales_by_date_{$from}_{$to}", 60, function() use ($from, $to) {
            return Sales::getSalesByDateQuery($from, $to)->get()->toArray();
        });
        $salesByDate = new LengthAwarePaginator(
            collect($salesByDateCollection)->forPage($page, $perPage),
            count($salesByDateCollection),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

       
        $salesByProductCollection = Cache::remember("sales_by_product", 60, function() {
            return SaleItem::getSalesByProductQuery()->get()->toArray();
        });
        $salesByProduct = new LengthAwarePaginator(
            collect($salesByProductCollection)->forPage($page, $perPage),
            count($salesByProductCollection),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        
        $invoiceDetailsCollection = Cache::remember("invoice_details_{$from}_{$to}", 60, function() use ($from, $to) {
            return Sales::getInvoiceDetailsQuery($from, $to)->get()->toArray();
        });
        $invoiceDetails = new LengthAwarePaginator(
            collect($invoiceDetailsCollection)->forPage($page, $perPage),
            count($invoiceDetailsCollection),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('POS.Reports.reports', compact('salesByDate','salesByProduct','invoiceDetails'));
    
    }


     public function exportSalesByDatePDF(Request $request)
    {
        $from = $request->from;
        $to = $request->to;

        $salesByDate = Sales::getSalesByDateQuery($from, $to)->get();

        $pdf = Pdf::loadView('POS.Reports.pdf.sales_by_date', compact('salesByDate', 'from', 'to'));
        return $pdf->download('sales_by_date.pdf');
    }


    public function exportSalesByProductPDF(Request $request)
    {
        $salesByProduct = SaleItem::getSalesByProductQuery()->get();

        $pdf = Pdf::loadView('POS.Reports.pdf.sales_by_product', compact('salesByProduct'));
        return $pdf->download('sales_by_product.pdf');
    }


    public function exportInvoiceDetailsPDF(Request $request)
    {
        $from = $request->from;
        $to = $request->to;

        $invoiceDetails = Sales::getInvoiceDetailsQuery($from, $to)->get();

        $pdf = Pdf::loadView('POS.Reports.pdf.invoice_details', compact('invoiceDetails', 'from', 'to'));
        return $pdf->download('invoice_details.pdf');
    }



   
}
