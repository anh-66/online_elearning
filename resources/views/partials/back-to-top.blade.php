<button type="button"
        id="back-to-top"
        class="back-to-top"
        aria-label="Lên đầu trang"
        title="Lên đầu trang"
        hidden>
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 19.5v-15m0 0-6.75 6.75M12 4.5l6.75 6.75"></path>
    </svg>
</button>

<script>
    (() => {
        const button = document.getElementById('back-to-top');
        const configuredTarget = document.querySelector('[data-back-to-top-scroll]');
        const usesDocumentScroll = configuredTarget?.tagName === 'BODY';
        const scrollTarget = usesDocumentScroll ? window : configuredTarget;

        if (!button || !scrollTarget) {
            return;
        }

        const updateVisibility = () => {
            const scrollPosition = usesDocumentScroll
                ? window.scrollY
                : scrollTarget.scrollTop;

            button.hidden = scrollPosition < 120;
        };

        scrollTarget.addEventListener('scroll', updateVisibility, { passive: true });
        updateVisibility();

        button.addEventListener('click', () => {
            if (usesDocumentScroll) {
                window.scrollTo({ top: 0, behavior: 'smooth' });
                return;
            }

            scrollTarget.scrollTo({ top: 0, behavior: 'smooth' });
        });
    })();
</script>
