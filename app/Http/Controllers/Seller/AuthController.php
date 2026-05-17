<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (Auth::guard('seller')->check()) {
            return redirect()->route('seller.dashboard');
        }

        if (Auth::check() && Auth::user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return view('seller.auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        if (! Auth::guard('seller')->attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors(['username' => 'اطلاعات ورود صحیح نیست.'])->onlyInput('username');
        }

        $seller = Auth::guard('seller')->user();

        /** @var \App\Models\Seller $seller */
        if (! $seller->is_active) {
            Auth::guard('seller')->logout();

            return back()->withErrors(['username' => 'حساب فروشنده غیرفعال است.'])->onlyInput('username');
        }

        $seller->forceFill(['last_login_at' => now()])->save();
        $request->session()->regenerate();

        return redirect()->intended(route('seller.dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('seller')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('seller.login');
    }
}
