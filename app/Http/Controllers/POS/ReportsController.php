<?php
namespace App\Http\Controllers\POS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\Sales;
use App\Models\SaleItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Barryvdh\DomPDF\Facade\Pdf;
class ReportsController extends Controller
{
  
    public function index(Request $request)
    {
        $this->validateDateInputs($request);

        $from = $request->input('from');
        $to = $request->input('to');
        $perPage = 10;
        $page = $request->get('page', 1);

       
        $salesByDateCacheKey = 'sales_by_date_' . ($from ?: 'all') . '_' . ($to ?: 'all');
        $salesByDateCollection = $this->rememberReports($salesByDateCacheKey, 60, function() use ($from, $to) {
            return Sales::getSalesByDateQuery($from, $to)->get()->toArray();
        });
        $salesByDate = $this->paginateCollection($salesByDateCollection, $page, $perPage);

       
        $salesByProductCacheKey = 'sales_by_product_' . ($from ?: 'all') . '_' . ($to ?: 'all');
        $salesByProductCollection = $this->rememberReports($salesByProductCacheKey, 60, function() {
            return SaleItem::getSalesByProductQuery()->get()->toArray();
        });
        $salesByProduct = $this->paginateCollection($salesByProductCollection, $page, $perPage);

        
        $invoiceDetailsCacheKey = 'invoice_details_' . ($from ?: 'all') . '_' . ($to ?: 'all');
        $invoiceDetailsCollection = $this->rememberReports($invoiceDetailsCacheKey, 60, function() use ($from, $to) {
            return Sales::getInvoiceDetailsQuery($from, $to)->get()->toArray();
        });
        $invoiceDetails = $this->paginateCollection($invoiceDetailsCollection, $page, $perPage);

        return view('POS.Reports.reports', compact('salesByDate','salesByProduct','invoiceDetails'));
    
    }


     public function exportSalesByDatePDF(Request $request)
    {
        $this->validateDateInputs($request);
        $from = $request->input('from');
        $to = $request->input('to');

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
        $this->validateDateInputs($request);
        $from = $request->input('from');
        $to = $request->input('to');

        $invoiceDetails = Sales::getInvoiceDetailsQuery($from, $to)->get();

        $pdf = Pdf::loadView('POS.Reports.pdf.invoice_details', compact('invoiceDetails', 'from', 'to'));
        return $pdf->download('invoice_details.pdf');
    }



   

    private function validateDateInputs(Request $request)
    {
        $validator = Validator::make($request->only(['from', 'to']), [
            'from' => 'nullable|date|date_format:Y-m-d',
            'to' => 'nullable|date|date_format:Y-m-d',
        ]);

        $validator->after(function ($v) use ($request) {
            $from = $request->input('from');
            $to = $request->input('to');
            if (!empty($from) && !empty($to)) {
                try {
                    $fromDt = Carbon::createFromFormat('Y-m-d', $from);
                    $toDt = Carbon::createFromFormat('Y-m-d', $to);
                    if ($fromDt->gt($toDt)) {
                        $v->errors()->add('to', 'The to date must be after or equal to the from date.');
                    }
                } catch (\Exception $e) {
                }
            }
        });

        $validator->validate();
    }

    private function rememberReports(string $key, int $ttl, callable $callback)
    {
        $store = Cache::getStore();
        if (method_exists($store, 'tags')) {
            return Cache::tags(['reports'])->remember($key, $ttl, $callback);
        }

        return Cache::remember($key, $ttl, $callback);
    }

    private function paginateCollection($collection, int $page, int $perPage)
    {
        $col = collect($collection);
        return new LengthAwarePaginator(
            $col->forPage($page, $perPage)->values(),
            $col->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'query' => request()->query()]
        );
    }
}
