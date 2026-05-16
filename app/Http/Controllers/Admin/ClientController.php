<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Seller;
use App\Models\Setting;
use App\Services\XuiClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use RuntimeException;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        $clients = $this->filteredClients($request)
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.clients.index', [
            'clients' => $clients,
            'sellers' => Seller::orderBy('name')->get(),
            'selectedSellerId' => $request->integer('seller_id'),
            'search' => $request->string('q')->toString(),
        ]);
    }

    public function sync(Request $request, XuiClient $xuiClient): RedirectResponse
    {
        $clients = $this->filteredClients($request)->get();
        $setting = Setting::current();
        $synced = 0;

        try {
            foreach ($clients as $client) {
                $xuiClient->syncClient($client, $setting);
                $synced++;
            }
        } catch (RuntimeException $exception) {
            return back()->withErrors(['sync' => $exception->getMessage()]);
        }

        return back()->with('status', $synced.' کلاینت بروزرسانی شد.');
    }

    private function filteredClients(Request $request)
    {
        $search = trim($request->string('q')->toString());

        return Client::with('seller')
            ->when($request->integer('seller_id'), fn ($query, int $sellerId) => $query->where('seller_id', $sellerId))
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('email', 'like', '%'.$search.'%')
                        ->orWhere('uuid', 'like', '%'.$search.'%');
                });
            });
    }
}
