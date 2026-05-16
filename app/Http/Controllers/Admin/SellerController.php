<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSellerRequest;
use App\Http\Requests\Admin\UpdateSellerRequest;
use App\Models\Seller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SellerController extends Controller
{
    public function index(): View
    {
        return view('admin.sellers.index', [
            'sellers' => Seller::with('wallet')->latest()->paginate(15),
        ]);
    }

    public function store(StoreSellerRequest $request): RedirectResponse
    {
        $seller = Seller::create($request->validated() + [
            'is_active' => $request->boolean('is_active', true),
        ]);

        $seller->wallet()->create(['balance' => 0]);

        return redirect()->route('admin.sellers.index')->with('status', 'فروشنده ساخته شد.');
    }

    public function edit(Seller $seller): View
    {
        return view('admin.sellers.edit', ['seller' => $seller]);
    }

    public function update(UpdateSellerRequest $request, Seller $seller): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active');

        if (blank($data['password'])) {
            unset($data['password']);
        }

        $seller->update($data);

        return redirect()->route('admin.sellers.index')->with('status', 'فروشنده به‌روزرسانی شد.');
    }
}
