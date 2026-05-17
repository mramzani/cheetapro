<x-layouts.app title="ورود فروشنده">
    <div class="mx-auto max-w-md">
        <div class="panel-card">
        <h1 class="page-title mb-2">ورود فروشنده</h1>
        <p class="page-subtitle mb-6">برای ساخت و تحویل سرویس به مشتری وارد شوید.</p>
        <form method="POST" action="{{ route('seller.login.store') }}" class="space-y-4">
            @csrf
            <input name="username" value="{{ old('username') }}" placeholder="نام کاربری" class="form-input" required>
            <input name="password" type="password" placeholder="رمز عبور" class="form-input" required>
            <button class="btn-primary w-full">ورود</button>
        </form>
        </div>
    </div>
</x-layouts.app>
