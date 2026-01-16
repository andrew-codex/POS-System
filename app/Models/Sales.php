<?php

namespace App\Models;
use App\Models\SaleItem;
use App\Models\User;
use App\Models\Refund;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class Sales extends Model
{
    protected $fillable = [
        'total_amount', 'amount_received', 'change_amount', 'created_by', 'invoice_no', 'status'
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class, 'sale_id'); 
    }

    public function cashier()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function refunds()
    {
        return $this->hasMany(Refund::class, 'sale_id');
    }


    public function totalRefunded()
    {
        return $this->refunds()->sum('refund_amount');
    }

    protected static function booted()
    {

        static::saved(function () {
            Cache::flush(); 
        });

        static::deleted(function () {
            Cache::flush();
        });

        static::created(function ($sale) {
            $sale->invoice_no = 'INV-' . now()->format('Ymd') . '-' . str_pad($sale->id, 6, '0', STR_PAD_LEFT);
            $sale->save();
        });
    }

    public static function getSalesByDateQuery($from = null, $to = null)
    {
        $query = self::join('sale_items as si', 'sales.id', '=', 'si.sale_id')
            ->select(
                DB::raw('DATE(sales.created_at) as sale_date'),
                DB::raw('COUNT(DISTINCT sales.id) as total_invoices'),
                DB::raw('SUM(si.quantity) as total_items_sold'),
                DB::raw('SUM(si.subtotal) as total_sales')
            )
            ->groupBy(DB::raw('DATE(sales.created_at)'))
            ->orderBy('sale_date', 'desc');

        if ($from && $to) {
            $query->whereBetween('sales.created_at', [$from, $to]);
        }

        return $query; 
    }

 
    public static function getInvoiceDetailsQuery($from = null, $to = null)
    {
        $query = self::join('sale_items as si', 'sales.id', '=', 'si.sale_id')
            ->join('products as p', 'si.product_id', '=', 'p.id')
            ->select(
                'sales.invoice_no',
                'sales.status',
                'sales.total_amount',
                'sales.amount_received',
                'sales.change_amount',
                'si.product_id',
                'p.product_name as product_name',
                'si.quantity',
                'si.price',
                'si.subtotal',
                'sales.created_at'
            )
            ->orderBy('sales.created_at', 'desc');

        if ($from && $to) {
            $query->whereBetween('sales.created_at', [$from, $to]);
        }

        return $query;
    }

    public function getTotalAmountAttribute($value)
    {
        return number_format($value, 2);
    }

    public function getAmountReceivedAttribute($value)
    {
        return number_format($value, 2);
    }

    public function getChangeAmountAttribute($value)
    {
        return number_format($value, 2);
    }

     public function getFormattedCreatedAtAttribute()
    {
        return date('M d, Y', strtotime($value));
    }

  
}
