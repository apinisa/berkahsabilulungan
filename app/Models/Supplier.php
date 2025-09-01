<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $primaryKey = 'supplier_id';
    public $incrementing = false;
    protected $keyType = 'string';


    protected $fillable = [
        'supplier_id', 'name', 'contact_person', 'phone', 'email', 'address'
    ];

    public function getRouteKeyName()
{
    return 'supplier_id';
}

public function products()
{
    return $this->hasMany(Product::class, 'supplier_id', 'supplier_id');
}


}
