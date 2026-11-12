<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Chào mừng bạn đến với {{ config('app.name', 'EduLearn') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=be-vietnam-pro:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Be Vietnam Pro', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* Mũi nhọn bong bóng thoại trỏ sang mascot (Desktop) */
        @media (min-width: 768px) {
            .speech-bubble-tail {
                position: absolute;
                left: -12px;
                top: 42px;
                width: 0;
                height: 0;
                border-top: 10px solid transparent;
                border-bottom: 10px solid transparent;
                border-right: 14px solid #eef4ff;
            }
        }

        /* Mũi nhọn bong bóng thoại trỏ lên mascot (Mobile) */
        @media (max-width: 767px) {
            .speech-bubble-tail {
                position: absolute;
                top: -12px;
                left: 50%;
                transform: translateX(-50%);
                width: 0;
                height: 0;
                border-left: 10px solid transparent;
                border-right: 10px solid transparent;
                border-bottom: 14px solid #eef4ff;
            }
        }

        /* Phóng to linh vật để hiển thị to rõ, cân đối với khung thoại */
        #mascot-container svg {
            width: 100% !important;
            height: 100% !important;
            transform: scale(var(--mascot-scale, 1.25));
            transform-origin: center center;
            overflow: visible !important;
            transition: transform 0.3s ease;
        }

        .step-transition {
            transition: opacity 0.25s ease, transform 0.25s ease;
        }
    </style>
</head>
<body class="h-screen w-screen overflow-hidden bg-white text-gray-900 antialiased flex flex-col justify-between selection:bg-[#019D91] selection:text-white">

    <!-- Top Header Bar -->
    <header class="w-full max-w-7xl mx-auto px-6 sm:px-10 py-3 sm:py-4 flex items-center justify-between shrink-0">
        <!-- Logo bên trái -->
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2.5 group shrink-0" aria-label="{{ config('app.name', 'EduLearn') }}">
            <svg class="h-7 w-7 text-[#019D91] transition-transform duration-200 group-hover:scale-105" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
            </svg>
            <span class="text-xl sm:text-2xl font-bold tracking-tight text-gray-900">EduLearn</span>
        </a>

        <!-- Thanh tiến trình ở giữa: < [===      ] -->
        <div class="flex items-center gap-3">
            <button type="button"
                    id="btn-back"
                    onclick="prevStep()"
                    disabled
                    aria-label="Quay lại bước trước"
                    class="p-1 rounded-full text-gray-300 cursor-not-allowed hover:bg-gray-100 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                </svg>
            </button>
            <div class="w-32 sm:w-56 h-2 bg-gray-200 rounded-full overflow-hidden flex">
                <div id="progress-bar-fill" class="w-1/4 h-full bg-[#019D91] rounded-full transition-all duration-500 ease-out"></div>
            </div>
            <span id="step-indicator" class="text-xs font-semibold text-gray-400 select-none">1/4</span>
        </div>

        <!-- Nút Bỏ qua bên phải -->
        <form method="POST" action="{{ route('onboarding.complete') }}" class="shrink-0">
            @csrf
            <button type="submit" class="text-sm font-medium text-gray-400 hover:text-gray-700 transition-colors py-1 px-2.5 rounded-md hover:bg-gray-100">
                Bỏ qua
            </button>
        </form>
    </header>

    <!-- Main Content: Mascot & Lời chào (Vừa vặn không bị cuộn trang) -->
    <main class="flex-1 flex items-center justify-center px-4 sm:px-8 py-2 min-h-0">
        <div class="max-w-4xl w-full flex flex-col md:flex-row items-center justify-center gap-4 sm:gap-8 md:gap-12">

            <!-- Mascot Lottie Player (Kích thước to rõ, nổi bật) -->
            <div class="flex items-center justify-center shrink-0">
                <div id="mascot-container"
                     class="w-64 sm:w-80 md:w-[380px] lg:w-[440px] max-h-[52vh] aspect-square flex items-center justify-center overflow-visible step-transition">
                    {{-- Spinner hiển thị trong lúc tải file animation --}}
                    <div id="mascot-loading" class="flex flex-col items-center gap-3 text-gray-400 text-sm">
                        <svg class="animate-spin h-7 w-7 text-[#019D91]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Đang tải...</span>
                    </div>
                </div>
            </div>

            <!-- Speech Bubble & Lời thoại thuần Tiếng Việt -->
            <div class="w-full max-w-md flex flex-col items-start step-transition" id="bubble-container">
                {{-- Khung hội thoại bong bóng màu xanh nhạt --}}
                <div class="relative w-full bg-[#eef4ff] rounded-3xl p-5 sm:p-7 shadow-xs">
                    {{-- Mũi nhọn bong bóng trỏ sang mascot --}}
                    <div class="speech-bubble-tail"></div>

                    <h1 id="bubble-heading" class="text-xl sm:text-2xl font-bold text-gray-900 leading-snug tracking-tight">
                        {{ $greeting ?? 'Xin chào!' }}<br>
                        <span id="bubble-subheading" class="text-lg sm:text-xl font-semibold text-gray-800">Mình là Lynx, người bạn đồng hành của bạn.</span>
                    </h1>

                    <p id="bubble-body" class="mt-2 text-sm sm:text-base text-gray-600 leading-relaxed">
                        Rất vui vì bạn đã bắt đầu hành trình học tập cùng EduLearn!
                    </p>
                </div>

                {{-- Dòng chữ hướng dẫn phụ bên dưới khung bong bóng --}}
                <div class="mt-3 sm:mt-4 px-1">
                    <p id="bubble-hint" class="text-gray-500 text-sm sm:text-base leading-relaxed">
                        Hãy cùng chuẩn bị mọi thứ sẵn sàng trong vài bước đơn giản nhé. Bấm <strong class="text-gray-700 font-semibold">"Tiếp tục"</strong> để bắt đầu.
                    </p>
                </div>
            </div>

        </div>
    </main>

    <!-- Bottom Action Bar: Nút Tiếp tục / Hoàn tất (Luôn hiển thị không cần cuộn) -->
    <footer class="w-full max-w-7xl mx-auto px-6 sm:px-10 py-3 sm:py-5 flex flex-col items-center justify-center shrink-0">
        <form id="onboarding-form" method="POST" action="{{ route('onboarding.complete') }}" class="w-full flex flex-col items-center">
            @csrf
            <button type="button"
                    id="btn-action"
                    onclick="handleNext()"
                    class="inline-flex items-center justify-center min-w-[160px] sm:min-w-[190px] px-8 py-3 bg-[#019D91] hover:bg-[#00877c] active:bg-[#007369] text-white font-semibold text-base rounded-md shadow-xs hover:shadow transition-all duration-200 transform active:scale-[0.98]">
                Tiếp tục
            </button>
        </form>
    </footer>

    <!-- Lottie Web Animation Script -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>
    <script>
        // Danh sách 4 bước Onboarding tương ứng 4 mascot:
        // 1. mascot0.json: Vẫy tay chào mừng
        // 2. mascot1.json: Khám phá kho tàng khóa học
        // 3. happy_mascot.json: Học tương tác & làm bài tập
        // 4. winner.json: Nâng cúp vô địch / Sẵn sàng bứt phá
        const ONBOARDING_STEPS = [
            {
                mascotUrl: '{{ asset('animations/mascots/mascot0.json') }}',
                progress: '25%',
                indicator: '1/4',
                headingHtml: '{{ addslashes($greeting ?? 'Xin chào!') }}<br><span class="text-lg sm:text-xl font-semibold text-gray-800">Mình là Lynx, người bạn đồng hành của bạn.</span>',
                body: 'Rất vui vì bạn đã bắt đầu hành trình học tập cùng EduLearn!',
                hintHtml: 'Hãy cùng chuẩn bị mọi thứ sẵn sàng trong vài bước đơn giản nhé. Bấm <strong class="text-gray-700 font-semibold">"Tiếp tục"</strong> để bắt đầu.',
                buttonText: 'Tiếp tục',
                isFinal: false,
                scale: 1.25
            },
            {
                mascotUrl: '{{ asset('animations/mascots/mascot1.json') }}',
                progress: '50%',
                indicator: '2/4',
                headingHtml: 'Khám phá kho tàng khóa học<br><span class="text-lg sm:text-xl font-semibold text-gray-800">Hàng trăm bài giảng chất lượng cao.</span>',
                body: 'Từ Lập trình Web, AI, Thiết kế UI/UX đến Kỹ năng mềm được biên soạn kỹ lưỡng từ các giảng viên hàng đầu.',
                hintHtml: 'Học tập linh hoạt theo tốc độ của riêng bạn với video sắc nét, tài liệu phong phú và bài tập thực chiến.',
                buttonText: 'Tiếp tục',
                isFinal: false,
                scale: 1.25
            },
            {
                mascotUrl: '{{ asset('animations/mascots/happy_mascot.json') }}',
                progress: '75%',
                indicator: '3/4',
                headingHtml: 'Học tập tương tác & Ghi nhận tiến độ<br><span class="text-lg sm:text-xl font-semibold text-gray-800">Học đi đôi với hành.</span>',
                body: 'Làm bài kiểm tra trắc nghiệm thông minh, thảo luận hỏi đáp trực tiếp và nhận chứng chỉ hoàn thành danh giá.',
                hintHtml: 'Hệ thống tự động lưu mốc học tập để bạn dễ dàng tiếp tục bài giảng bất kỳ lúc nào trên mọi thiết bị.',
                buttonText: 'Tiếp tục',
                isFinal: false,
                scale: 1.25
            },
            {
                mascotUrl: '{{ asset('animations/mascots/Winner.json') }}',
                progress: '100%',
                indicator: '4/4',
                headingHtml: 'Bạn đã sẵn sàng bứt phá!<br><span class="text-lg sm:text-xl font-semibold text-gray-800">Mọi thứ đã được chuẩn bị hoàn hảo.</span>',
                body: 'Chúc bạn có một hành trình học tập thật hứng khởi, tích lũy nhiều kiến thức bổ ích và đạt được mọi mục tiêu!',
                hintHtml: 'Bấm <strong class="text-gray-700 font-semibold">"Bắt đầu học ngay"</strong> để khám phá các khóa học đầu tiên của bạn.',
                buttonText: 'Bắt đầu học ngay',
                isFinal: true,
                scale: 1.15
            }
        ];

        let currentStepIndex = 0;
        let currentAnimation = null;
        const preloadedData = {};

        const mascotContainer = document.getElementById('mascot-container');
        const mascotLoading = document.getElementById('mascot-loading');
        const bubbleContainer = document.getElementById('bubble-container');
        const bubbleHeading = document.getElementById('bubble-heading');
        const bubbleBody = document.getElementById('bubble-body');
        const bubbleHint = document.getElementById('bubble-hint');
        const btnBack = document.getElementById('btn-back');
        const btnAction = document.getElementById('btn-action');
        const progressBarFill = document.getElementById('progress-bar-fill');
        const stepIndicator = document.getElementById('step-indicator');
        const onboardingForm = document.getElementById('onboarding-form');

        // Tải trước dữ liệu animation vào bộ nhớ để chuyển bước tức thì
        function preloadAllMascots() {
            ONBOARDING_STEPS.forEach((step, idx) => {
                fetch(step.mascotUrl)
                    .then(res => res.json())
                    .then(data => {
                        preloadedData[idx] = data;
                    })
                    .catch(err => console.warn('Không tải trước được mascot ' + idx, err));
            });
        }

        // Tải và hiển thị mascot theo bước
        function renderMascot(stepIndex) {
            const step = ONBOARDING_STEPS[stepIndex];

            // Thiết lập tỷ lệ scale cho SVG
            document.documentElement.style.setProperty('--mascot-scale', step.scale || 1.25);

            if (currentAnimation) {
                currentAnimation.destroy();
                currentAnimation = null;
            }

            try {
                const animConfig = {
                    container: mascotContainer,
                    renderer: 'svg',
                    loop: true,
                    autoplay: true
                };

                // Dùng dữ liệu đã cache nếu có, hoặc tải theo URL
                if (preloadedData[stepIndex]) {
                    animConfig.animationData = preloadedData[stepIndex];
                } else {
                    animConfig.path = step.mascotUrl;
                }

                currentAnimation = lottie.loadAnimation(animConfig);

                currentAnimation.addEventListener('DOMLoaded', function () {
                    if (mascotLoading) mascotLoading.style.display = 'none';
                });

                currentAnimation.addEventListener('data_failed', function () {
                    if (mascotLoading) {
                        mascotLoading.innerHTML = '<span class="text-red-500 text-sm">Không thể tải hoạt ảnh</span>';
                    }
                });
            } catch (err) {
                console.error('Lỗi Lottie:', err);
                if (mascotLoading) {
                    mascotLoading.innerHTML = '<span class="text-amber-500 text-sm">Không hỗ trợ animation</span>';
                }
            }
        }

        // Cập nhật giao diện nội dung của bước hiện tại
        function updateStepUI(stepIndex) {
            const step = ONBOARDING_STEPS[stepIndex];

            // Hiệu ứng mờ nhẹ khi chuyển bước
            bubbleContainer.style.opacity = '0';
            mascotContainer.style.opacity = '0';

            setTimeout(() => {
                // Cập nhật text & tiến trình
                bubbleHeading.innerHTML = step.headingHtml;
                bubbleBody.innerText = step.body;
                bubbleHint.innerHTML = step.hintHtml;
                progressBarFill.style.width = step.progress;
                stepIndicator.innerText = step.indicator;

                // Cập nhật trạng thái nút Quay lại
                if (stepIndex === 0) {
                    btnBack.disabled = true;
                    btnBack.classList.remove('text-gray-600', 'cursor-pointer');
                    btnBack.classList.add('text-gray-300', 'cursor-not-allowed');
                } else {
                    btnBack.disabled = false;
                    btnBack.classList.remove('text-gray-300', 'cursor-not-allowed');
                    btnBack.classList.add('text-gray-600', 'cursor-pointer');
                }

                // Cập nhật nút Hành động
                btnAction.innerText = step.buttonText;
                if (step.isFinal) {
                    btnAction.classList.remove('bg-[#019D91]', 'hover:bg-[#00877c]');
                    btnAction.classList.add('bg-emerald-600', 'hover:bg-emerald-700', 'shadow-md');
                } else {
                    btnAction.classList.remove('bg-emerald-600', 'hover:bg-emerald-700', 'shadow-md');
                    btnAction.classList.add('bg-[#019D91]', 'hover:bg-[#00877c]');
                }

                // Chạy hoạt ảnh mascot mới
                renderMascot(stepIndex);

                // Khôi phục hiển thị
                bubbleContainer.style.opacity = '1';
                mascotContainer.style.opacity = '1';
            }, 150);
        }

        // Xử lý khi nhấn nút Tiếp tục / Bắt đầu học
        function handleNext() {
            if (currentStepIndex < ONBOARDING_STEPS.length - 1) {
                currentStepIndex++;
                updateStepUI(currentStepIndex);
            } else {
                // Bước cuối: gửi form hoàn thành onboarding
                onboardingForm.submit();
            }
        }

        // Xử lý khi nhấn nút Quay lại
        function prevStep() {
            if (currentStepIndex > 0) {
                currentStepIndex--;
                updateStepUI(currentStepIndex);
            }
        }

        // Điều khiển phím mũi tên và Enter tiện lợi
        document.addEventListener('keydown', function (e) {
            if (e.key === 'ArrowRight' || e.key === 'Enter') {
                e.preventDefault();
                handleNext();
            } else if (e.key === 'ArrowLeft') {
                e.preventDefault();
                prevStep();
            }
        });

        // Khởi động khi tải xong trang
        document.addEventListener('DOMContentLoaded', function () {
            preloadAllMascots();
            renderMascot(0);
        });
    </script>
</body>
</html>
