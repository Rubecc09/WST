<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NetPass membership application received</title>
</head>
<body style="margin:0;padding:0;background-color:#0b0c10;color:#e8edf2;font-family:Arial,Helvetica,sans-serif;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" bgcolor="#0b0c10" style="width:100%;background-color:#0b0c10;">
        <tr><td align="center" style="padding:32px 12px;">
            <!--[if mso]><table role="presentation" width="600"><tr><td><![endif]-->
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="width:100%;max-width:600px;border:1px solid #303746;background-color:#141720;" bgcolor="#141720">
                <tr><td style="padding:28px 24px;border-top:4px solid #fcee0a;border-bottom:1px solid #303746;">
                    <div style="font-family:Arial,Helvetica,sans-serif;font-size:32px;font-weight:800;letter-spacing:2px;line-height:40px;color:#fcee0a;">NET<span style="color:#00f0ff;">PASS</span></div>
                    <div style="margin-top:6px;font-size:11px;letter-spacing:2px;line-height:18px;color:#aeb9c9;">MEMBERSHIP APPLICATION</div>
                </td></tr>
                <tr><td style="padding:28px 24px;color:#e8edf2;">
                    <h1 style="margin:0 0 22px;font-size:22px;line-height:30px;color:#00f0ff;font-weight:700;">{{ $greeting }}</h1>
                    @foreach ($introLines as $line)
                        <p style="margin:0 0 16px;font-size:16px;line-height:26px;color:#e8edf2;overflow-wrap:break-word;word-break:break-word;">{{ $line }}</p>
                    @endforeach
                    @foreach ($outroLines as $line)
                        <p style="margin:0 0 16px;font-size:16px;line-height:26px;color:#e8edf2;">{{ $line }}</p>
                    @endforeach
                    <p style="margin:26px 0 0;padding-top:20px;border-top:1px solid #303746;font-size:16px;line-height:26px;color:#e8edf2;">Regards,<br><strong style="color:#fcee0a;">NetPass Team</strong></p>
                </td></tr>
                <tr><td align="center" style="padding:20px 24px;border-top:1px solid #303746;background-color:#101219;color:#aeb9c9;font-size:12px;line-height:20px;">&copy; {{ date('Y') }} NetPass. All rights reserved.</td></tr>
            </table>
            <!--[if mso]></td></tr></table><![endif]-->
        </td></tr>
    </table>
</body>
</html>
