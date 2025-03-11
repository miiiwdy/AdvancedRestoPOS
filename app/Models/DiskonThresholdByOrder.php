<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiskonThresholdByOrder extends Model
{
    use HasFactory;
    protected $guarded = [
        'id',
    ];
    public function resto()
    {
        return $this->belongsTo(Resto::class, 'restos_id');
    }
    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'outlets_id');
    }
    public function product()
    {
        return $this->belongsTo(Product::class, 'target_product_id');
    }
}
