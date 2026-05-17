<x-layouts.app title="داشبورد فروشنده">
    <div class="mb-6">
        <h1 class="page-title">سلام {{ $seller->name }}</h1>
        <p class="page-subtitle">از اینجا موجودی کیف پول، کلاینت‌ها و ساخت سرویس جدید را مدیریت کن.</p>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="stat-card">
            <div class="stat-label">موجودی کیف پول</div>
            <div class="stat-value">{{ number_format($seller->wallet?->balance ?? 0) }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">قیمت هر گیگ</div>
            <div class="stat-value">نامشخص</div>
        </div>
        <div class="stat-card sm:col-span-2 lg:col-span-1">
            <div class="stat-label">کلاینت‌های شما</div>
            <div class="stat-value">{{ $seller->clients()->count() }}</div>
        </div>
    </div>

    <div class="mt-8 grid gap-3 sm:flex">
        <a href="{{ route('seller.clients.create') }}" class="btn-primary">ساخت کلاینت جدید</a>
        <a href="{{ route('seller.clients.index') }}" class="btn-secondary">مشاهده کلاینت‌ها</a>
    </div>

    <div class="panel-card mt-8">
        <h2 class="mb-4 text-lg font-black">کلاینت‌های اخیر</h2>
        @include('seller.clients.partials.table', ['clients' => $recentClients])
    </div>
</x-layouts.app>
