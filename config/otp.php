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

    // Thời gian chờ trước khi được gửi lại, tăng dần theo số mã đã gửi trong giờ:
    // sau mã thứ 1 chờ 60 giây, mã thứ 2 chờ 2 phút, mã thứ 3 chờ 5 phút, từ mã thứ 4 chờ 10 phút.
    'resend_wait_seconds' => [60, 120, 300, 600],

    // Mỗi tài khoản (hoặc mỗi email khi quên mật khẩu) nhận tối đa bao nhiêu mã trong một giờ.
    'max_sends_per_hour' => 5,

    // Mỗi địa chỉ IP được bấm xin mã / gửi lại mã tối đa bao nhiêu lần trong một giờ (chặn spam nhiều email từ một máy).
    // Để rộng một chút vì cả phòng máy hay dùng chung một IP.
    'max_requests_per_ip_per_hour' => 20,

    // Toàn web gửi tối đa bao nhiêu mail OTP mỗi ngày (gói Free của Brevo là 300 mail/ngày).
    'max_sends_per_day' => (int) env('EMAIL_OTP_MAX_PER_DAY', 250),

    // Quên mật khẩu: nhập đúng mã xong có bao nhiêu phút để tạo mật khẩu mới.
    'reset_window_minutes' => 10,

];
