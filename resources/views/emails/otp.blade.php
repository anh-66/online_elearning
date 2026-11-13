<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $heading }} · {{ $appName }}</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f5f5;font-family:'Segoe UI',Roboto,Helvetica,Arial,sans-serif;color:#171717;">
    {{-- Dòng xem trước trong hộp thư --}}
    <div style="display:none;max-height:0;overflow:hidden;">Mã của bạn là {{ $code }}, có hiệu lực {{ $ttlMinutes }} phút.</div>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f5f5;padding:32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:480px;background-color:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e5e5e5;">
                    <tr>
                        <td style="background-color:#019D91;padding:20px 28px;">
                            <span style="font-size:20px;font-weight:700;color:#ffffff;letter-spacing:-0.2px;">{{ $appName }}</span>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:28px 28px 8px;">
                            <h1 style="margin:0 0 12px;font-size:20px;line-height:28px;font-weight:700;color:#171717;">{{ $heading }}</h1>
                            <p style="margin:0 0 4px;font-size:15px;line-height:24px;color:#404040;">Xin chào {{ $user->name }},</p>
                            <p style="margin:0;font-size:15px;line-height:24px;color:#404040;">{{ $intro }}</p>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" style="padding:20px 28px;">
                            <div style="display:inline-block;padding:14px 24px;border-radius:12px;background-color:#e6f5f4;border:1px solid #b3e2de;font-size:34px;line-height:40px;font-weight:700;letter-spacing:10px;color:#017a71;font-family:'Courier New',Courier,monospace;">{{ $code }}</div>
                            <p style="margin:12px 0 0;font-size:13px;line-height:20px;color:#737373;">Mã có hiệu lực trong {{ $ttlMinutes }} phút.</p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:8px 28px 28px;">
                            <p style="margin:0;font-size:13px;line-height:20px;color:#737373;">Không chia sẻ mã này cho bất kỳ ai, kể cả người tự nhận là nhân viên {{ $appName }}. Nếu bạn không yêu cầu mã này, hãy bỏ qua email.</p>
                        </td>
                    </tr>
                </table>
                <p style="margin:16px 0 0;font-size:12px;line-height:18px;color:#a3a3a3;">Email tự động từ {{ $appName }}, vui lòng không trả lời.</p>
            </td>
        </tr>
    </table>
</body>
</html>
