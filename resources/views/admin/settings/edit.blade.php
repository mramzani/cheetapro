<x-layouts.app title="تنظیمات">
    <div class="mx-auto max-w-2xl">
        <div class="panel-card">
        <h1 class="page-title mb-2">تنظیمات</h1>
        <p class="page-subtitle mb-6">اطلاعات اتصال به x-ui، لینک subscription و قیمت‌گذاری را مدیریت کنید.</p>
        <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-4">
            @csrf
            @method('PUT')
            <input name="xui_base_url" value="{{ old('xui_base_url', $setting->xui_base_url) }}" placeholder="https://dash.cheeta.site:2053" class="form-input" required>
            <input name="subscription_base_url" value="{{ old('subscription_base_url', $setting->subscription_base_url) }}" placeholder="https://sub.cheeta.site:2096/sub" class="form-input" required>
            <div class="grid gap-4 sm:grid-cols-2">
                <input name="xui_username" value="{{ old('xui_username', $setting->xui_username) }}" placeholder="نام کاربری x-ui" class="form-input" required>
                <input name="xui_password" type="password" placeholder="رمز x-ui؛ در صورت عدم تغییر خالی بماند" class="form-input">
            </div>
            <div class="grid gap-4 sm:grid-cols-3">
                <input name="xui_inbound_id" type="number" min="1" value="{{ old('xui_inbound_id', $setting->xui_inbound_id) }}" placeholder="Inbound ID" class="form-input" required>
                <input name="price_per_gb" type="number" min="0" value="{{ old('price_per_gb', $setting->price_per_gb) }}" placeholder="قیمت هر گیگ" class="form-input" required>
                <input name="default_expiry_days" type="number" min="1" value="{{ old('default_expiry_days', $setting->default_expiry_days) }}" placeholder="مدت پیش‌فرض روز" class="form-input" required>
            </div>
            <button class="btn-primary w-full sm:w-auto">ذخیره تنظیمات</button>
        </form>
        <form method="POST" action="{{ route('admin.settings.test') }}" class="mt-4">
            @csrf
            <button class="btn-secondary w-full sm:w-auto">تست اتصال و دریافت inboundها</button>
        </form>
        </div>
    </div>
</x-layouts.app>
