<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Outlet extends Model
{
    use HasFactory;
    protected $guarded = [
        'id',
    ];
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function resto()
    {
        return $this->belongsTo(Resto::class, 'restos_id');
    }
}
