@props(['certificate', 'thumbnail' => false])

@php
    /** @var \App\Models\Certificate $certificate */
    $uid = 'cert'.$certificate->getKey().'-'.\Illuminate\Support\Str::lower(\Illuminate\Support\Str::random(5));
    $course = $certificate->course;

    $nameLength = mb_strlen($certificate->recipient_name);
    $nameSize = $nameLength > 30 ? 3.8 : ($nameLength > 22 ? 4.5 : 5.3);

    $titleLength = mb_strlen($certificate->course_title);
    $titleSize = $titleLength > 70 ? 1.95 : ($titleLength > 45 ? 2.3 : 2.7);

    $meta = array_filter([
        $certificate->lessons_count > 0 ? $certificate->lessons_count.' bài học' : null,
        $course ? 'Trình độ '.mb_strtolower($course->levelLabel()) : null,
        $course?->category?->name,
    ]);

    $appName = config('app.name', 'EduLearn');
    $signer = $certificate->instructor_name ?: 'Ban đào tạo '.$appName;
    $signature = $certificate->instructor_name ?: $appName;
    $signerRole = $certificate->instructor_name ? 'Giảng viên khóa học' : 'Đơn vị cấp chứng chỉ';
    $signatureLength = mb_strlen($signature);
    $signatureSize = $signatureLength > 24 ? 1.7 : ($signatureLength > 17 ? 2.1 : 2.5);
    $displayUrl = preg_replace('#^https?://#', '', $certificate->url());
    $year = $certificate->issued_at->copy()->timezone(\App\Models\Certificate::DISPLAY_TIMEZONE)->year;

    // Viền răng cưa của con dấu: 40 đỉnh xen kẽ bán kính 50 / 46.
    $sealPoints = collect(range(0, 79))->map(function (int $i) {
        $radius = $i % 2 === 0 ? 50 : 46.5;
        $angle = deg2rad($i * 4.5 - 90);

        return round(60 + $radius * cos($angle), 2).','.round(60 + $radius * sin($angle), 2);
    })->implode(' ');

    $capPath = 'M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z';
@endphp

