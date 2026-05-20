<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Setting;
use GuzzleHttp\Cookie\CookieJar;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class XuiClient
{
    public function listInbounds(?Setting $setting = null): array
    {
        $setting ??= Setting::current();
        $jar = $this->login($setting);

        $response = Http::withOptions(['cookies' => $jar])
            ->acceptJson()
            ->get($this->url($setting, '/dash/panel/api/inbounds/list'));

        $body = $this->decode($response, 'دریافت inboundها از x-ui ناموفق بود.');

        return $body['obj'] ?? [];
    }

    public function findInbound(Setting $setting, int $inboundId): array
    {
        $inbound = collect($this->listInbounds($setting))->firstWhere('id', $inboundId);

        if (! is_array($inbound)) {
            throw new RuntimeException('inbound انتخاب‌شده در x-ui پیدا نشد.');
        }

        return $inbound;
    }

    public function addClient(Setting $setting, array $client, ?int $inboundId = null): array
    {
        $jar = $this->login($setting);
        $inboundId ??= $setting->xui_inbound_id;

        $settings = json_encode([
            'clients' => [[
                'id' => $client['uuid'],
                'alterId' => 0,
                'flow' => '',
                'email' => $client['email'],
                'limitIp' => 0,
                'totalGB' => $client['total_bytes'],
                'expiryTime' => $client['expiry_time'],
                'enable' => true,
                'tgId' => $client['tg_id'] ?? '',
                'subId' => $client['sub_id'],
                'comment' => $client['comment'] ?? '',
                'reset' => 0,
            ]],
        ], JSON_UNESCAPED_UNICODE);

        $response = Http::withOptions(['cookies' => $jar])
            ->acceptJson()
            ->asJson()
            ->post($this->url($setting, '/dash/panel/api/inbounds/addClient'), [
                'id' => (int) $inboundId,
                'settings' => $settings,
            ]);

        return $this->decode($response, 'ساخت کلاینت در x-ui ناموفق بود.');
    }

    public function updateClientEnabled(Setting $setting, Client $client, bool $enabled): array
    {
        $jar = $this->login($setting);
        $settings = json_encode([
            'clients' => [[
                'id' => $client->uuid,
                'email' => $client->email,
                'totalGB' => $client->total_bytes,
                'expiryTime' => $client->expiry_time,
                'enable' => $enabled,
            ]],
        ], JSON_UNESCAPED_UNICODE);

        $response = Http::withOptions(['cookies' => $jar])
            ->acceptJson()
            ->asForm()
            ->post($this->url($setting, '/dash/panel/api/inbounds/updateClient/'.$client->uuid), [
                'id' => $client->inbound_id,
                'settings' => $settings,
            ]);

        return $this->decode($response, 'تغییر وضعیت کلاینت در x-ui ناموفق بود.');
    }

    public function getClientTraffic(Setting $setting, string $email): array
    {
        $jar = $this->login($setting);

        $response = Http::withOptions(['cookies' => $jar])
            ->acceptJson()
            ->get($this->url($setting, '/dash/panel/api/inbounds/getClientTraffics/'.rawurlencode($email)));

        $body = $this->decode($response, 'دریافت وضعیت کلاینت از x-ui ناموفق بود.');
        $traffic = $body['obj'] ?? null;

        if (! is_array($traffic)) {
            throw new RuntimeException('اطلاعات ترافیک کلاینت در x-ui پیدا نشد.');
        }

        return $traffic;
    }

    public function syncClient(Client $client, ?Setting $setting = null): Client
    {
        $setting ??= Setting::current();
        $traffic = $this->getClientTraffic($setting, $client->email);
        $usedBytes = (int) ($traffic['up'] ?? 0) + (int) ($traffic['down'] ?? 0);
        $totalBytes = (int) ($traffic['total'] ?? $client->total_bytes);
        $expiryTime = (int) ($traffic['expiryTime'] ?? $client->xui_expiry_time ?? 0);
        $remainingBytes = max(0, $totalBytes - $usedBytes);
        $remainingSeconds = $this->remainingSeconds($expiryTime);
        $enabled = (bool) ($traffic['enable'] ?? true);

        $client->update([
            'total_bytes' => $totalBytes > 0 ? $totalBytes : $client->total_bytes,
            'used_traffic_bytes' => $usedBytes,
            'remaining_traffic_bytes' => $remainingBytes,
            'xui_expiry_time' => $expiryTime,
            'remaining_seconds' => $remainingSeconds,
            'status' => $this->statusFromTraffic($enabled, $remainingBytes, $remainingSeconds, $totalBytes),
            'synced_at' => now(),
            'xui_response' => array_merge($client->xui_response ?? [], ['traffic' => $traffic]),
        ]);

        return $client->refresh();
    }

    public function isMissingClientTrafficException(RuntimeException $exception): bool
    {
        $message = strtolower($exception->getMessage());

        return str_contains($message, 'inbound not found for email')
            || str_contains($message, 'client not found')
            || str_contains($message, 'email not found');
    }

    public function makeClientLinks(Setting $setting, array $inbound, array $client): array
    {
        $streamSettings = $this->jsonField($inbound, 'streamSettings');
        $protocolSettings = $this->jsonField($inbound, 'settings');
        $network = $streamSettings['network'] ?? 'tcp';
        $security = $streamSettings['security'] ?? 'none';
        $port = (int) ($streamSettings['externalProxy'][0]['port'] ?? $inbound['port'] ?? 0);
        $protocol = $inbound['protocol'] ?? 'vless';
        $address = $this->clientAddress($setting, $streamSettings);
        $query = [
            'type' => $network,
            'encryption' => $protocolSettings['encryption'] ?? 'none',
        ];

        if ($network === 'ws') {
            $path = $streamSettings['wsSettings']['path'] ?? '/';
            $host = $streamSettings['wsSettings']['host'] ?? null;
            $query['path'] = $path;

            if ($host) {
                $query['host'] = $host;
            }
        }

        $query['security'] = $security;

        if (! empty($client['flow'])) {
            $query['flow'] = $client['flow'];
        }

        $fragment = rawurlencode(($inbound['remark'] ?? 'CheetaNet').'-'.$client['email']);
        $configLink = sprintf(
            '%s://%s@%s:%d?%s#%s',
            $protocol,
            $client['uuid'],
            $address,
            $port,
            http_build_query($query, '', '&', PHP_QUERY_RFC3986),
            $fragment
        );

        return [
            'config_link' => $configLink,
            'subscription_link' => rtrim($setting->subscription_base_url ?: 'https://sub.cheeta.site:2096/sub', '/').'/'.$client['sub_id'],
        ];
    }

    private function login(Setting $setting): CookieJar
    {
        if (! $setting->xui_base_url || ! $setting->xui_username || ! $setting->xui_password) {
            throw new RuntimeException('تنظیمات اتصال x-ui کامل نیست.');
        }

        $jar = new CookieJar();

        $response = Http::withOptions(['cookies' => $jar])
            ->acceptJson()
            ->asForm()
            ->post($this->url($setting, '/dash/login'), [
                'username' => $setting->xui_username,
                'password' => $setting->xui_password,
            ]);

        $this->decode($response, 'ورود به x-ui ناموفق بود.');

        if ($jar->count() === 0) {
            throw new RuntimeException('ورود به x-ui انجام شد اما کوکی 3x-ui دریافت نشد.');
        }

        return $jar;
    }

    private function url(Setting $setting, string $path): string
    {
        return rtrim((string) $setting->xui_base_url, '/').$path;
    }

    private function clientAddress(Setting $setting, array $streamSettings): string
    {
        $externalProxy = $streamSettings['externalProxy'][0]['dest'] ?? null;

        if ($externalProxy) {
            return $externalProxy;
        }

        $host = parse_url((string) $setting->xui_base_url, PHP_URL_HOST);

        if (! $host) {
            throw new RuntimeException('آدرس عمومی ساخت سرویس قابل تشخیص نیست.');
        }

        return $host;
    }

    private function jsonField(array $inbound, string $field): array
    {
        $value = $inbound[$field] ?? [];

        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode((string) $value, true);

        return is_array($decoded) ? $decoded : [];
    }

    private function decode(Response $response, string $message): array
    {
        if ($response->failed()) {
            throw new RuntimeException($message);
        }

        $body = $response->json();

        if (! is_array($body)) {
            return ['raw' => $response->body()];
        }

        if (($body['success'] ?? true) === false) {
            throw new RuntimeException($body['msg'] ?? $message);
        }

        return $body;
    }

    private function remainingSeconds(int $expiryTime): ?int
    {
        if ($expiryTime === 0) {
            return null;
        }

        if ($expiryTime < 0) {
            return max(0, (int) floor(abs($expiryTime) / 1000));
        }

        return max(0, (int) floor($expiryTime / 1000) - now()->timestamp);
    }

    private function statusFromTraffic(bool $enabled, int $remainingBytes, ?int $remainingSeconds, int $totalBytes): string
    {
        if (! $enabled) {
            return 'disabled';
        }

        if ($remainingSeconds === 0) {
            return 'expired';
        }

        if ($totalBytes > 0 && $remainingBytes <= 0) {
            return 'limited';
        }

        return 'active';
    }
}
