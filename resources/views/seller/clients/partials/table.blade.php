<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Email</th>
                <th>UUID</th>
                <th>حجم</th>
                <th>باقی‌مانده حجم</th>
                <th>باقی‌مانده زمان</th>
                <th>هزینه</th>
                <th>آخرین بروزرسانی</th>
                <th>تاریخ</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clients as $client)
                <tr>
                    <td class="font-mono">{{ $client->email }}</td>
                    <td class="max-w-44 truncate font-mono text-xs">{{ $client->uuid }}</td>
                    <td>{{ $client->total_gb }} GB</td>
                    <td>{{ $client->remainingTrafficLabel() }}</td>
                    <td>{{ $client->remainingTimeLabel() }}</td>
                    <td>{{ number_format($client->cost) }}</td>
                    <td>{{ $client->synced_at?->format('Y-m-d H:i') ?? 'هنوز بروزرسانی نشده' }}</td>
                    <td>{{ $client->created_at?->format('Y-m-d H:i') }}</td>
                    <td><a class="btn-secondary min-h-0 px-3 py-1.5" href="{{ route('seller.clients.show', $client) }}">مشاهده</a></td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-slate-500">کلاینتی ساخته نشده است.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
