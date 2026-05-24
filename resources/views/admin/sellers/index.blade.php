<x-layouts.app title="فروشنده‌ها">
    <div class="mb-6">
        <h1 class="page-title">فروشنده‌ها</h1>
        <p class="page-subtitle">ساخت فروشنده جدید، مدیریت وضعیت حساب و شارژ کیف پول‌ها.</p>
    </div>
    <div class="grid gap-6 lg:grid-cols-3">
        <div class="panel-card">
            <h2 class="mb-4 text-lg font-black">فروشنده جدید</h2>
            <form method="POST" action="{{ route('admin.sellers.store') }}" class="space-y-4">
                @csrf
                <input name="name" value="{{ old('name') }}" placeholder="نام" class="form-input" required>
                <input name="username" value="{{ old('username') }}" placeholder="نام کاربری" class="form-input" required>
                <input name="password" type="password" placeholder="رمز عبور" class="form-input" required>
                <div class="grid gap-4 sm:grid-cols-2">
                    <input name="price_per_gb" type="number" min="0" value="{{ old('price_per_gb') }}" placeholder="قیمت اختصاصی هر گیگ (اختیاری)" class="form-input">
                    <input name="xui_inbound_id" type="number" min="1" value="{{ old('xui_inbound_id') }}" placeholder="Inbound اختصاصی (اختیاری)" class="form-input">
                </div>
                <input name="is_active" value="0" type="hidden">
                <label class="flex items-center gap-2 rounded-2xl bg-slate-50 p-3 text-sm font-medium">
                    <input name="is_active" value="1" type="checkbox" checked class="size-4 rounded border-slate-300">
                    <span>حساب فعال باشد</span>
                </label>
                <button class="btn-primary w-full">ایجاد فروشنده</button>
            </form>
        </div>

        <div class="panel-card lg:col-span-2">
            <h2 class="mb-4 text-lg font-black">لیست فروشنده‌ها</h2>
            <div class="space-y-3 sm:hidden">
                @forelse($sellers as $seller)
                    <div class="rounded-2xl border border-slate-100 bg-slate-50/70 p-4">
                        <div class="mb-3 flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <h3 class="truncate text-sm font-black text-slate-950">{{ $seller->name }}</h3>
                                <p class="mt-1 truncate font-mono text-xs text-slate-500">{{ $seller->username }}</p>
                            </div>
                            <span class="badge shrink-0 {{ $seller->is_active ? 'badge-success' : '' }}">{{ $seller->is_active ? 'فعال' : 'غیرفعال' }}</span>
                        </div>

                        <div class="mb-4 flex items-center justify-between gap-3 text-sm">
                            <span class="text-slate-500">موجودی</span>
                            <span class="font-bold text-slate-900">{{ number_format($seller->wallet?->balance ?? 0) }}</span>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <a class="btn-secondary min-h-0 px-3 py-2" href="{{ route('admin.sellers.edit', $seller) }}">ویرایش</a>
                            <a class="btn-success min-h-0 px-3 py-2" href="{{ route('admin.wallets.charge', $seller) }}">شارژ</a>
                            <a class="btn-secondary col-span-2 min-h-0 px-3 py-2" href="{{ route('admin.wallets.transactions', $seller) }}">تراکنش‌ها</a>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4 text-center text-sm text-slate-500">فروشنده‌ای ثبت نشده است.</div>
                @endforelse
            </div>
            <div class="table-wrap hidden sm:block">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>نام</th>
                            <th>نام کاربری</th>
                            <th>موجودی</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sellers as $seller)
                            <tr>
                                <td>{{ $seller->name }}</td>
                                <td class="font-mono">{{ $seller->username }}</td>
                                <td>{{ number_format($seller->wallet?->balance ?? 0) }}</td>
                                <td><span class="badge {{ $seller->is_active ? 'badge-success' : '' }}">{{ $seller->is_active ? 'فعال' : 'غیرفعال' }}</span></td>
                                <td class="flex flex-wrap gap-2">
                                    <a class="btn-secondary min-h-0 px-3 py-1.5" href="{{ route('admin.sellers.edit', $seller) }}">ویرایش</a>
                                    <a class="btn-success min-h-0 px-3 py-1.5" href="{{ route('admin.wallets.charge', $seller) }}">شارژ</a>
                                    <a class="btn-secondary min-h-0 px-3 py-1.5" href="{{ route('admin.wallets.transactions', $seller) }}">تراکنش‌ها</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-slate-500">فروشنده‌ای ثبت نشده است.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $sellers->links() }}</div>
        </div>
    </div>
</x-layouts.app>
