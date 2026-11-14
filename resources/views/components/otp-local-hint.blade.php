{{-- Chỉ hiện ở máy local khi mail không gửi thật (MAIL_MAILER=log): chỉ chỗ lấy mã để test. --}}
@if (app()->isLocal() && config('mail.default') === 'log')
    <p class="mb-4 rounded-xl border border-dashed border-sky-300 bg-sky-50 px-4 py-2.5 text-xs text-sky-800">
        Đang chạy local với <code class="font-mono">MAIL_MAILER=log</code>: mail không gửi thật.
        Mã nằm ở cuối file <code class="font-mono">storage/logs/laravel.log</code>
        hoặc trang Admin &gt; Email đã gửi.
    </p>
@endif
