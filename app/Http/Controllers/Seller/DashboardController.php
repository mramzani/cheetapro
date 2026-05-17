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
        /** @var \App\Models\Seller $seller */
        $seller = Auth::guard('seller')->user();
        $seller->load('wallet');
        $setting = Setting::current();

        return view('seller.dashboard', [
            'seller' => $seller,
            'setting' => $setting,
            'pricePerGb' => $seller->effectivePricePerGb($setting),
            'recentClients' => $seller->clients()->latest()->limit(10)->get(),
        ]);
    }
}
