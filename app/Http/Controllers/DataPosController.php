<?php

namespace App\Http\Controllers;

use App\Models\DataOrder;
use App\Models\DiskonThresholdByOrder;
use App\Models\DiskonThresholdByProduct;
use App\Models\Kategori;
use App\Models\KategoriDiskon;
use App\Models\Meja;
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
        ])->get();
        $diskonThresholdByProduct = DiskonThresholdByProduct::where([
            ['restos_id', '=', Auth::user()->restos_id],
            ['outlets_id', '=', Auth::user()->outlets_id],
            ['is_active', '=', true],
        ])->get();
        $kategoriDiskons = KategoriDiskon::where([
            ['restos_id', '=', Auth::user()->restos_id],
            ['outlets_id', '=', Auth::user()->outlets_id],
        ])->get();
        $tables = Meja::where([
            ['restos_id', '=', Auth::user()->restos_id],
            ['outlets_id', '=', Auth::user()->outlets_id],
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
            'kategoriDiskons' => $kategoriDiskons,
            'table' => $tables,
        ]);
    }
    public function store(Request $request) {
        $request->validate([
            'cart' => 'required|array',
            'cart.*.orderID' => 'required|string',
            'cart.*.orderType' => 'required|string',
            'cart.*.table' => 'nullable|string',
            'cart.*.guest' => 'required|integer',
            'cart.*.thb' => 'required|numeric',
            'cart.*.tt_b' => 'required|numeric',
            'cart.*.tt_a' => 'required|numeric',
            'cart.*.total_after_rounding' => 'required|numeric',
            'cart.*.rounding' => 'required|numeric',
            'cart.*.amount_paid' => 'required|numeric',
            'cart.*.change' => 'required|numeric',
            'cart.*.total_pajak' => 'required|numeric',
        ]);

        $totalHargaBeli = 0;
        $totalHargaJual = 0;
        $totalPajak = 0;
        $totalHargaBeforeRounding = 0;
        
        foreach ($request->cart as $item) {
            $totalHargaBeli += $item['thb'];
            $totalHargaJual += $item['tt_b'];
            $totalPajak += $item['total_pajak'];
            $totalHargaBeforeRounding += $item['tt_a'];
        }
        $rounding = $request->cart[0]['rounding'];
        if ($rounding < 0) {
            $rounding = 0;
        }
        $calculateKeuntungan = $totalHargaJual - $totalHargaBeli - $rounding;

        DataOrder::create([
            'restos_id' => Auth::user()->restos_id,
            'outlets_id' => Auth::user()->outlets_id,
            'order_id' => $request->cart[0]['orderID'],
            'nama_kasir' => Auth::user()->name,
            'order_type' => $request->cart[0]['orderType'],
            'tables' => $request->cart[0]['table'],
            'guest' => $request->cart[0]['guest'],
            'total_harga_beli' => $totalHargaBeli,
            'total_harga_jual' => $totalHargaJual,
            'total_harga_before_rounding' => $totalHargaBeforeRounding,
            'total_harga_after_all' => $request->cart[0]['total_after_rounding'],
            'rounding' => $request->cart[0]['rounding'],
            'amount_paid' => $request->cart[0]['amount_paid'],
            'change' => $request->cart[0]['change'],
            'total_pajak' => $totalPajak,
            'keuntungan' => $calculateKeuntungan,
        ]);
        return redirect()->back()->with('type', 'success')->with('message', 'Order selesai.');
    }
}
