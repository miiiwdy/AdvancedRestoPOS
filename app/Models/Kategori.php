<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $guarded = [
        'id',
    ];
    public function resto()
    {
        return $this->belongsTo(Resto::class);
    }
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }
    public function product()
    {
        return $this->hasMany(Product::class);
    }
}
