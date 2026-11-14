@php
    /** @var \App\Models\Certificate $certificate */
    $appName = config('app.name', 'EduLearn');
    $pageTitle = 'Chứng chỉ '.$certificate->course_title.' - '.$certificate->recipient_name;
    $shareUrl = $certificate->url();
@endphp
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $certificate->recipient_name }} đã hoàn thành khóa học {{ $certificate->course_title }} trên {{ $appName }}.">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Chứng chỉ hoàn thành khóa học: {{ $certificate->course_title }}">
    <meta property="og:description" content="{{ $certificate->recipient_name }} đã hoàn thành khóa học trên {{ $appName }}. Mã chứng chỉ {{ $certificate->code }}.">
    <meta property="og:url" content="{{ $shareUrl }}">

    @include('certificates.partials.styles')

    <style>
        :root { color-scheme: light; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Be Vietnam Pro', system-ui, sans-serif;
            color: #1C2B29;
            background: #E9EFEE radial-gradient(circle at 20% 0%, #F6FBFA 0%, rgba(246, 251, 250, 0) 55%);
        }
        a { color: inherit; }
        .page { width: 100%; max-width: 1180px; margin: 0 auto; padding: 20px 16px 48px; }

        .toolbar { display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 18px; }
        .toolbar__back { display: inline-flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 600; text-decoration: none; color: #0B4F4A; }
        .toolbar__back:hover { text-decoration: underline; }
        .toolbar__actions { display: flex; flex-wrap: wrap; gap: 8px; }

        .btn { display: inline-flex; align-items: center; gap: 8px; height: 40px; padding: 0 16px; border-radius: 10px; border: 1px solid #C9D6D4; background: #fff; color: #1C2B29; font: 600 14px/1 'Be Vietnam Pro', system-ui, sans-serif; text-decoration: none; cursor: pointer; transition: background-color .15s, border-color .15s, transform .15s; }
        .btn:hover { background: #F3F7F6; border-color: #A9BEBB; }
        .btn:active { transform: translateY(1px); }
        .btn:focus-visible { outline: 3px solid rgba(1, 157, 145, .35); outline-offset: 2px; }
        .btn svg { width: 18px; height: 18px; flex: none; }
        .btn--primary { background: #019D91; border-color: #019D91; color: #fff; }
        .btn--primary:hover { background: #018A80; border-color: #018A80; }
        .btn--linkedin { color: #0A66C2; }
        .btn--facebook { color: #1877F2; }

        .notice { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 16px; padding: 12px 16px; border-radius: 12px; font-size: 14px; line-height: 1.5; }
        .notice svg { width: 20px; height: 20px; flex: none; margin-top: 1px; }
        .notice--success { background: #E6F6F4; color: #0B4F4A; border: 1px solid #B5E3DE; }
        .notice--error { background: #FDECEC; color: #8A1C1C; border: 1px solid #F5C2C2; }
        .notice--info { background: #FFF8E6; color: #6B4E12; border: 1px solid #F1DDA5; justify-content: space-between; flex-wrap: wrap; }
        .notice form { margin: 0; }
        .notice .btn { height: 34px; font-size: 13px; }

        .verified { display: flex; flex-wrap: wrap; align-items: center; gap: 14px; margin-bottom: 18px; padding: 14px 18px; border-radius: 14px; background: #fff; border: 1px solid #CDE7E3; }
        .verified__icon { display: flex; align-items: center; justify-content: center; width: 40px; height: 40px; border-radius: 999px; background: #019D91; color: #fff; flex: none; }
        .verified__icon svg { width: 22px; height: 22px; }
        .verified__title { margin: 0; font-size: 15px; font-weight: 700; color: #0B4F4A; }
        .verified__text { margin: 2px 0 0; font-size: 13.5px; color: #4E5B59; line-height: 1.5; }
        .verified__text strong { color: #1C2B29; }
        .verified .btn { margin-left: auto; }

        .sheet-wrap { border-radius: 6px; box-shadow: 0 1px 2px rgba(16, 40, 38, .08), 0 18px 48px -12px rgba(16, 40, 38, .28); }

        .hint { margin: 14px 0 0; text-align: center; font-size: 13px; color: #5F6B69; }

        .toast { position: fixed; left: 50%; bottom: 24px; transform: translate(-50%, 20px); padding: 10px 16px; border-radius: 10px; background: #1C2B29; color: #fff; font-size: 14px; opacity: 0; pointer-events: none; transition: opacity .2s, transform .2s; }
        .toast.is-visible { opacity: 1; transform: translate(-50%, 0); }

        @media (max-width: 640px) {
            .btn { height: 38px; padding: 0 12px; font-size: 13px; }
            .btn span.btn__long { display: none; }
            .verified .btn { margin-left: 0; }
        }
        @media (prefers-reduced-motion: reduce) { .btn, .toast { transition: none; } }
        @media print {
            body { min-height: 0; background: #fff; }
            .page { max-width: none; padding: 0; }
        }
    </style>
</head>
<body>
    <main class="page">
        <div class="toolbar no-print">
            <a class="toolbar__back" href="{{ $isOwner ? route('certificates.index') : url('/') }}">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M15 18l-6-6 6-6" /></svg>
                {{ $isOwner ? 'Chứng chỉ của tôi' : 'Về trang chủ '.$appName }}
            </a>

            <div class="toolbar__actions">
                @if ($isOwner)
                    <button type="button" class="btn btn--primary" onclick="window.print()">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12m0 0l-4-4m4 4l4-4M5 21h14" /></svg>
                        Tải PDF / In
                    </button>
                @endif
                <button type="button" class="btn" data-copy-link="{{ $shareUrl }}">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 14a5 5 0 007.07 0l3-3a5 5 0 00-7.07-7.07l-1.5 1.5" /><path d="M14 10a5 5 0 00-7.07 0l-3 3a5 5 0 007.07 7.07l1.5-1.5" /></svg>
                    Sao chép liên kết
                </button>
                @if ($isOwner)
                    <a class="btn btn--linkedin" href="{{ $certificate->linkedInUrl() }}" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.41v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.46v6.28zM5.34 7.43a2.06 2.06 0 110-4.13 2.06 2.06 0 010 4.13zM7.12 20.45H3.56V9h3.56v11.45zM22.22 0H1.77C.79 0 0 .77 0 1.73v20.54C0 23.23.79 24 1.77 24h20.45c.98 0 1.78-.77 1.78-1.73V1.73C24 .77 23.2 0 22.22 0z" /></svg>
                        <span>Thêm vào <span class="btn__long">hồ sơ </span>LinkedIn</span>
                    </a>
                    <a class="btn btn--facebook" href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode($shareUrl) }}" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M24 12.07C24 5.4 18.63 0 12 0S0 5.4 0 12.07C0 18.1 4.39 23.1 10.13 24v-8.44H7.08v-3.49h3.04V9.41c0-3.02 1.8-4.7 4.54-4.7 1.31 0 2.68.24 2.68.24v2.97h-1.5c-1.5 0-1.96.93-1.96 1.89v2.26h3.33l-.53 3.49h-2.8V24C19.62 23.1 24 18.1 24 12.07z" /></svg>
                        Chia sẻ
                    </a>
                @endif
            </div>
        </div>

        @if (session('success'))
            <div class="notice notice--success no-print" role="status">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6L9 17l-5-5" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="notice notice--error no-print" role="alert">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9" /><path d="M12 8v5m0 3h.01" /></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if ($nameChanged)
            <div class="notice notice--info no-print">
                <span>Tên trên chứng chỉ là <strong>{{ $certificate->recipient_name }}</strong>, khác với tên hiện tại trong hồ sơ của bạn (<strong>{{ auth()->user()->name }}</strong>).</span>
                <form method="POST" action="{{ route('certificates.refresh-name', $certificate) }}">
                    @csrf
                    <button type="submit" class="btn">Cập nhật tên trên chứng chỉ</button>
                </form>
            </div>
        @endif

        @unless ($isOwner)
            <section class="verified no-print" aria-label="Kết quả xác minh">
                <span class="verified__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5" /></svg>
                </span>
                <div>
                    <p class="verified__title">Chứng chỉ hợp lệ</p>
                    <p class="verified__text">
                        Do <strong>{{ $appName }}</strong> cấp cho <strong>{{ $certificate->recipient_name }}</strong>
                        ngày {{ $certificate->issuedOnText() }}, mã <strong>{{ $certificate->code }}</strong>.
                    </p>
                </div>
                <a class="btn" href="{{ route('certificates.verify') }}">Tra cứu chứng chỉ khác</a>
            </section>
        @endunless

        <div class="sheet-wrap cert-print-area">
            <x-certificate-sheet :certificate="$certificate" />
        </div>

        @if ($isOwner)
            <p class="hint no-print">Bấm “Tải PDF / In” rồi chọn <strong>Lưu dưới dạng PDF</strong> để tải về. Khổ giấy A4 ngang, nhớ bật “Đồ họa nền” nếu trình duyệt hỏi.</p>
        @endif
    </main>

    <div class="toast no-print" role="status" aria-live="polite" data-toast></div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcode-generator/1.4.4/qrcode.min.js"
            integrity="sha512-ZDSPMa/JM1D+7kdg2x3BsruQ6T/JpJo3jWDWkCZsP+5yVyp1KfESqLI+7RqB5k24F7p2cV7i2YHh/890y6P6Sw=="
            crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        (function () {
            // Mã QR dẫn tới trang xác minh này. Không tải được thư viện thì ẩn ô QR, chứng chỉ vẫn đầy đủ thông tin.
            document.querySelectorAll('[data-cert-qr]').forEach(function (box) {
                if (typeof window.qrcode !== 'function') {
                    box.style.display = 'none';
                    return;
                }
                var qr = window.qrcode(0, 'M');
                qr.addData(box.getAttribute('data-cert-qr'));
                qr.make();
                box.innerHTML = qr.createSvgTag({ cellSize: 4, margin: 0, scalable: true });
            });

            // Nút "Tải PDF" ở trang Chứng chỉ của tôi mở trang này kèm ?print=1: chờ font tải xong rồi mở hộp thoại in.
            if (@js($isOwner) && new URLSearchParams(window.location.search).has('print')) {
                var ready = document.fonts && document.fonts.ready ? document.fonts.ready : Promise.resolve();
                ready.then(function () { setTimeout(function () { window.print(); }, 250); });
            }

            var toast = document.querySelector('[data-toast]');
            var timer;
            function showToast(message) {
                toast.textContent = message;
                toast.classList.add('is-visible');
                clearTimeout(timer);
                timer = setTimeout(function () { toast.classList.remove('is-visible'); }, 2200);
            }

            document.querySelectorAll('[data-copy-link]').forEach(function (button) {
                button.addEventListener('click', function () {
                    var link = button.getAttribute('data-copy-link');
                    if (navigator.clipboard && window.isSecureContext) {
                        navigator.clipboard.writeText(link).then(function () {
                            showToast('Đã sao chép liên kết chứng chỉ');
                        }, function () {
                            window.prompt('Sao chép liên kết chứng chỉ:', link);
                        });
                    } else {
                        window.prompt('Sao chép liên kết chứng chỉ:', link);
                    }
                });
            });
        })();
    </script>
</body>
</html>
