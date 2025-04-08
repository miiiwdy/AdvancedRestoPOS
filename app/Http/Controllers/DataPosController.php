<?php

namespace App\Http\Controllers;

use App\Models\DiskonPercentageByOrder;
use App\Models\DiskonThresholdByOrder;
use App\Models\DiskonThresholdByProduct;
use App\Models\Kategori;
use App\Models\KategoriDiskon;
use App\Models\LaporanOrder;
use App\Models\LaporanOrderProduct;
use App\Models\LaporanPajak;
use App\Models\Meja;
use App\Models\Pajak;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Carbon\Carbon;

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
        $trackOrderData = LaporanOrder::where([
            ['restos_id', '=', Auth::user()->restos_id],
            ['outlets_id', '=', Auth::user()->outlets_id],
        ])
        ->whereDate('created_at', Carbon::today())
        ->get();
        $dataOrder = LaporanOrder::where([
            ['restos_id', '=', Auth::user()->restos_id],
            ['outlets_id', '=', Auth::user()->outlets_id],
        ])->get();
        $dataOrderProduct = LaporanOrderProduct::where([
            ['restos_id', '=', Auth::user()->restos_id],
            ['outlets_id', '=', Auth::user()->outlets_id],
        ])->get();
        $diskonPercentageByOrder = DiskonPercentageByOrder::where([
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
            'diskonPercentageByOrder' => $diskonPercentageByOrder,
            'kategoriDiskons' => $kategoriDiskons,
            'table' => $tables,
            'trackOrder' => $trackOrderData,
            'dataOrder' => $dataOrder,
            'dataOrderProduct' => $dataOrderProduct,
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
        $rounding = $request->cart[0]['rounding'];
        if ($rounding < 0) {
            $rounding = 0;
        }
        
        foreach ($request->cart as $item) {
            $totalHargaBeli += $item['thb'];
            $totalHargaJual += $item['tt_b'];
            $totalPajak += $item['total_pajak'];
            $totalHargaBeforeRounding += $item['tt_a'];

            LaporanOrderProduct::create([
                'restos_id' => Auth::user()->restos_id,
                'outlets_id' => Auth::user()->outlets_id,
                'order_id' => $item['orderID'],
                'nama_kasir' => Auth::user()->name,
                'id_product' => $item['id_product'],
                'nama_product' => $item['np'],
                'kode_product' => $item['kode_product'],
                'foto_product' => $item['foto_product'],
                'kategori_id' => $item['kategori'],
                'quantity' => $item['quantity'],
                'harga_beli_per_item' => $item['hb'],
                'total_harga_beli' => $item['thb'],
                'harga_jual_per_item' => $item['hj'],
                'total_harga_jual_before' => $item['tt_b'],
                'total_harga_jual_after' => $item['tt_a'],
                'total_pajak' => $item['total_pajak'],
                'total_after_rounding' => $item['total_after_rounding'],
                'note' => $item['note'],
                'order_type' => $item['orderType'],
            ]);
        }
        $calculateKeuntungan = $totalHargaJual - $totalHargaBeli - $rounding;

        LaporanOrder::create([
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
        LaporanPajak::create([
            'restos_id' => Auth::user()->restos_id,
            'outlets_id' => Auth::user()->outlets_id,
            'order_id' => $request->cart[0]['orderID'],
            'nama_kasir' => Auth::user()->name,
            'total_pajak' => $totalPajak,
        ]);

        return redirect()->back()->with('type', 'success')->with('message', 'Order telah berhasil dilakukan, Terima Kasih.');
    }
}
