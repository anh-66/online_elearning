{{-- Font và CSS của tờ chứng chỉ. Mọi kích thước tính theo cqw (phần trăm bề ngang tờ giấy)
     nên tờ chứng chỉ thu nhỏ / phóng to vẫn giữ nguyên bố cục, kể cả khi in ra khổ A4 ngang. --}}
<link rel="preconnect" href="https://fonts.bunny.net">
<link href="https://fonts.bunny.net/css?family=playfair-display:400,600,700,400i,600i|dancing-script:600,700|be-vietnam-pro:400,500,600,700&display=swap" rel="stylesheet" />
<style>
    .cert {
        --cert-teal: #019D91;
        --cert-teal-deep: #0B4F4A;
        --cert-gold: #B98A2F;
        --cert-gold-light: #E3C26F;
        --cert-ink: #1C2B29;
        --cert-muted: #66706E;
        --cert-paper: #FFFCF4;

        position: relative;
        width: 100%;
        aspect-ratio: 297 / 210;
        container-type: inline-size;
        overflow: hidden;
        color: var(--cert-ink);
        font-family: 'Be Vietnam Pro', system-ui, sans-serif;
        background-color: var(--cert-paper);
        background-image:
            radial-gradient(ellipse 70% 60% at 50% 45%, rgba(255, 252, 244, 0.96) 0%, rgba(255, 252, 244, 0.55) 60%, rgba(255, 252, 244, 0) 100%),
            url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='60' height='30' viewBox='0 0 60 30'%3E%3Cg fill='none' stroke='%23019D91' stroke-opacity='0.11' stroke-width='0.6'%3E%3Cpath d='M0 15 C 15 0, 15 30, 30 15 S 45 0, 60 15'/%3E%3Cpath d='M0 15 C 15 30, 15 0, 30 15 S 45 30, 60 15'/%3E%3C/g%3E%3C/svg%3E");
        background-size: 100% 100%, 3.4cqw 1.7cqw;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
        line-height: 1.25;
        text-align: center;
    }

    .cert *, .cert *::before, .cert *::after { box-sizing: border-box; }

    .cert p, .cert h1, .cert h2 { margin: 0; }

    /* Khung viền: dải xanh đậm, chỉ vàng, chỉ mảnh bên trong */
    .cert__frame {
        position: absolute;
        inset: 1.6cqw;
        border: 0.45cqw solid var(--cert-teal-deep);
        pointer-events: none;
    }
    .cert__frame::before {
        content: '';
        position: absolute;
        inset: 0.55cqw;
        border: 0.14cqw solid var(--cert-gold);
    }
    .cert__frame::after {
        content: '';
        position: absolute;
        inset: 0.95cqw;
        border: 0.05cqw solid rgba(11, 79, 74, 0.45);
    }

    /* Hoa văn góc nằm sát đường chỉ mảnh bên trong khung */
    .cert__corner {
        position: absolute;
        width: 8cqw;
        height: 8cqw;
        color: var(--cert-gold);
        pointer-events: none;
    }
    .cert__corner--tl { top: 3cqw; left: 3cqw; }
    .cert__corner--tr { top: 3cqw; right: 3cqw; transform: scaleX(-1); }
    .cert__corner--bl { bottom: 3cqw; left: 3cqw; transform: scaleY(-1); }
    .cert__corner--br { bottom: 3cqw; right: 3cqw; transform: scale(-1, -1); }

    .cert__watermark {
        position: absolute;
        left: 50%;
        top: 47%;
        width: 30cqw;
        height: 30cqw;
        transform: translate(-50%, -50%);
        color: var(--cert-teal);
        opacity: 0.045;
        pointer-events: none;
    }

    .cert__content {
        position: absolute;
        inset: 4.3cqw 7.5cqw 5.1cqw;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .cert__brand {
        display: inline-flex;
        align-items: center;
        gap: 0.9cqw;
    }
    .cert__brand-mark {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 3cqw;
        height: 3cqw;
        border-radius: 999px;
        background: var(--cert-teal-deep);
        color: #fff;
    }
    .cert__brand-mark svg { width: 1.9cqw; height: 1.9cqw; }
    .cert__brand-text { text-align: left; }
    .cert__brand-name {
        font-size: 1.25cqw;
        font-weight: 700;
        letter-spacing: 0.32em;
        color: var(--cert-teal-deep);
    }
    .cert__brand-tagline {
        margin-top: 0.15cqw !important;
        font-size: 0.68cqw;
        letter-spacing: 0.22em;
        text-transform: uppercase;
        color: var(--cert-muted);
    }

    .cert__title {
        margin-top: 1.7cqw !important;
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 5.3cqw;
        font-weight: 700;
        line-height: 1.12;
        letter-spacing: 0.1em;
        color: var(--cert-teal-deep);
    }

    .cert__subtitle {
        margin-top: 0.6cqw !important;
        display: flex;
        align-items: center;
        gap: 1.2cqw;
        font-size: 1.1cqw;
        font-weight: 600;
        letter-spacing: 0.42em;
        color: var(--cert-gold);
    }
    .cert__subtitle::before,
    .cert__subtitle::after {
        content: '';
        width: 6cqw;
        height: 0.1cqw;
        background: linear-gradient(90deg, transparent, var(--cert-gold));
    }
    .cert__subtitle::after { background: linear-gradient(90deg, var(--cert-gold), transparent); }

    .cert__en {
        margin-top: 0.45cqw !important;
        font-family: 'Playfair Display', Georgia, serif;
        font-style: italic;
        font-size: 1cqw;
        color: var(--cert-muted);
    }

    /* Phần thân (tên học viên, khóa học) nằm giữa khoảng trống còn lại */
    .cert__main {
        flex: 1;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding-bottom: 1cqw;
    }

    .cert__lead {
        font-size: 1.2cqw;
        color: var(--cert-muted);
    }
    .cert__lead--second { margin-top: 1.5cqw !important; }

    .cert__name {
        margin-top: 0.5cqw !important;
        max-width: 100%;
        font-family: 'Playfair Display', Georgia, serif;
        font-style: italic;
        font-weight: 600;
        line-height: 1.3;
        color: var(--cert-ink);
        overflow-wrap: anywhere;
    }

    .cert__divider {
        margin-top: 0.35cqw;
        display: flex;
        align-items: center;
        gap: 0.8cqw;
        color: var(--cert-gold);
    }
    .cert__divider::before,
    .cert__divider::after {
        content: '';
        width: 17cqw;
        height: 0.08cqw;
        background: var(--cert-gold);
        opacity: 0.8;
    }
    .cert__divider span {
        width: 0.65cqw;
        height: 0.65cqw;
        background: var(--cert-gold);
        transform: rotate(45deg);
    }

    .cert__course {
        margin-top: 0.5cqw !important;
        max-width: 78%;
        font-weight: 700;
        line-height: 1.28;
        color: var(--cert-teal-deep);
        text-wrap: balance;
        overflow-wrap: anywhere;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .cert__meta {
        margin-top: 1.1cqw !important;
        font-size: 1cqw;
        letter-spacing: 0.04em;
        color: var(--cert-muted);
    }
    .cert__meta span + span::before {
        content: '•';
        margin: 0 0.8cqw;
        color: var(--cert-gold);
    }

    .cert__bottom {
        width: 100%;
        display: grid;
        grid-template-columns: 1fr auto 1fr;
        align-items: end;
        gap: 2cqw;
    }

    .cert__verify {
        display: flex;
        align-items: flex-end;
        gap: 1.1cqw;
        text-align: left;
    }
    .cert__qr {
        flex: none;
        width: 6.6cqw;
        height: 6.6cqw;
        padding: 0.35cqw;
        background: #fff;
        border: 0.07cqw solid rgba(11, 79, 74, 0.35);
    }
    .cert__qr svg { display: block; width: 100%; height: 100%; }
    .cert__label {
        font-size: 0.7cqw;
        font-weight: 600;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: var(--cert-muted);
    }
    .cert__value {
        margin-top: 0.15cqw !important;
        font-size: 1.02cqw;
        font-weight: 600;
        color: var(--cert-ink);
    }
    .cert__value--code {
        font-family: ui-monospace, 'SFMono-Regular', Consolas, monospace;
        font-size: 0.95cqw;
        letter-spacing: 0.06em;
    }
    .cert__verify-row + .cert__verify-row { margin-top: 0.75cqw; }

    .cert__seal {
        width: 12.5cqw;
        margin-bottom: -0.8cqw;
    }
    .cert__seal svg { display: block; width: 100%; height: auto; }

    .cert__sign {
        justify-self: end;
        width: 19cqw;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .cert__signature {
        max-width: 100%;
        font-family: 'Dancing Script', 'Brush Script MT', cursive;
        font-weight: 700;
        font-size: 2.5cqw;
        line-height: 1.1;
        color: #1D3C6E;
        transform: rotate(-4deg);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .cert__rule {
        margin-top: 0.3cqw;
        width: 100%;
        height: 0.08cqw;
        background: rgba(28, 43, 41, 0.55);
    }
    .cert__sign-name {
        margin-top: 0.45cqw !important;
        font-size: 1cqw;
        font-weight: 700;
        color: var(--cert-ink);
    }
    .cert__sign-role {
        margin-top: 0.1cqw !important;
        font-size: 0.8cqw;
        color: var(--cert-muted);
    }

    .cert__footnote {
        position: absolute;
        left: 0;
        right: 0;
        bottom: 3.05cqw;
        font-size: 0.72cqw;
        letter-spacing: 0.04em;
        color: var(--cert-muted);
    }

    @media print {
        @page { size: A4 landscape; margin: 0; }
        html, body { margin: 0 !important; padding: 0 !important; background: #fff !important; }
        .no-print { display: none !important; }
        .cert-print-area { margin: 0 !important; padding: 0 !important; max-width: none !important; box-shadow: none !important; border: 0 !important; border-radius: 0 !important; }
        .cert-print-area .cert { width: 297mm; height: 209.6mm; aspect-ratio: auto; break-inside: avoid; page-break-inside: avoid; }
    }
</style>
