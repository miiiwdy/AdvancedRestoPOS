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
                
            $currentHour = $now->format('H:i');
            $ShiftStart = $Shift->shift_start;
            $ShiftEnd = $Shift->shift_end;
    
            $ShiftValidate = $currentHour >= $ShiftStart && $currentHour < $ShiftEnd;
        }

        if (auth()->user()->hasRole(1) || auth()->user()->hasRole(2) || auth()->user()->hasRole(3)) {
            return Inertia::location('/admin');
        } else if(Auth::user()->hasRole(4) && $ShiftValidate) {
            return Inertia::location('/pos');
        } else {
            Auth::logout();
            return Inertia::location('/login');
        }
    }
}
