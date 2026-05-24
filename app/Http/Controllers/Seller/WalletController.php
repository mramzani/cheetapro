<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function transactions(): View
    {
        /** @var \App\Models\Seller $seller */
        $seller = Auth::guard('seller')->user();
        $seller->load('wallet');

        return view('seller.wallets.transactions', [
            'seller' => $seller,
            'transactions' => $seller->walletTransactions()
                ->with('createdBy')
                ->latest()
                ->paginate(20),
        ]);
    }
}
