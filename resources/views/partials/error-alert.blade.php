<div class="error-alert-wrapper">
    <div class="error-alert" role="alert">
        <div class="error-alert-content">
            <div class="error-alert-icon">
                <svg xmlns="http://www.w3.org/2000/svg"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke-width="1.5"
                     stroke="currentColor"
                     aria-hidden="true">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0118 0Zm-9 3.75h.008v.008H12v-.008Z"></path>
                </svg>
            </div>
            <div class="error-alert-copy">
                <p>Vui lòng thử lại</p>
                <p>{{ $message }}</p>
            </div>
        </div>
        <button type="button"
                class="error-alert-close"
                aria-label="Đóng thông báo lỗi"
                onclick="this.closest('.error-alert-wrapper').remove()">
            <svg xmlns="http://www.w3.org/2000/svg"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke-width="1.5"
                 stroke="currentColor"
                 aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"></path>
            </svg>
        </button>
    </div>
</div>
