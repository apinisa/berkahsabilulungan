<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasePayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'payment_number',
        'installment_number',
        'amount',
        'payment_date',
        'remaining_amount',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

  // Tambahkan custom accessor status & sisa


public function getStatusAttribute()
{
    $po = $this->purchaseOrder;
    if (!$po) return 'TIDAK DIKETAHUI';

    return $po->totalPaid() >= $po->grand_total ? 'LUNAS' : 'BELUM LUNAS';
}

}
