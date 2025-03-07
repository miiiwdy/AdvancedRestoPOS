<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DataPosController extends Controller
{
    public function index() {
        $product = Product::with('kategori')
        ->where([
            ['restos_id', '=', Auth::user()->restos_id],
            ['outlets_id', '=', Auth::user()->outlets_id],
        ])->get();
        $kategori = Kategori::where([
            ['restos_id', '=', Auth::user()->restos_id],
            ['outlets_id', '=', Auth::user()->outlets_id],
        ])->get();
        $namaKasir = Auth::user()->name;

        return Inertia::render('POS', [
            'product' => $product,
            'kategori' => $kategori,
            'namaKasir' => $namaKasir,
        ]);
    }
}
