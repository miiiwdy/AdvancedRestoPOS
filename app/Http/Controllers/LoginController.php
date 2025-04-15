<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\DataShift;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LoginController extends Controller
{
    public function index()
    {
        if (Auth::user()->hasRole(4)) {
            $now = Carbon::now();
            $Shift = DataShift::where([
                ['restos_id', '=', Auth::user()->restos_id],
                ['outlets_id', '=', Auth::user()->outlets_id],
                ['no_shift', '=', Auth::user()->shift]
            ])->first();
            $start = Carbon::createFromFormat('H:i:s', $Shift->shift_start)->setDate($now->year, $now->month, $now->day);
            $end = Carbon::createFromFormat('H:i:s', $Shift->shift_end)->setDate($now->year, $now->month, $now->day);
            if ($end->lessThan($start)) {
                $end->addDay();
            }
            $ShiftValidate = $now->between($start, $end);
        }

        if (auth()->user()->hasRole(1) || auth()->user()->hasRole(2) || auth()->user()->hasRole(3)) {
            return Inertia::location('/admin');
        } else if (Auth::user()->hasRole(4) && isset($ShiftValidate) && $ShiftValidate) {
            return Inertia::location('/pos');
        } else {
            Auth::logout();
            return Inertia::location('/login');
        }
    }
}
