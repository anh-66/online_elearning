@extends('layouts.public')

@section('title', 'Tra cứu chứng chỉ')

@push('styles')
<style>
    .cv { --cv-teal: #019D91; --cv-deep: #0B4F4A; --cv-ink: #1D1D1F; --cv-muted: #6E6E73; --cv-line: #E5E5EA; --cv-card: #fff; --cv-soft: #F5F8F8; }
    .dark .cv { --cv-teal: #00ddb3; --cv-deep: #7fe3d6; --cv-ink: #e6edf3; --cv-muted: #8b949e; --cv-line: #30363d; --cv-card: #161b22; --cv-soft: #0d1117; }
    .cv { padding: 56px 16px 80px; background: linear-gradient(180deg, rgba(1, 157, 145, .07), transparent 320px); }
    .cv__inner { max-width: 640px; margin: 0 auto; }
    .cv__icon { display: flex; align-items: center; justify-content: center; width: 56px; height: 56px; margin: 0 auto; border-radius: 16px; background: var(--cv-teal); color: #fff; }
    .dark .cv__icon { color: #0d1117; }
    .cv__icon svg { width: 30px; height: 30px; }
    .cv__title { margin: 18px 0 0; text-align: center; font-size: clamp(26px, 4vw, 34px); font-weight: 800; letter-spacing: -.02em; color: var(--cv-ink); }
    .cv__lead { margin: 10px auto 0; max-width: 520px; text-align: center; font-size: 15.5px; line-height: 1.6; color: var(--cv-muted); }
    .cv__card { margin-top: 28px; padding: 22px; border-radius: 18px; background: var(--cv-card); border: 1px solid var(--cv-line); box-shadow: 0 10px 30px -18px rgba(0, 0, 0, .25); }
    .cv__label { display: block; margin-bottom: 8px; font-size: 14px; font-weight: 600; color: var(--cv-ink); }
    .cv__row { display: flex; gap: 10px; }
    .cv__input { flex: 1; min-width: 0; height: 50px; padding: 0 16px; border-radius: 12px; border: 1px solid var(--cv-line); background: var(--cv-soft); color: var(--cv-ink); font: 600 16px/1 ui-monospace, 'SFMono-Regular', Consolas, monospace; letter-spacing: .06em; text-transform: uppercase; }
    .cv__input::placeholder { color: var(--cv-muted); font-weight: 500; letter-spacing: .04em; }
    .cv__input:focus { outline: none; border-color: var(--cv-teal); box-shadow: 0 0 0 4px rgba(1, 157, 145, .15); }
    .cv__btn { flex: none; height: 50px; padding: 0 22px; border: 0; border-radius: 12px; background: var(--cv-teal); color: #fff; font-weight: 700; font-size: 15px; cursor: pointer; transition: filter .15s; }
    .dark .cv__btn { color: #0d1117; }
    .cv__btn:hover { filter: brightness(.94); }
    .cv__btn:focus-visible { outline: 3px solid rgba(1, 157, 145, .4); outline-offset: 2px; }
    .cv__error { display: flex; gap: 10px; margin-top: 16px; padding: 12px 14px; border-radius: 12px; background: #FDECEC; border: 1px solid #F5C2C2; color: #8A1C1C; font-size: 14px; line-height: 1.55; }
    .dark .cv__error { background: rgba(248, 81, 73, .1); border-color: rgba(248, 81, 73, .35); color: #ffa198; }
    .cv__error svg { width: 20px; height: 20px; flex: none; margin-top: 1px; }
    .cv__error code { font-weight: 700; }
    .cv__tips { display: grid; gap: 12px; margin: 22px 0 0; padding: 0; list-style: none; }
    .cv__tip { display: flex; gap: 12px; align-items: flex-start; font-size: 14px; line-height: 1.55; color: var(--cv-muted); }
    .cv__tip strong { color: var(--cv-ink); font-weight: 600; }
    .cv__tip-num { display: flex; align-items: center; justify-content: center; flex: none; width: 26px; height: 26px; border-radius: 999px; background: rgba(1, 157, 145, .12); color: var(--cv-deep); font-size: 13px; font-weight: 700; }
    @media (max-width: 520px) { .cv__row { flex-direction: column; } .cv__btn { width: 100%; } }
</style>
@endpush

@section('content')
<section class="cv">
    <div class="cv__inner">
        <div class="cv__icon" aria-hidden="true">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v5c0 4.5-3 8.3-7 10-4-1.7-7-5.5-7-10V6l7-3z" /><path d="M9 12l2 2 4-4" /></svg>
        </div>
        <h1 class="cv__title">Tra cứu chứng chỉ</h1>
        <p class="cv__lead">Nhập mã in trên chứng chỉ để kiểm tra chứng chỉ đó có đúng do {{ config('app.name', 'EduLearn') }} cấp hay không.</p>

        <div class="cv__card">
            <form method="GET" action="{{ route('certificates.verify') }}">
                <label class="cv__label" for="certificate-code">Mã chứng chỉ</label>
                <div class="cv__row">
                    <input id="certificate-code" class="cv__input" type="text" name="code" value="{{ $code }}"
                           placeholder="EDU-2026-XXXX-XXXX" autocomplete="off" spellcheck="false" maxlength="40" required>
                    <button type="submit" class="cv__btn">Tra cứu</button>
                </div>
            </form>

            @if ($notFound)
                <div class="cv__error" role="alert">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9" /><path d="M12 8v5m0 3h.01" /></svg>
                    <span>Không tìm thấy chứng chỉ có mã <code>{{ $code }}</code>. Bạn kiểm tra lại từng ký tự nhé, mã có dạng EDU-năm-4 ký tự-4 ký tự và không dùng chữ O, I, L hay số 0, 1.</span>
                </div>
            @endif

            <ul class="cv__tips">
                <li class="cv__tip"><span class="cv__tip-num">1</span><span><strong>Mã nằm ở góc trái phía dưới</strong> của chứng chỉ, ngay cạnh ngày cấp.</span></li>
                <li class="cv__tip"><span class="cv__tip-num">2</span><span><strong>Quét mã QR</strong> trên chứng chỉ bằng camera điện thoại để mở thẳng trang xác minh.</span></li>
                <li class="cv__tip"><span class="cv__tip-num">3</span><span>Mỗi chứng chỉ có <strong>một mã riêng</strong>, trang xác minh hiện đúng tên học viên, khóa học và ngày cấp.</span></li>
            </ul>
        </div>
    </div>
</section>
@endsection