<div {{ $attributes->class(['cert', 'cert--thumb' => $thumbnail]) }}>
    {{-- Họa tiết nền: logo chìm --}}
    <svg class="cert__watermark" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.1" d="{{ $capPath }}" />
    </svg>

    {{-- Khung viền và hoa văn 4 góc --}}
    <div class="cert__frame" aria-hidden="true"></div>
    @foreach (['tl', 'tr', 'bl', 'br'] as $corner)
        <svg class="cert__corner cert__corner--{{ $corner }}" viewBox="0 0 100 100" fill="none" stroke="currentColor" aria-hidden="true">
            <path d="M0 0 H15 A15 15 0 0 1 0 15 Z" fill="currentColor" stroke="none" />
            <path d="M0 23 A23 23 0 0 0 23 0" stroke-width="1.4" />
            <path d="M0 28 A28 28 0 0 0 28 0" stroke-width="0.7" />
            <path d="M31 7 C46 7 51 22 42 29 C36 33 31 27 35 23 C37 21 40 22 40 24" stroke-width="1.3" stroke-linecap="round" />
            <path d="M7 31 C7 46 22 51 29 42 C33 36 27 31 23 35 C21 37 22 40 24 40" stroke-width="1.3" stroke-linecap="round" />
            <path d="M27 27 Q41 31 47 47 Q31 41 27 27 Z" fill="currentColor" stroke="none" />
            <path d="M47 6 H88 M6 47 V88" stroke-width="0.8" stroke-linecap="round" />
            <circle cx="93" cy="6" r="1.8" fill="currentColor" stroke="none" />
            <circle cx="6" cy="93" r="1.8" fill="currentColor" stroke="none" />
        </svg>
    @endforeach

    <div class="cert__content">
        <div class="cert__brand">
            <span class="cert__brand-mark" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $capPath }}" />
                </svg>
            </span>
            <span class="cert__brand-text">
                <p class="cert__brand-name">{{ \Illuminate\Support\Str::upper(config('app.name', 'EduLearn')) }}</p>
                <p class="cert__brand-tagline">Nền tảng học trực tuyến</p>
            </span>
        </div>

        <h1 class="cert__title">CHỨNG NHẬN</h1>
        <p class="cert__subtitle">HOÀN THÀNH KHÓA HỌC</p>
        <p class="cert__en">Certificate of Completion</p>

        <div class="cert__main">
        <p class="cert__lead cert__lead--first">Chứng nhận học viên</p>
        <h2 class="cert__name" style="font-size: {{ $nameSize }}cqw">{{ $certificate->recipient_name }}</h2>
        <div class="cert__divider" aria-hidden="true"><span></span></div>

        <p class="cert__lead cert__lead--second">đã hoàn thành xuất sắc khóa học trực tuyến</p>
        <p class="cert__course" style="font-size: {{ $titleSize }}cqw">{{ $certificate->course_title }}</p>

        @if ($meta)
            <p class="cert__meta">
                @foreach ($meta as $item)
                    <span>{{ $item }}</span>
                @endforeach
            </p>
        @endif
        </div>

        <div class="cert__bottom">
            <div class="cert__verify">
                @unless ($thumbnail)
                    <div class="cert__qr" data-cert-qr="{{ $certificate->url() }}" aria-label="Mã QR xác minh chứng chỉ"></div>
                @endunless
                <div>
                    <div class="cert__verify-row">
                        <p class="cert__label">Ngày cấp</p>
                        <p class="cert__value">{{ $certificate->issuedOnText() }}</p>
                    </div>
                    <div class="cert__verify-row">
                        <p class="cert__label">Mã chứng chỉ</p>
                        <p class="cert__value cert__value--code">{{ $certificate->code }}</p>
                    </div>
                </div>
            </div>

            <div class="cert__seal" aria-hidden="true">
                <svg viewBox="0 0 120 150">
                    <defs>
                        <linearGradient id="{{ $uid }}-gold" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0" stop-color="#F1D88A" />
                            <stop offset="0.5" stop-color="#D3A946" />
                            <stop offset="1" stop-color="#A97C27" />
                        </linearGradient>
                        <linearGradient id="{{ $uid }}-inner" x1="0" y1="0" x2="1" y2="1">
                            <stop offset="0" stop-color="#F7E4A6" />
                            <stop offset="1" stop-color="#CFA142" />
                        </linearGradient>
                        <path id="{{ $uid }}-ring" d="M60,60 m-37,0 a37,37 0 1,1 74,0 a37,37 0 1,1 -74,0" />
                    </defs>

                    <polygon points="44,96 28,144 39,137 47,148 58,100" fill="#0B4F4A" />
                    <polygon points="76,96 92,144 81,137 73,148 62,100" fill="#11685F" />

                    <polygon points="{{ $sealPoints }}" fill="url(#{{ $uid }}-gold)" stroke="#9A7024" stroke-width="0.8" />
                    <circle cx="60" cy="60" r="44" fill="none" stroke="#FFF3CC" stroke-width="0.8" stroke-opacity="0.8" />
                    <circle cx="60" cy="60" r="42.5" fill="#A87C28" />
                    <circle cx="60" cy="60" r="31" fill="url(#{{ $uid }}-inner)" stroke="#FFF3CC" stroke-width="0.8" />

                    <text font-family="'Be Vietnam Pro', sans-serif" font-size="6.4" font-weight="700" fill="#FFF6DA" letter-spacing="0.6">
                        <textPath href="#{{ $uid }}-ring" startOffset="0" textLength="226" lengthAdjust="spacing">EDULEARN • CHỨNG NHẬN HOÀN THÀNH •</textPath>
                    </text>

                    <g transform="translate(49 38) scale(0.92)" fill="none" stroke="#5B4212" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="{{ $capPath }}" />
                    </g>
                    <text x="60" y="74" text-anchor="middle" font-family="'Playfair Display', Georgia, serif" font-size="13" font-weight="700" fill="#5B4212">{{ $year }}</text>
                    <text x="60" y="82.5" text-anchor="middle" font-family="'Be Vietnam Pro', sans-serif" font-size="4.6" font-weight="600" letter-spacing="1.2" fill="#6E5219">XÁC THỰC</text>
                </svg>
            </div>

            <div class="cert__sign">
                <p class="cert__signature" style="font-size: {{ $signatureSize }}cqw">{{ $signature }}</p>
                <div class="cert__rule"></div>
                <p class="cert__sign-name">{{ $signer }}</p>
                <p class="cert__sign-role">{{ $signerRole }}</p>
            </div>
        </div>
    </div>

    <p class="cert__footnote">Xác minh tính hợp lệ của chứng chỉ tại {{ $displayUrl }}</p>
</div>
