<?php

use App\Http\Controllers\DataPosController;
use App\Http\Controllers\DataReportController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return Inertia::render('Welcome', [
//         'canLogin' => Route::has('login'),
//         'canRegister' => Route::has('register'),
//         'laravelVersion' => Application::VERSION,
//         'phpVersion' => PHP_VERSION,
//     ]);
// });
Route::get('/redirects', [LoginController::class, 'index'])->name('redirects');

Route::get('/', function() {
    return redirect('/login');
})->name('logginn');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/report', [DataReportController::class, 'index'])->name('report');
    Route::get('/pos', [DataPosController::class, 'index'])->name('pos');
    Route::post('/confirm-order', [DataPosController::class, 'store'])->name('confirm-order');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/print-receipt', function () {
        return Inertia::render('PrintReceipt');
    });
});

require __DIR__.'/auth.php';
