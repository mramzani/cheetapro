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

    public function sync(XuiClient $xuiClient): RedirectResponse
    {
        $setting = Setting::current();
        $synced = 0;
        $deleted = 0;

        try {
            Client::query()->chunkById(100, function ($clients) use ($xuiClient, $setting, &$synced, &$deleted): void {
                foreach ($clients as $client) {
                    try {
                        $xuiClient->syncClient($client, $setting);
                        $synced++;
                    } catch (RuntimeException $exception) {
                        if (! $xuiClient->isMissingClientTrafficException($exception)) {
                            throw $exception;
                        }

                        $client->delete();
                        $deleted++;
                    }
                }
            });
        } catch (RuntimeException $exception) {
            return back()->withErrors(['sync' => $exception->getMessage()]);
        }

        return back()->with('status', $synced.' کلاینت بروزرسانی شد و '.$deleted.' کلاینت به علت پایان حجم حذف شد.');
    }

    public function toggle(Client $client, XuiClient $xuiClient): RedirectResponse
    {
        $enabled = $client->status === 'disabled';

        try {
            $xuiClient->updateClientEnabled(Setting::current(), $client, $enabled);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['client' => $exception->getMessage()]);
        }

        $client->update(['status' => $enabled ? 'active' : 'disabled']);

        return back()->with('status', $enabled ? 'کلاینت فعال شد.' : 'کلاینت غیرفعال شد.');
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
