{{-- Khung chat AI tư vấn khóa học. Chỉ hiện khi đã cấu hình GEMINI_API_KEY. --}}
@if (filled(config('services.gemini.key')))
<div id="course-chat" data-endpoint="{{ route('course-chat.send') }}">
    <button type="button"
            id="course-chat-toggle"
            class="fixed bottom-4 right-4 sm:bottom-6 sm:right-6 z-[65] inline-flex h-14 w-14 items-center justify-center rounded-full bg-[#019D91] text-white shadow-lg shadow-[#019D91]/30 transition-all duration-200 hover:-translate-y-0.5 hover:bg-[#008b80] focus:outline-none focus-visible:ring-4 focus-visible:ring-[#75cfca]"
            aria-controls="course-chat-panel"
            aria-expanded="false"
            title="Chat với trợ lý AI">
        <svg data-chat-icon="open" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
        </svg>
        <svg data-chat-icon="close" class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
        </svg>
        <span class="sr-only">Mở chat tư vấn khóa học</span>
    </button>

    <section id="course-chat-panel"
             class="fixed inset-x-4 bottom-20 sm:inset-x-auto sm:right-6 sm:bottom-24 z-[65] flex h-[min(560px,calc(100dvh-7rem))] flex-col overflow-hidden rounded-2xl border border-[#E5E5EA] bg-white text-[#1D1D1F] shadow-2xl sm:w-[380px] dark:border-[#30363d] dark:bg-[#161b22] dark:text-[#e6edf3]"
             role="dialog"
             aria-label="Chat tư vấn khóa học"
             hidden>
        <header class="flex items-center gap-3 bg-[#019D91] px-4 py-3 text-white">
            <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-white/20">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456Z" />
                </svg>
            </span>
            <div class="min-w-0 flex-1">
                <p class="text-[15px] font-bold leading-tight">Trợ lý {{ config('app.name', 'EduLearn') }}</p>
                <p class="text-xs text-white/80">AI tư vấn khóa học</p>
            </div>
            <button type="button" id="course-chat-reset" class="rounded-lg p-1.5 text-white/90 transition-colors hover:bg-white/15 hover:text-white" title="Cuộc trò chuyện mới">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                </svg>
                <span class="sr-only">Cuộc trò chuyện mới</span>
            </button>
            <button type="button" id="course-chat-close" class="rounded-lg p-1.5 text-white/90 transition-colors hover:bg-white/15 hover:text-white" title="Đóng">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
                <span class="sr-only">Đóng chat</span>
            </button>
        </header>

        <div id="course-chat-messages" class="flex-1 space-y-3 overflow-y-auto px-4 py-4 text-sm leading-relaxed" aria-live="polite"></div>

        <div id="course-chat-suggestions" class="flex flex-wrap gap-2 px-4 pb-3">
            @foreach (['Khóa học nào hợp cho người mới bắt đầu?', 'Có khóa học miễn phí nào không?', 'Khóa nào được đánh giá cao nhất?'] as $suggestion)
                <button type="button" class="rounded-full border border-[#019D91]/40 px-3 py-1.5 text-xs font-medium text-[#017a71] transition-colors hover:bg-[#019D91] hover:text-white dark:text-[#5fd3c9] dark:hover:text-white" data-chat-suggestion>{{ $suggestion }}</button>
            @endforeach
        </div>

        <form id="course-chat-form" class="flex items-end gap-2 border-t border-[#E5E5EA] p-3 dark:border-[#30363d]">
            <label for="course-chat-input" class="sr-only">Câu hỏi của bạn</label>
            <textarea id="course-chat-input"
                      rows="1"
                      maxlength="500"
                      placeholder="Hỏi về khóa học..."
                      class="max-h-32 min-h-[42px] flex-1 resize-none rounded-xl border border-[#D2D2D7] bg-white px-3 py-2.5 text-sm text-[#1D1D1F] placeholder:text-neutral-400 focus:border-[#019D91] focus:ring-[#019D91] dark:border-[#30363d] dark:bg-[#0d1117] dark:text-[#e6edf3]"></textarea>
            <button type="submit"
                    id="course-chat-send"
                    class="inline-flex h-[42px] w-[42px] shrink-0 items-center justify-center rounded-xl bg-[#019D91] text-white transition-colors hover:bg-[#008b80] disabled:cursor-not-allowed disabled:opacity-50"
                    title="Gửi">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.125A59.769 59.769 0 0 1 21.485 12 59.768 59.768 0 0 1 3.27 20.875L5.999 12Zm0 0h7.5" />
                </svg>
                <span class="sr-only">Gửi câu hỏi</span>
            </button>
        </form>
        <p class="px-4 pb-3 text-[11px] leading-snug text-neutral-500 dark:text-neutral-400">AI có thể trả lời chưa chính xác. Đừng nhập mật khẩu hay thông tin cá nhân.</p>
    </section>
