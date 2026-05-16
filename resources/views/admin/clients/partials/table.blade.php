<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Email</th>
                <th>فروشنده</th>
                <th>حجم</th>
                <th>باقی‌مانده حجم</th>
                <th>باقی‌مانده زمان</th>
                <th>هزینه</th>
                <th>وضعیت</th>
                <th>آخرین بروزرسانی</th>
                <th>تاریخ</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clients as $client)
                <tr>
                    <td class="font-mono">{{ $client->email }}</td>
                    <td>{{ $client->seller?->name }}</td>
                    <td>{{ $client->total_gb }} GB</td>
                    <td>{{ $client->remainingTrafficLabel() }}</td>
                    <td>{{ $client->remainingTimeLabel() }}</td>
                    <td>{{ number_format($client->cost) }}</td>
                    <td><span class="badge {{ $client->status === 'active' ? 'badge-success' : '' }}">{{ $client->status }}</span></td>
                    <td>{{ $client->synced_at?->format('Y-m-d H:i') ?? 'هنوز بروزرسانی نشده' }}</td>
                    <td>{{ $client->created_at?->format('Y-m-d H:i') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="9" class="text-center text-slate-500">موردی وجود ندارد.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
