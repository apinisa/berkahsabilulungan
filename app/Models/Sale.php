<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    protected $fillable = [
        'sale_date',
        'sale_number',
        'buyer_name',
        'discount',
        'total_payment',
        'payment_method',
        'note',
    ];

    protected $casts = [
        'sale_date' => 'date',
    ];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
