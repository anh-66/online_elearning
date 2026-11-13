<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Mã OTP gửi qua email
    |--------------------------------------------------------------------------
    |
    | Dùng khi đăng ký tài khoản (xác thực email) và khi quên mật khẩu.
    | Đặt EMAIL_OTP_ENABLED=false để tắt nhanh bước xác thực email
    | (mọi tài khoản được coi như đã xác thực) mà không cần sửa code.
    |
    */

    'enabled' => (bool) env('EMAIL_OTP_ENABLED', true),

    // Số chữ số của mã.
    'length' => 6,

    // Mã hết hạn sau bao nhiêu phút.
    'ttl_minutes' => 10,

    // Nhập sai bao nhiêu lần thì phải xin mã mới.
    'max_attempts' => 5,

    // Phải chờ bao nhiêu giây giữa hai lần gửi mã.
    'resend_seconds' => 60,

    // Mỗi tài khoản nhận tối đa bao nhiêu mã trong một giờ (giữ hạn mức 300 mail/ngày của Brevo).
    'max_sends_per_hour' => 5,

    // Quên mật khẩu: nhập đúng mã xong có bao nhiêu phút để tạo mật khẩu mới.
    'reset_window_minutes' => 10,

];
