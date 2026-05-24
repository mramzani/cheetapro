<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Cheeta Panel' }}</title>
    <link rel="stylesheet" href="https://cdn.myrinofy.ir/css/fonts/Estedad/Estedad.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="app-shell">
        <nav class="app-navbar">
            <div class="app-container flex flex-col gap-3 py-3 sm:flex-row sm:items-center sm:justify-between">
                <a href="{{ auth('seller')->check() ? route('seller.dashboard') : route('admin.dashboard') }}" class="flex items-center gap-3">
                    <span class="brand-mark">C</span>
                    <span>
                        <span class="block text-base font-black text-slate-950">Cheeta Panel</span>
                        <span class="block text-xs text-slate-500">مدیریت فروش و سرویس</span>
                    </span>
                </a>
                <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:justify-end sm:overflow-visible sm:pb-0">
                    @if(auth('seller')->check())
                        <a class="nav-link" href="{{ route('seller.dashboard') }}">داشبورد</a>
                        <a class="nav-link" href="{{ route('seller.clients.create') }}">ساخت کلاینت</a>
                        <a class="nav-link" href="{{ route('seller.clients.index') }}">کلاینت‌ها</a>
                        <a class="nav-link" href="{{ route('seller.wallets.transactions') }}">تراکنش‌ها</a>
                        <form method="POST" action="{{ route('seller.logout') }}">
                            @csrf
                            <button class="nav-link text-rose-600">خروج</button>
                        </form>
                    @else
                        @if(auth()->check())
                            <a class="nav-link" href="{{ route('admin.dashboard') }}">داشبورد</a>
                            <a class="nav-link" href="{{ route('admin.sellers.index') }}">فروشنده‌ها</a>
                            <a class="nav-link" href="{{ route('admin.clients.index') }}">کلاینت‌ها</a>
                            <a class="nav-link" href="{{ route('admin.settings.edit') }}">تنظیمات</a>
                            <form method="POST" action="{{ route('admin.logout') }}">
                                @csrf
                                <button class="nav-link text-rose-600">خروج</button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        </nav>

        <main class="app-container py-6 sm:py-10">
            @if(session('status'))
                <div class="alert-success">{{ session('status') }}</div>
            @endif

            @if($errors->any())
                <div class="alert-error">
                    <ul class="list-inside list-disc">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>
</body>
</html>
