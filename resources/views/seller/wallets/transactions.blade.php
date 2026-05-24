<x-layouts.app title="تراکنش‌های کیف پول">
    <div class="panel-card">
        <div class="mobile-stack mb-4">
            <div>
                <h1 class="page-title">تراکنش‌های کیف پول</h1>
                <p class="page-subtitle">سابقه شارژ و کسر از کیف پولت را اینجا ببین. موجودی فعلی: {{ number_format($seller->wallet?->balance ?? 0) }}</p>
            </div>
            <a href="{{ route('seller.clients.index') }}" class="btn-secondary">بازگشت به کلاینت‌ها</a>
        </div>

        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>زمان</th>
                        <th>نوع</th>
                        <th>مبلغ</th>
                        <th>موجودی قبل</th>
                        <th>موجودی بعد</th>
                        <th>ثبت‌کننده</th>
                        <th>توضیحات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                        @php
                            $delta = $transaction->balance_after - $transaction->balance_before;
                            $isCharge = $transaction->type === \App\Models\WalletTransaction::TYPE_CHARGE;
                            $typeLabel = match ($transaction->type) {
                                \App\Models\WalletTransaction::TYPE_CHARGE => 'شارژ کیف پول',
                                \App\Models\WalletTransaction::TYPE_CLIENT_PURCHASE => 'کسر بابت ساخت کلاینت',
                                default => $transaction->type,
                            };
                        @endphp
                        <tr>
                            <td>{{ $transaction->created_at->format('Y/m/d H:i') }}</td>
                            <td>
                                <span class="badge {{ $isCharge ? 'badge-success' : '' }}">{{ $typeLabel }}</span>
                            </td>
                            <td class="font-bold {{ $delta >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $delta >= 0 ? '+' : '-' }}{{ number_format(abs($transaction->amount)) }}
                            </td>
                            <td>{{ number_format($transaction->balance_before) }}</td>
                            <td>{{ number_format($transaction->balance_after) }}</td>
                            <td>{{ $transaction->createdBy?->name ?? 'سیستم / فروشنده' }}</td>
                            <td class="whitespace-normal">{{ $transaction->description ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-slate-500">هنوز تراکنشی برای کیف پول شما ثبت نشده است.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $transactions->links() }}</div>
    </div>
</x-layouts.app>
