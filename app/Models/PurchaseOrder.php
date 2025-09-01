<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class PurchaseOrder extends Model
{
    use HasFactory;
    protected $fillable = [
        'order_number',
        'order_date',
        'supplier_id',
        'grand_total',
        'installment_target',
        'paid_off_date',
    ];

    public function items()
    {
    return $this->hasMany(PurchaseOrderItem::class, 'purchase_order_id');
    }

    public function supplier()
    {
    return $this->belongsTo(Supplier::class, 'supplier_id', 'supplier_id');
    }

    public function payments()
    {
    return $this->hasMany(PurchasePayment::class);
    }

    public function totalPaid()
    {
    return $this->payments()->sum('amount');
    }

    public function remaining()
{
    $grandTotal = $this->items->sum(function ($item) {
        return $item->price * $item->quantity;
    });

    return $grandTotal - $this->totalPaid();
}


}
