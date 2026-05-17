<div class="table-wrap">
    <table class="data-table">
        <thead>
            <tr>
                <th>Email</th>
                <th>مصرف و اعتبار</th>
                <th>وضعیت</th>
                <th>آخرین بروزرسانی</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clients as $client)
                @php
                    $usedBytes = $client->used_traffic_bytes ?? 0;
                    $remainingBytes = $client->remaining_traffic_bytes;
                    $totalBytes = max(1, $client->total_bytes);
                    $usedPercent = $remainingBytes === null
                        ? 0
                        : min(100, max(0, round(($usedBytes / $totalBytes) * 100)));
                    $remainingPercent = $remainingBytes === null ? null : 100 - $usedPercent;
                @endphp
                <tr>
                    <td class="font-mono">{{ $client->email }}</td>
                    <td class="min-w-64">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between gap-3 text-xs font-bold text-slate-500">
                                <span>{{ $client->total_gb }} GB</span>
                                <span>{{ $remainingBytes === null ? 'حجم نامشخص' : $remainingPercent.'٪ باقی‌مانده' }}</span>
                            </div>
                            <div class="h-3 overflow-hidden rounded-full bg-slate-100 ring-1 ring-slate-200">
                                <div class="h-full rounded-full {{ $usedPercent >= 90 ? 'bg-rose-500' : ($usedPercent >= 70 ? 'bg-amber-500' : 'bg-emerald-500') }}" style="width: {{ $usedPercent }}%"></div>
                            </div>
                            <div class="flex items-center justify-between gap-3 text-xs text-slate-500">
                                <span>باقی‌مانده: {{ $client->remainingTrafficLabel() }}</span>
                                <span>زمان: {{ $client->remainingTimeLabel() }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <form method="POST" action="{{ route('seller.clients.toggle', $client) }}">
                            @csrf
                            @method('PATCH')
                            <button class="group inline-flex items-center gap-2 rounded-full bg-slate-100 p-1 text-xs font-bold text-slate-600 ring-1 ring-slate-200 transition hover:bg-slate-200" title="{{ $client->status === 'disabled' ? 'فعال‌سازی' : 'غیرفعال‌سازی' }}">
                                <span class="relative h-6 w-11 rounded-full transition {{ $client->status === 'active' ? 'bg-emerald-500' : 'bg-slate-300' }}">
                                    <span class="absolute top-1 h-4 w-4 rounded-full bg-white shadow transition {{ $client->status === 'active' ? 'right-6' : 'right-1' }}"></span>
                                </span>
                                <span>{{ $client->status === 'active' ? 'فعال' : 'غیرفعال' }}</span>
                            </button>
                        </form>
                    </td>
                    <td>{{ $client->synced_at?->format('Y-m-d H:i') ?? 'هنوز بروزرسانی نشده' }}</td>
                    <td>
                        <a class="btn-secondary min-h-0 px-3 py-1.5" href="{{ route('seller.clients.show', $client) }}">مشاهده</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-slate-500">کلاینتی ساخته نشده است.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
