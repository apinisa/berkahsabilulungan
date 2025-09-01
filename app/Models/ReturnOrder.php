<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnOrder extends Model
{
    use HasFactory;

    protected $table = 'return_orders';

    protected $fillable = [
        'return_number',
        'purchase_order_id',
        'product_id',
        'quantity',
        'reason',
        'total',
        'return_date',
    ];

    // Relasi ke PurchaseOrder
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    // Relasi ke Product
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'product_id');
    }

    public function items()
    {
    return $this->hasMany(ReturnOrderItem::class);
    }


}
