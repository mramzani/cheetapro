<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ChargeWalletRequest;
use App\Models\Seller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class WalletController extends Controller
{
    public function create(Seller $seller): View
    {
        $seller->load('wallet');

        return view('admin.wallets.charge', ['seller' => $seller]);
    }

    public function transactions(Seller $seller): View
    {
        $seller->load('wallet');

        return view('admin.wallets.transactions', [
            'seller' => $seller,
            'transactions' => $seller->walletTransactions()
                ->with('createdBy')
                ->latest()
                ->paginate(20),
        ]);
    }

    public function store(ChargeWalletRequest $request, Seller $seller): RedirectResponse
    {
        $data = $request->validated();

        DB::transaction(function () use ($seller, $data): void {
            $wallet = Wallet::query()->where('seller_id', $seller->id)->lockForUpdate()->firstOrFail();
            $before = $wallet->balance;
            $after = $before + (int) $data['amount'];

            $wallet->update(['balance' => $after]);

            WalletTransaction::create([
                'seller_id' => $seller->id,
                'created_by_user_id' => Auth::id(),
                'type' => WalletTransaction::TYPE_CHARGE,
                'amount' => $data['amount'],
                'balance_before' => $before,
                'balance_after' => $after,
                'description' => $data['description'] ?? 'شارژ دستی کیف پول',
            ]);
        });

        return redirect()->route('admin.sellers.index')->with('status', 'کیف پول شارژ شد.');
    }
}
