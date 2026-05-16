<x-layouts.app title="کلاینت‌های من">
    <div class="panel-card">
        <div class="mobile-stack mb-4">
            <div>
                <h1 class="page-title">کلاینت‌های من</h1>
                <p class="page-subtitle">لینک‌ها، حجم باقی‌مانده و زمان باقی‌مانده کلاینت‌ها را اینجا ببین.</p>
            </div>
            <a href="{{ route('seller.clients.create') }}" class="btn-primary">ساخت کلاینت</a>
        </div>
        <div class="mb-4 grid gap-3 lg:grid-cols-[1fr_auto]">
            <form method="GET" class="grid gap-2 sm:flex">
                <input name="q" value="{{ $search }}" placeholder="جستجو email یا uuid" class="form-input min-w-60">
                <button class="btn-secondary">جستجو</button>
            </form>
            <form method="POST" action="{{ route('seller.clients.sync') }}">
                @csrf
                <input type="hidden" name="q" value="{{ $search }}">
                <button class="btn-primary w-full">بروزرسانی حجم و زمان</button>
            </form>
        </div>
        @include('seller.clients.partials.table', ['clients' => $clients])
        <div class="mt-4">{{ $clients->links() }}</div>
    </div>
</x-layouts.app>
