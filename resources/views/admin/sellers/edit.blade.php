<x-layouts.app title="ویرایش فروشنده">
    <div class="mx-auto max-w-xl">
        <div class="panel-card">
        <h1 class="page-title mb-2">ویرایش فروشنده</h1>
        <p class="page-subtitle mb-6">اطلاعات ورود و وضعیت حساب فروشنده را تغییر دهید.</p>
        <form method="POST" action="{{ route('admin.sellers.update', $seller) }}" class="space-y-4">
            @csrf
            @method('PUT')
            <input name="name" value="{{ old('name', $seller->name) }}" class="form-input" required>
            <input name="username" value="{{ old('username', $seller->username) }}" class="form-input" required>
            <input name="password" type="password" placeholder="رمز جدید؛ در صورت عدم تغییر خالی بماند" class="form-input">
            <input name="is_active" value="0" type="hidden">
            <label class="flex items-center gap-2 rounded-2xl bg-slate-50 p-3 text-sm font-medium">
                <input name="is_active" value="1" type="checkbox" class="size-4 rounded border-slate-300" @checked(old('is_active', $seller->is_active))>
                <span>فعال</span>
            </label>
            <button class="btn-primary w-full sm:w-auto">ذخیره</button>
        </form>
        </div>
    </div>
</x-layouts.app>
