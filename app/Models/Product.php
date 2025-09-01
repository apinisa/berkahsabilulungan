<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $primaryKey = 'product_id';
    public $incrementing = false;
    protected $fillable = ['product_id', 'name', 'description', 'price', 'selling_price', 'stock', 'supplier_id'];


    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'product_id', 'product_id');
    }

    public function purchaseOrderItems()
    {
        return $this->hasMany(\App\Models\PurchaseOrderItem::class, 'product_id', 'product_id');
    }

    public function saleItems()
    {
    return $this->hasMany(SaleItem::class, 'product_id', 'product_id');
    }



}
