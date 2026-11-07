{{-- Partial: Áp dụng giao diện sáng/tối trước khi trang hiển thị để tránh nháy màu --}}
<script>
    (() => {
        let theme = null;
        try { theme = localStorage.getItem('theme'); } catch (e) {}
        // Mặc định là 'light'. Chỉ kích hoạt dark mode nếu người dùng đã chủ động chọn 'dark'
        // hoặc chọn 'system' và hệ điều hành/trình duyệt đang ở dark mode.
        const dark = theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
        document.documentElement.classList.toggle('dark', dark);
    })();
</script>
