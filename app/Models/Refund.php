<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Sales;
use App\Models\User;
class Refund extends Model
{
    protected $fillable = [
        'sale_id',
        'refund_amount',
        'refund_type',
        'refunded_by',
        'refund_reason',
    ];

    public function items()
    {
        return $this->hasMany(RefundItem::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sales::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'refunded_by');
    }
}
