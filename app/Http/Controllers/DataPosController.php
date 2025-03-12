<?php

namespace App\Http\Controllers;

use App\Models\DiskonThresholdByOrder;
use App\Models\DiskonThresholdByProduct;
use App\Models\Kategori;
use App\Models\Pajak;
use App\Models\Payment;
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
        $pajak = Pajak::where([
            ['restos_id', '=', Auth::user()->restos_id],
            ['outlets_id', '=', Auth::user()->outlets_id],
            ['is_active', '=', true],
        ])->sum('jumlah_pajak');
        $payment = Payment::where([
            ['restos_id', '=', Auth::user()->restos_id],
            ['outlets_id', '=', Auth::user()->outlets_id],
            ['is_active', '=', true],
        ])->get();
        $diskonThresholdByOrder = DiskonThresholdByOrder::where([
            ['restos_id', '=', Auth::user()->restos_id],
            ['outlets_id', '=', Auth::user()->outlets_id],
            ['is_active', '=', true],
        ])->first();
        $diskonThresholdByProduct = DiskonThresholdByProduct::where([
            ['restos_id', '=', Auth::user()->restos_id],
            ['outlets_id', '=', Auth::user()->outlets_id],
            ['is_active', '=', true],
        ])->get();
        $namaKasir = Auth::user()->name;

        return Inertia::render('POS', [
            'product' => $product,
            'kategori' => $kategori,
            'namaKasir' => $namaKasir,
            'pajak' => $pajak,
            'payment' => $payment,
            'diskonThresholdByOrder' => $diskonThresholdByOrder,
            'diskonThresholdByProduct' => $diskonThresholdByProduct,
        ]);
    }
}
