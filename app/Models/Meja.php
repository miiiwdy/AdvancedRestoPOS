<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Meja extends Model
{
    use HasFactory;
    protected $guarded = [
        'id',
    ];
    public function kategoriMeja()
    {
        return $this->belongsTo(KategoriMeja::class, 'kategori_mejas_id');
    }
}
