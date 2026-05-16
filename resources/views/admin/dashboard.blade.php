<x-layouts.app title="داشبورد مدیر">
    <div class="mb-6">
        <h1 class="page-title">داشبورد مدیر</h1>
        <p class="page-subtitle">نمای سریع وضعیت فروشندگان، کیف پول‌ها و کلاینت‌های ساخته‌شده.</p>
    </div>
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        <div class="stat-card">
            <div class="stat-label">فروشنده‌ها</div>
            <div class="stat-value">{{ $sellersCount }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">کلاینت‌ها</div>
            <div class="stat-value">{{ $clientsCount }}</div>
        </div>
        <div class="stat-card sm:col-span-2 lg:col-span-1">
            <div class="stat-label">مجموع موجودی کیف پول‌ها</div>
            <div class="stat-value">{{ number_format($walletsBalance) }}</div>
        </div>
    </div>

    <div class="panel-card mt-8">
        <div class="mobile-stack mb-4">
            <h2 class="text-lg font-black">کلاینت‌های اخیر</h2>
            <a href="{{ route('admin.clients.index') }}" class="btn-secondary">مشاهده همه</a>
        </div>
        @include('admin.clients.partials.table', ['clients' => $recentClients])
    </div>
</x-layouts.app>
