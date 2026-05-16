<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureSellerIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $seller = Auth::guard('seller')->user();

        if (! $seller || ! $seller->is_active) {
            Auth::guard('seller')->logout();

            return redirect()->route('seller.login')->withErrors([
                'username' => 'حساب فروشنده غیرفعال است.',
            ]);
        }

        return $next($request);
    }
}