</div>

<script>
    (() => {
        const root = document.getElementById('course-chat');

        if (!root) {
            return;
        }

        document.body.classList.add('has-course-chat');

        const endpoint = root.dataset.endpoint;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content ?? '';
        const toggleButton = document.getElementById('course-chat-toggle');
        const panel = document.getElementById('course-chat-panel');
        const messageList = document.getElementById('course-chat-messages');
        const suggestions = document.getElementById('course-chat-suggestions');
        const form = document.getElementById('course-chat-form');
        const input = document.getElementById('course-chat-input');
        const sendButton = document.getElementById('course-chat-send');
        const storageKey = 'course-chat:history';
        const historyLimit = 20;
        const welcomeMessage = @js('Chào bạn! Mình là trợ lý AI của '.config('app.name', 'EduLearn').'. Bạn muốn học gì, mình gợi ý khóa học phù hợp cho bạn nhé.');
        let history = loadHistory();
        let isSending = false;

        function loadHistory() {
            try {
                const saved = JSON.parse(sessionStorage.getItem(storageKey) ?? '[]');

                return Array.isArray(saved)
                    ? saved.filter((item) => ['user', 'assistant'].includes(item?.role) && typeof item.content === 'string')
                    : [];
            } catch {
                return [];
            }
        }

        function saveHistory() {
            try {
                sessionStorage.setItem(storageKey, JSON.stringify(history.slice(-historyLimit)));
            } catch {
                // Trình duyệt chặn sessionStorage: lịch sử chỉ còn trong trang hiện tại.
            }
        }

        function safeHref(url) {
            if (/^\/(?!\/)/.test(url)) {
                return url;
            }

            try {
                const parsed = new URL(url);

                return ['http:', 'https:'].includes(parsed.protocol) ? parsed.href : null;
            } catch {
                return null;
            }
        }

        // Hiển thị link [chữ](url) và **in đậm** bằng DOM (không dùng innerHTML để tránh chèn mã).
        function appendInline(parent, text) {
            const pattern = /\[([^\]]+)\]\(([^)\s]+)\)|\*\*([^*]+)\*\*/g;
            let lastIndex = 0;

            for (const match of text.matchAll(pattern)) {
                if (match.index > lastIndex) {
                    parent.append(text.slice(lastIndex, match.index));
                }

                if (match[1] !== undefined) {
                    const href = safeHref(match[2]);

                    if (href) {
                        const link = document.createElement('a');
                        link.href = href;
                        link.textContent = match[1];
                        link.className = 'font-semibold text-[#017a71] underline decoration-[#019D91]/40 underline-offset-2 hover:decoration-[#019D91] dark:text-[#5fd3c9]';

                        if (!href.startsWith('/') && new URL(href).origin !== window.location.origin) {
                            link.target = '_blank';
                            link.rel = 'noopener noreferrer';
                        }

                        parent.append(link);
                    } else {
                        parent.append(match[1]);
                    }
                } else {
                    const strong = document.createElement('strong');
                    strong.textContent = match[3];
                    parent.append(strong);
                }

                lastIndex = match.index + match[0].length;
            }

            if (lastIndex < text.length) {
                parent.append(text.slice(lastIndex));
            }
        }

        function renderRichText(container, text) {
            let list = null;

            for (const rawLine of text.split(/\r?\n/)) {
                const line = rawLine.trimEnd();
                const bullet = line.match(/^\s*(?:[-*•]|\d+[.)])\s+(.*)$/);

                if (bullet) {
                    if (!list) {
                        list = document.createElement('ul');
                        list.className = 'list-disc space-y-1 pl-5';
                        container.append(list);
                    }

                    const item = document.createElement('li');
                    appendInline(item, bullet[1]);
                    list.append(item);
                    continue;
                }

                list = null;

                if (line.trim() === '') {
                    continue;
                }

                const paragraph = document.createElement('p');
                appendInline(paragraph, line.replace(/^#{1,6}\s+/, ''));
                container.append(paragraph);
            }
        }

        function scrollToBottom() {
            messageList.scrollTop = messageList.scrollHeight;
        }

        function addBubble(role, text, isError = false) {
            const row = document.createElement('div');
            const bubble = document.createElement('div');
            row.className = role === 'user' ? 'flex justify-end' : 'flex justify-start';

            if (role === 'user') {
                bubble.className = 'max-w-[85%] whitespace-pre-wrap break-words rounded-2xl rounded-br-md bg-[#019D91] px-3.5 py-2.5 text-white';
                bubble.textContent = text;
            } else {
                bubble.className = isError
                    ? 'max-w-[85%] break-words rounded-2xl rounded-bl-md border border-red-200 bg-red-50 px-3.5 py-2.5 text-red-700 dark:border-red-900/60 dark:bg-red-950/40 dark:text-red-300'
                    : 'max-w-[85%] space-y-2 break-words rounded-2xl rounded-bl-md bg-[#F5F5F7] px-3.5 py-2.5 dark:bg-[#21262d]';
                renderRichText(bubble, text);
            }

            row.append(bubble);
            messageList.append(row);
            scrollToBottom();

            return row;
        }

        function addTypingIndicator() {
            const row = document.createElement('div');
            row.className = 'flex justify-start';
            row.setAttribute('aria-label', 'AI đang trả lời');
            row.innerHTML = '<div class="flex items-center gap-1 rounded-2xl rounded-bl-md bg-[#F5F5F7] px-4 py-3.5 dark:bg-[#21262d]">'
                + '<span class="h-2 w-2 animate-bounce rounded-full bg-[#019D91]"></span>'
                + '<span class="h-2 w-2 animate-bounce rounded-full bg-[#019D91] [animation-delay:150ms]"></span>'
                + '<span class="h-2 w-2 animate-bounce rounded-full bg-[#019D91] [animation-delay:300ms]"></span>'
                + '</div>';
            messageList.append(row);
            scrollToBottom();

            return row;
        }

        function renderConversation() {
            messageList.replaceChildren();
            addBubble('assistant', welcomeMessage);
            history.forEach((item) => addBubble(item.role, item.content));
            suggestions.hidden = history.length > 0;
        }

        function resizeInput() {
            input.style.height = 'auto';
            input.style.height = `${input.scrollHeight}px`;
        }

        function setOpen(isOpen) {
            panel.hidden = !isOpen;
            toggleButton.setAttribute('aria-expanded', String(isOpen));
            toggleButton.querySelector('[data-chat-icon="open"]').classList.toggle('hidden', isOpen);
            toggleButton.querySelector('[data-chat-icon="close"]').classList.toggle('hidden', !isOpen);

            if (isOpen) {
                scrollToBottom();
                input.focus();
            }
        }

        async function sendMessage(text) {
            const message = text.trim();

            if (!message || isSending) {
                return;
            }

            isSending = true;
            sendButton.disabled = true;
            suggestions.hidden = true;

            const previousMessages = history.slice(-8);
            addBubble('user', message);
            input.value = '';
            resizeInput();

            const typingIndicator = addTypingIndicator();

            try {
                const response = await fetch(endpoint, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({ message, history: previousMessages }),
                });
                const data = await response.json().catch(() => ({}));
                typingIndicator.remove();

                if (response.ok && data.reply) {
                    // Chỉ lưu vào lịch sử khi AI trả lời thành công, để câu hỏi lỗi không bị gửi lại.
                    history.push({ role: 'user', content: message }, { role: 'assistant', content: data.reply });
                    saveHistory();
                    addBubble('assistant', data.reply);

                    return;
                }

                addBubble('assistant', response.status === 419
                    ? 'Phiên làm việc đã hết hạn. Bạn tải lại trang rồi hỏi lại nhé.'
                    : (data.message || 'Có lỗi xảy ra, bạn thử lại sau nhé.'), true);
            } catch {
                typingIndicator.remove();
                addBubble('assistant', 'Không kết nối được máy chủ. Bạn kiểm tra mạng rồi thử lại nhé.', true);
            } finally {
                isSending = false;
                sendButton.disabled = false;
                input.focus();
            }
        }

        toggleButton.addEventListener('click', () => setOpen(panel.hidden));
        document.getElementById('course-chat-close').addEventListener('click', () => setOpen(false));
        document.getElementById('course-chat-reset').addEventListener('click', () => {
            history = [];
            saveHistory();
            renderConversation();
            input.focus();
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !panel.hidden) {
                setOpen(false);
                toggleButton.focus();
            }
        });

        suggestions.addEventListener('click', (event) => {
            const button = event.target.closest('[data-chat-suggestion]');

            if (button) {
                sendMessage(button.textContent);
            }
        });

        form.addEventListener('submit', (event) => {
            event.preventDefault();
            sendMessage(input.value);
        });

        input.addEventListener('input', resizeInput);
        input.addEventListener('keydown', (event) => {
            // Enter để gửi, Shift + Enter để xuống dòng.
            if (event.key === 'Enter' && !event.shiftKey && !event.isComposing) {
                event.preventDefault();
                form.requestSubmit();
            }
        });

        renderConversation();
    })();
</script>
@endif
