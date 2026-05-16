<x-layouts.app title="شارژ کیف پول">
    <div class="mx-auto max-w-xl">
        <div class="panel-card">
        <h1 class="page-title mb-2">شارژ کیف پول</h1>
        <p class="page-subtitle mb-6">{{ $seller->name }} - موجودی فعلی: {{ number_format($seller->wallet?->balance ?? 0) }}</p>
        <form method="POST" action="{{ route('admin.wallets.charge.store', $seller) }}" class="space-y-4">
            @csrf
            <input name="amount" type="number" min="1" value="{{ old('amount') }}" placeholder="مبلغ شارژ" class="form-input" required>
            <input name="description" value="{{ old('description') }}" placeholder="توضیح اختیاری" class="form-input">
            <button class="btn-success w-full sm:w-auto">شارژ</button>
        </form>
        </div>
    </div>
</x-layouts.app>
