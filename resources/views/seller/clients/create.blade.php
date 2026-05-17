<x-layouts.app title="ساخت کلاینت">
    <div class="mb-6">
        <h1 class="page-title">ساخت کلاینت</h1>
        <p class="page-subtitle">حجم و مدت را وارد کن؛ بعد از ساخت، لینک سرویس و سابسکریپشن آماده کپی نمایش داده می‌شود.</p>
    </div>
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="stat-card">
            <h2 class="mb-6 text-lg font-black">کیف پول</h2>
            <p class="stat-label">موجودی فعلی</p>
            <p class="stat-value">{{ number_format($seller->wallet?->balance ?? 0) }}</p>
            {{-- <div class="mt-6 rounded-2xl bg-slate-50 p-4 text-sm text-slate-600">
                هزینه نهایی با نرخ {{ number_format($pricePerGb) }} برای هر گیگ از کیف پول کسر می‌شود.
            </div> --}}
        </div>

        <div class="panel-card lg:col-span-2">
            <h2 class="mb-6 text-xl font-black">مشخصات کلاینت x-ui</h2>
            <form method="POST" action="{{ route('seller.clients.store') }}" class="space-y-4">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <input name="total_gb" type="number" min="1" value="{{ old('total_gb', 1) }}" placeholder="حجم به گیگ" class="form-input" required>
                    <input name="expiry_days" type="number" min="1" value="{{ old('expiry_days', $setting->default_expiry_days) }}" placeholder="مدت به روز" class="form-input" required>
                </div>
                <input name="tg_id" value="{{ old('tg_id') }}" placeholder="تلگرام آیدی اختیاری" class="form-input">
                <textarea name="comment" rows="4" placeholder="یادداشت اختیاری" class="form-input">{{ old('comment') }}</textarea>
                <button class="btn-primary w-full sm:w-auto">ساخت و دریافت لینک‌ها</button>
            </form>
        </div>
    </div>
</x-layouts.app>
