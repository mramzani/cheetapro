<x-layouts.app title="کلاینت‌ها">
    <div class="panel-card">
        <div class="mobile-stack mb-4">
            <div>
                <h1 class="page-title">کلاینت‌ها</h1>
            <p class="page-subtitle">مشاهده، جستجو و بروزرسانی وضعیت کلاینت‌های ساخته‌شده.</p>
            </div>
            <form method="GET" class="grid gap-2 sm:flex">
                <input name="q" value="{{ $search }}" placeholder="جستجو email یا uuid" class="form-input min-w-60">
                <select name="seller_id" class="form-input min-w-52">
                    <option value="">همه فروشنده‌ها</option>
                    @foreach($sellers as $seller)
                        <option value="{{ $seller->id }}" @selected($selectedSellerId === $seller->id)>{{ $seller->name }}</option>
                    @endforeach
                </select>
                <button class="btn-secondary">فیلتر</button>
            </form>
        </div>
        <form method="POST" action="{{ route('admin.clients.sync') }}" class="mb-4">
            @csrf
            <input type="hidden" name="q" value="{{ $search }}">
            <input type="hidden" name="seller_id" value="{{ $selectedSellerId }}">
            <button class="btn-primary w-full sm:w-auto">بروزرسانی حجم و زمان باقی‌مانده</button>
        </form>
        @include('admin.clients.partials.table', ['clients' => $clients])
        <div class="mt-4">{{ $clients->links() }}</div>
    </div>
</x-layouts.app>
