<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Seller;
use App\Models\Wallet;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', [
            'sellersCount' => Seller::count(),
            'clientsCount' => Client::count(),
            'walletsBalance' => Wallet::sum('balance'),
            'recentClients' => Client::with('seller')->latest()->limit(10)->get(),
        ]);
    }
}
