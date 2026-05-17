<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use App\Http\Requests\Seller\StoreClientRequest;
use App\Models\Client;
use App\Models\Setting;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use App\Services\XuiClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;
use RuntimeException;

class ClientController extends Controller
{
    public function index(Request $request): View
    {
        /** @var \App\Models\Seller $seller */
        $seller = Auth::guard('seller')->user();
        $search = trim($request->string('q')->toString());

        return view('seller.clients.index', [
            'clients' => $this->filteredClients($seller->id, $search)->latest()->paginate(20)->withQueryString(),
            'search' => $search,
        ]);
    }

    public function sync(Request $request, XuiClient $xuiClient): RedirectResponse
    {
        /** @var \App\Models\Seller $seller */
        $seller = Auth::guard('seller')->user();
        $search = trim($request->string('q')->toString());
        $clients = $this->filteredClients($seller->id, $search)->get();
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

    public function create(): View
    {
        /** @var \App\Models\Seller $seller */
        $seller = Auth::guard('seller')->user();
        $setting = Setting::current();

        return view('seller.clients.create', [
            'seller' => $seller->load('wallet'),
            'setting' => $setting,
            'pricePerGb' => $seller->effectivePricePerGb($setting),
        ]);
    }

    public function store(StoreClientRequest $request, XuiClient $xuiClient): RedirectResponse
    {
        /** @var \App\Models\Seller $seller */
        $seller = Auth::guard('seller')->user();
        $setting = Setting::current();
        $inboundId = $seller->effectiveInboundId($setting);
        $pricePerGb = $seller->effectivePricePerGb($setting);
        $data = $request->validated();

        try {
            $client = DB::transaction(function () use ($seller, $setting, $data, $xuiClient, $inboundId, $pricePerGb): Client {
                $wallet = Wallet::query()->where('seller_id', $seller->id)->lockForUpdate()->firstOrFail();
                $cost = (int) $data['total_gb'] * $pricePerGb;

                if ($wallet->balance < $cost) {
                    throw new RuntimeException('موجودی کیف پول کافی نیست.');
                }

                if (! $inboundId) {
                    throw new RuntimeException('inbound_id در تنظیمات ثبت نشده است.');
                }

                $inbound = $xuiClient->findInbound($setting, $inboundId);
                $payload = [
                    'uuid' => (string) Str::uuid(),
                    'email' => $this->makeUniqueEmail($seller->username, (int) $data['total_gb']),
                    'sub_id' => $this->makeUniqueSubId(),
                    'total_bytes' => (int) $data['total_gb'] * 1024 * 1024 * 1024,
                    'expiry_time' => -1 * (int) $data['expiry_days'] * 24 * 60 * 60 * 1000,
                    'tg_id' => $data['tg_id'] ?? '',
                    'comment' => $data['comment'] ?? '',
                ];

                $xuiResponse = $xuiClient->addClient($setting, $payload, $inboundId);
                $links = $xuiClient->makeClientLinks($setting, $inbound, $payload);
                $before = $wallet->balance;
                $after = $before - $cost;

                $client = Client::create([
                    'seller_id' => $seller->id,
                    'uuid' => $payload['uuid'],
                    'email' => $payload['email'],
                    'sub_id' => $payload['sub_id'],
                    'inbound_id' => $inboundId,
                    'total_gb' => $data['total_gb'],
                    'total_bytes' => $payload['total_bytes'],
                    'expiry_time' => $payload['expiry_time'],
                    'tg_id' => $payload['tg_id'],
                    'comment' => $payload['comment'],
                    'cost' => $cost,
                    'status' => 'active',
                    'config_link' => $links['config_link'],
                    'subscription_link' => $links['subscription_link'],
                    'xui_response' => $xuiResponse,
                ]);

                $wallet->update(['balance' => $after]);

                WalletTransaction::create([
                    'seller_id' => $seller->id,
                    'type' => WalletTransaction::TYPE_CLIENT_PURCHASE,
                    'amount' => $cost,
                    'balance_before' => $before,
                    'balance_after' => $after,
                    'description' => 'ساخت کلاینت '.$client->email,
                ]);

                return $client;
            });
        } catch (RuntimeException $exception) {
            return back()->withErrors(['client' => $exception->getMessage()])->withInput();
        }

        return redirect()->route('seller.clients.show', $client)->with('status', 'کلاینت با موفقیت ساخته شد.');
    }

    public function show(Client $client): View
    {
        abort_unless($client->seller_id === Auth::guard('seller')->id(), 403);

        return view('seller.clients.show', ['client' => $client]);
    }

    public function toggle(Client $client, XuiClient $xuiClient): RedirectResponse
    {
        abort_unless($client->seller_id === Auth::guard('seller')->id(), 403);

        $enabled = $client->status === 'disabled';

        try {
            $xuiClient->updateClientEnabled(Setting::current(), $client, $enabled);
        } catch (RuntimeException $exception) {
            return back()->withErrors(['client' => $exception->getMessage()]);
        }

        $client->update(['status' => $enabled ? 'active' : 'disabled']);

        return back()->with('status', $enabled ? 'کلاینت فعال شد.' : 'کلاینت غیرفعال شد.');
    }

    private function makeUniqueEmail(string $username, int $totalGb): string
    {
        $prefix = preg_replace('/[^A-Za-z0-9]/', '', $username) ?: 'seller';
        $counter = Client::query()->count() + 101;

        do {
            $email = strtolower($prefix).$totalGb.'G'.$counter;
            $counter++;
        } while (Client::where('email', $email)->exists());

        return $email;
    }

    private function makeUniqueSubId(): string
    {
        do {
            $subId = Str::lower(Str::random(16));
        } while (Client::where('sub_id', $subId)->exists());

        return $subId;
    }

    private function filteredClients(int $sellerId, string $search)
    {
        return Client::query()
            ->where('seller_id', $sellerId)
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($query) use ($search): void {
                    $query->where('email', 'like', '%'.$search.'%')
                        ->orWhere('uuid', 'like', '%'.$search.'%');
                });
            });
    }
}
