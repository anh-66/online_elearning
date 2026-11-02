{{-- Partial: Áp dụng giao diện sáng/tối trước khi trang hiển thị để tránh nháy màu --}}
<script>
    (() => {
        let theme = null;
        try { theme = localStorage.getItem('theme'); } catch (e) {}
        const dark = theme ? theme === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
        document.documentElement.classList.toggle('dark', dark);
    })();
</script>
