<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $seller = Auth::guard('seller')->user()->load('wallet');

        return view('seller.dashboard', [
            'seller' => $seller,
            'setting' => Setting::current(),
            'recentClients' => $seller->clients()->latest()->limit(10)->get(),
        ]);
    }
}
