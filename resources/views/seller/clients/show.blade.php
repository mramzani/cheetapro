<x-layouts.app title="نتیجه ساخت کلاینت">
    <div class="panel-card">
        <div class="mb-6">
            <h1 class="page-title">کلاینت آماده است</h1>
            <p class="page-subtitle">این دو لینک مهم‌ترین خروجی برای تحویل به مشتری هستند.</p>
        </div>
        <div class="mb-6 space-y-4">
            <div>
                <div class="mb-2 text-sm font-bold text-slate-700">لینک سرویس</div>
                <div class="grid gap-2 sm:grid-cols-[1fr_auto]">
                    <textarea id="config-link" class="form-input min-h-28 font-mono text-xs leading-6 sm:text-sm" readonly>{{ $client->config_link }}</textarea>
                    <button type="button" data-copy-target="config-link" class="btn-primary self-start">کپی</button>
                </div>
            </div>
            <div>
                <div class="mb-2 text-sm font-bold text-slate-700">لینک Subscription</div>
                <div class="grid gap-2 sm:grid-cols-[1fr_auto]">
                    <input id="subscription-link" value="{{ $client->subscription_link }}" class="form-input font-mono text-xs sm:text-sm" readonly>
                    <button type="button" data-copy-target="subscription-link" class="btn-primary">کپی</button>
                </div>
            </div>
        </div>
        <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <div class="rounded-2xl bg-slate-50 p-4">
                <div class="text-xs font-bold text-slate-500">Email</div>
                <div class="mt-1 font-mono text-sm">{{ $client->email }}</div>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4">
                <div class="text-xs font-bold text-slate-500">Subscription Key</div>
                <div class="mt-1 font-mono text-sm">{{ $client->sub_id }}</div>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4">
                <div class="text-xs font-bold text-slate-500">UUID</div>
                <div class="mt-1 truncate font-mono text-sm">{{ $client->uuid }}</div>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4">
                <div class="text-xs font-bold text-slate-500">Inbound ID</div>
                <div>{{ $client->inbound_id }}</div>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4">
                <div class="text-xs font-bold text-slate-500">حجم</div>
                <div>{{ $client->total_gb }} GB</div>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4">
                <div class="text-xs font-bold text-slate-500">هزینه</div>
                <div>{{ number_format($client->cost) }}</div>
            </div>
            <div class="rounded-2xl bg-slate-50 p-4">
                <div class="text-xs font-bold text-slate-500">وضعیت</div>
                <div><span class="badge badge-success">{{ $client->status }}</span></div>
            </div>
        </div>
        <a href="{{ route('seller.clients.create') }}" class="btn-primary mt-6">ساخت کلاینت بعدی</a>
    </div>
    <script>
        const copyValue = async (input) => {
            if (navigator.clipboard && window.isSecureContext) {
                await navigator.clipboard.writeText(input.value);
                return;
            }

            input.focus();
            input.select();
            input.setSelectionRange(0, input.value.length);
            document.execCommand('copy');
        };

        document.querySelectorAll('[data-copy-target]').forEach((button) => {
            button.addEventListener('click', async () => {
                const input = document.getElementById(button.dataset.copyTarget);
                const originalText = button.textContent;

                try {
                    await copyValue(input);
                    button.textContent = 'کپی شد';
                } catch (error) {
                    button.textContent = 'کپی نشد';
                }

                setTimeout(() => button.textContent = originalText, 1500);
            });
        });
    </script>
</x-layouts.app>
