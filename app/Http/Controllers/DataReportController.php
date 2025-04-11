<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Models\LaporanOrder;
use App\Models\LaporanOrderProduct;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DataReportController extends Controller
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
        $dataOrder = LaporanOrder::where([
            ['restos_id', '=', Auth::user()->restos_id],
            ['outlets_id', '=', Auth::user()->outlets_id],
        ])->get();
        $dataOrderProduct = LaporanOrderProduct::where([
            ['restos_id', '=', Auth::user()->restos_id],
            ['outlets_id', '=', Auth::user()->outlets_id],
        ])->get();
        $namaKasir = Auth::user()->name;
        $user = Auth::user();
        $namaResto = $user->resto ? $user->resto->nama_resto : null;
        $namaOutlet = $user->outlet ? $user->outlet->nama_outlet : null;

        return Inertia::render('SalesReport', [
            'product' => $product,
            'kategori' => $kategori,
            'namaKasir' => $namaKasir,
            'dataOrder' => $dataOrder,
            'dataOrderProduct' => $dataOrderProduct,
            'namaResto' => $namaResto,
            'namaOutlet' => $namaOutlet,
        ]);
    }
}
