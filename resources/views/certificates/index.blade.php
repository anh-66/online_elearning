@extends('layouts.public')

@section('title', 'Chứng chỉ của tôi')

@push('styles')
@include('certificates.partials.styles')
<style>
    .mc { --mc-teal: #019D91; --mc-ink: #1D1D1F; --mc-muted: #6E6E73; --mc-line: #E5E5EA; --mc-card: #fff; --mc-soft: #F5F7F7; --mc-track: #EDF0F0; }
    .dark .mc { --mc-teal: #00ddb3; --mc-ink: #e6edf3; --mc-muted: #8b949e; --mc-line: #30363d; --mc-card: #161b22; --mc-soft: #0d1117; --mc-track: #21262d; }
    .mc { width: 100%; max-width: 1280px; margin: 0 auto; padding: 40px 16px 80px; }
    @media (min-width: 640px) { .mc { padding: 48px 40px 96px; } }

    .mc__head { display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 16px; }
    .mc__eyebrow { margin: 0; font-size: 13px; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; color: var(--mc-teal); }
    .mc__title { margin: 6px 0 0; font-size: clamp(26px, 3.4vw, 36px); font-weight: 800; letter-spacing: -.02em; color: var(--mc-ink); }
    .mc__lead { margin: 8px 0 0; max-width: 620px; font-size: 15px; line-height: 1.6; color: var(--mc-muted); }
    .mc__link { display: inline-flex; align-items: center; gap: 8px; height: 42px; padding: 0 16px; border-radius: 12px; border: 1px solid var(--mc-line); background: var(--mc-card); color: var(--mc-ink); font-size: 14px; font-weight: 600; text-decoration: none; transition: border-color .15s, color .15s; }
    .mc__link:hover { border-color: var(--mc-teal); color: var(--mc-teal); }
    .mc__link svg { width: 18px; height: 18px; }

    .mc__grid { display: grid; gap: 24px; margin-top: 28px; grid-template-columns: repeat(auto-fill, minmax(min(100%, 340px), 1fr)); }
    .mc__card { display: flex; flex-direction: column; overflow: hidden; border-radius: 18px; border: 1px solid var(--mc-line); background: var(--mc-card); transition: box-shadow .2s, border-color .2s, transform .2s; }
    .mc__card:hover { border-color: rgba(1, 157, 145, .45); box-shadow: 0 16px 36px -20px rgba(0, 0, 0, .35); transform: translateY(-2px); }
    .mc__preview { display: block; padding: 18px; background: linear-gradient(135deg, #E7EFEE, #F4F1E8); }
    .dark .mc__preview { background: linear-gradient(135deg, #1b2a29, #222018); }
    .mc__preview .cert { border-radius: 3px; box-shadow: 0 6px 18px -8px rgba(16, 40, 38, .45); }
    .mc__body { display: flex; flex: 1; flex-direction: column; gap: 6px; padding: 16px 18px 18px; }
    .mc__course { margin: 0; font-size: 16px; font-weight: 700; line-height: 1.35; color: var(--mc-ink); display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .mc__course a { color: inherit; text-decoration: none; }
    .mc__course a:hover { color: var(--mc-teal); }
    .mc__meta { margin: 0; font-size: 13px; color: var(--mc-muted); }
    .mc__code { font-family: ui-monospace, 'SFMono-Regular', Consolas, monospace; font-size: 12.5px; letter-spacing: .04em; }
    .mc__actions { display: flex; gap: 8px; margin-top: auto; padding-top: 12px; }
    .mc__btn { flex: 1; display: inline-flex; align-items: center; justify-content: center; gap: 6px; height: 38px; border-radius: 10px; font-size: 13px; font-weight: 700; text-decoration: none; transition: filter .15s, background-color .15s; }
    .mc__btn svg { width: 16px; height: 16px; }
    .mc__btn--primary { background: var(--mc-teal); color: #fff; }
    .dark .mc__btn--primary { color: #0d1117; }
    .mc__btn--primary:hover { filter: brightness(.94); }
    .mc__btn--ghost { border: 1px solid var(--mc-line); color: var(--mc-ink); }
    .mc__btn--ghost:hover { background: var(--mc-soft); }

    .mc__empty { margin-top: 28px; display: grid; gap: 28px; align-items: center; padding: 28px; border-radius: 20px; border: 1px dashed var(--mc-line); background: var(--mc-card); }
    @media (min-width: 860px) { .mc__empty { grid-template-columns: 340px 1fr; padding: 36px; } }
    .mc__empty-sheet { position: relative; }
    .mc__empty-sheet .cert { filter: grayscale(.35); opacity: .55; border-radius: 3px; }
    .mc__empty-lock { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; }
    .mc__empty-lock span { display: flex; align-items: center; justify-content: center; width: 52px; height: 52px; border-radius: 999px; background: var(--mc-card); color: var(--mc-teal); box-shadow: 0 6px 20px -6px rgba(0, 0, 0, .3); }
    .mc__empty-lock svg { width: 24px; height: 24px; }
    /* Chỉ áp dụng cho phần chữ bên phải, không lan vào tờ chứng chỉ mẫu bên trái (nó cũng có thẻ h2, p). */
    .mc__empty-text h2 { margin: 0; font-size: 20px; font-weight: 800; color: var(--mc-ink); }
    .mc__empty-text p { margin: 8px 0 0; font-size: 14.5px; line-height: 1.6; color: var(--mc-muted); }
    .mc__empty .mc__btn { flex: none; width: auto; padding: 0 18px; margin-top: 18px; height: 42px; font-size: 14px; }

    .mc__section { margin-top: 56px; }
    .mc__section h2 { margin: 0; font-size: 20px; font-weight: 800; color: var(--mc-ink); }
    .mc__section > p { margin: 6px 0 0; font-size: 14px; color: var(--mc-muted); }
    .mc__list { display: grid; gap: 14px; margin: 20px 0 0; padding: 0; list-style: none; grid-template-columns: repeat(auto-fill, minmax(min(100%, 300px), 1fr)); }
    .mc__item { display: flex; flex-direction: column; gap: 10px; padding: 16px 18px; border-radius: 16px; border: 1px solid var(--mc-line); background: var(--mc-card); }
    .mc__item-title { margin: 0; font-size: 15px; font-weight: 700; line-height: 1.35; color: var(--mc-ink); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .mc__item-sub { margin: 2px 0 0; font-size: 12.5px; color: var(--mc-muted); }
    .mc__bar { height: 8px; border-radius: 999px; background: var(--mc-track); overflow: hidden; }
    .mc__bar span { display: block; height: 100%; border-radius: inherit; background: linear-gradient(90deg, #019D91, #2BC4B7); }
    .mc__item-foot { display: flex; align-items: center; justify-content: space-between; gap: 10px; font-size: 13px; color: var(--mc-muted); }
    .mc__item-foot strong { color: var(--mc-ink); }
    .mc__item-foot a { font-weight: 700; color: var(--mc-teal); text-decoration: none; }
    .mc__item-foot a:hover { text-decoration: underline; }

    @media (prefers-reduced-motion: reduce) { .mc__card, .mc__card:hover { transition: none; transform: none; } }
</style>
@endpush

@section('content')
<div class="mc">
    <div class="mc__head">
        <div>
            <p class="mc__eyebrow">Thành tích học tập</p>
            <h1 class="mc__title">Chứng chỉ của tôi</h1>
            <p class="mc__lead">
                @if ($certificates->isNotEmpty())
                    Bạn đã nhận {{ $certificates->count() }} chứng chỉ. Mỗi chứng chỉ có mã QR và đường link riêng để nhà tuyển dụng kiểm tra.
                @else
                    Học xong tất cả bài học của một khóa là bạn nhận chứng chỉ ngay, có mã QR để ai cũng kiểm tra được.
                @endif
            </p>
        </div>
        <a class="mc__link" href="{{ route('certificates.verify') }}">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3l7 3v5c0 4.5-3 8.3-7 10-4-1.7-7-5.5-7-10V6l7-3z" /><path d="M9 12l2 2 4-4" /></svg>
            Tra cứu chứng chỉ
        </a>
    </div>

    @if ($certificates->isNotEmpty())
        <div class="mc__grid">
            @foreach ($certificates as $certificate)
                <article class="mc__card">
                    <a class="mc__preview" href="{{ route('certificates.show', $certificate->code) }}" aria-label="Xem chứng chỉ {{ $certificate->course_title }}">
                        <x-certificate-sheet :certificate="$certificate" thumbnail />
                    </a>
                    <div class="mc__body">
                        <h2 class="mc__course"><a href="{{ route('certificates.show', $certificate->code) }}">{{ $certificate->course_title }}</a></h2>
                        <p class="mc__meta">Cấp ngày {{ $certificate->issuedOnText() }}</p>
                        <p class="mc__meta">Mã <span class="mc__code">{{ $certificate->code }}</span></p>
                        <div class="mc__actions">
                            <a class="mc__btn mc__btn--primary" href="{{ route('certificates.show', $certificate->code) }}">Xem chứng chỉ</a>
                            <a class="mc__btn mc__btn--ghost" href="{{ route('certificates.show', ['code' => $certificate->code, 'print' => 1]) }}">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v12m0 0l-4-4m4 4l4-4M5 21h14" /></svg>
                                Tải PDF
                            </a>
                        </div>
                    </div>
                </article>
            @endforeach
        </div>
    @else
        @php
            $sample = new \App\Models\Certificate([
                'code' => 'EDU-'.now()->year.'-XXXX-XXXX',
                'recipient_name' => auth()->user()->name,
                'course_title' => 'Khóa học đầu tiên của bạn',
                'instructor_name' => 'Giảng viên',
                'lessons_count' => 0,
                'issued_at' => now(),
            ]);
        @endphp
        <div class="mc__empty">
            <div class="mc__empty-sheet" aria-hidden="true">
                <x-certificate-sheet :certificate="$sample" thumbnail />
                <div class="mc__empty-lock"><span><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="11" width="14" height="10" rx="2" /><path d="M8 11V8a4 4 0 018 0v3" /></svg></span></div>
            </div>
            <div class="mc__empty-text">
                <h2>Bạn chưa có chứng chỉ nào</h2>
                <p>Hoàn thành tất cả bài học của một khóa để nhận chứng chỉ đầu tiên. Chứng chỉ được cấp tự động, bạn có thể tải PDF, in ra hoặc gửi liên kết để người khác kiểm tra.</p>
                <a class="mc__btn mc__btn--primary" href="{{ route($inProgress->isNotEmpty() ? 'my-courses' : 'courses.index') }}">
                    {{ $inProgress->isNotEmpty() ? 'Tiếp tục học' : 'Khám phá khóa học' }}
                </a>
            </div>
        </div>
    @endif

    @if ($inProgress->isNotEmpty())
        <section class="mc__section">
            <h2>Sắp nhận chứng chỉ</h2>
            <p>Các khóa bạn đang học, học xong là có chứng chỉ.</p>
            <ul class="mc__list">
                @foreach ($inProgress as $course)
                    @php $percent = min(100, max(0, (int) $course->pivot->progress_percent)); @endphp
                    <li class="mc__item">
                        <div>
                            <p class="mc__item-title" title="{{ $course->title }}">{{ $course->title }}</p>
                            <p class="mc__item-sub">{{ $course->instructor->name ?? config('app.name', 'EduLearn') }}</p>
                        </div>
                        <div class="mc__bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="{{ $percent }}" aria-label="Tiến độ {{ $course->title }}"><span style="width: {{ $percent }}%"></span></div>
                        <div class="mc__item-foot">
                            <span>Đã học <strong>{{ $percent }}%</strong></span>
                            <a href="{{ route('learning.index', $course->slug) }}">Học tiếp</a>
                        </div>
                    </li>
                @endforeach
            </ul>
        </section>
    @endif
</div>
@endsection
