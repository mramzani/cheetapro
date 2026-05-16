<x-layouts.app title="ورود مدیر">
    <div class="mx-auto max-w-md">
        <div class="panel-card">
        <h1 class="page-title mb-2">ورود مدیر</h1>
        <p class="page-subtitle mb-6">برای مدیریت فروشنده‌ها و تنظیمات x-ui وارد شوید.</p>
        <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-4">
            @csrf
            <label class="block">
                <span class="mb-2 block text-sm font-bold text-slate-700">ایمیل</span>
                <input name="email" type="email" value="{{ old('email') }}" class="form-input" required>
            </label>
            <label class="block">
                <span class="mb-2 block text-sm font-bold text-slate-700">رمز عبور</span>
                <input name="password" type="password" class="form-input" required>
            </label>
            <button class="btn-primary w-full">ورود</button>
        </form>
        </div>
    </div>
</x-layouts.app>
