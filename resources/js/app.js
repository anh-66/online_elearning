import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

// Global Animate UI Toast Dispatcher
window.toast = {
    show(detail) {
        window.dispatchEvent(new CustomEvent('toast', { detail }));
    },
    success(message, title = 'Thành công', action = null) {
        this.show({ type: 'success', title, description: message, action });
    },
    error(message, title = 'Có lỗi xảy ra', action = null) {
        this.show({ type: 'error', title, description: message, action });
    },
    info(message, title = 'Thông báo', action = null) {
        this.show({ type: 'info', title, description: message, action });
    },
    warning(message, title = 'Cảnh báo', action = null) {
        this.show({ type: 'warning', title, description: message, action });
    },
    loading(message, title = 'Đang xử lý...') {
        this.show({ type: 'loading', title, description: message, duration: 8000 });
    }
};

Alpine.data('animateUiToast', () => ({
    toasts: [],
    addToast(detail) {
        const id = Date.now() + Math.random();
        const desc = typeof detail === 'string' ? detail : (detail.description || '');
        const duration = Number(detail.duration) || 4000;
        
        const toast = {
            id,
            type: detail.type || 'info',
            title: detail.title || (detail.type === 'success' ? 'Thành công' : (detail.type === 'error' ? 'Lỗi' : 'Thông báo')),
            description: desc,
            action: detail.action || null,
            duration,
            remaining: duration,
            startTime: Date.now(),
            timer: null,
            isPaused: false,
            visible: true
        };

        toast.timer = setTimeout(() => {
            this.close(id);
        }, duration);

        this.toasts.unshift(toast);

        if (this.toasts.length > 5) {
            const oldest = this.toasts.pop();
            if (oldest && oldest.timer) clearTimeout(oldest.timer);
        }
    },
    pause(toast) {
        if (!toast || toast.isPaused) return;
        toast.isPaused = true;
        if (toast.timer) {
            clearTimeout(toast.timer);
            toast.timer = null;
        }
        const elapsed = Date.now() - toast.startTime;
        toast.remaining = Math.max(0, toast.remaining - elapsed);
    },
    resume(toast) {
        if (!toast || !toast.isPaused) return;
        toast.isPaused = false;
        toast.startTime = Date.now();
        if (toast.remaining > 0) {
            toast.timer = setTimeout(() => {
                this.close(toast.id);
            }, toast.remaining);
        } else {
            this.close(toast.id);
        }
    },
    close(id) {
        const item = this.toasts.find(t => t.id === id);
        if (item) {
            if (item.timer) clearTimeout(item.timer);
            item.visible = false;
            setTimeout(() => {
                this.toasts = this.toasts.filter(t => t.id !== id);
            }, 250);
        }
    }
}));

Alpine.start();
