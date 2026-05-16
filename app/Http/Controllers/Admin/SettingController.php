<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingRequest;
use App\Models\Setting;
use App\Services\XuiClient;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class SettingController extends Controller
{
    public function edit(): View
    {
        return view('admin.settings.edit', ['setting' => Setting::current()]);
    }

    public function update(UpdateSettingRequest $request): RedirectResponse
    {
        $setting = Setting::current();
        $data = $request->validated();

        if (blank($data['xui_password'])) {
            unset($data['xui_password']);
        }

        $setting->update($data);

        return back()->with('status', 'تنظیمات ذخیره شد.');
    }

    public function test(XuiClient $xuiClient): RedirectResponse
    {
        try {
            $inbounds = $xuiClient->listInbounds(Setting::current());
        } catch (RuntimeException $exception) {
            return back()->withErrors(['xui' => $exception->getMessage()]);
        }

        return back()->with('status', 'اتصال موفق بود. تعداد inboundها: '.count($inbounds));
    }
}
