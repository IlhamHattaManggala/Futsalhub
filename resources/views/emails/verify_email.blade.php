<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Alamat Email Anda</title>
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background-color: #f8fafc;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
        }
        .container {
            max-width: 580px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 24px;
            border: 1px solid #e2e8f0;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.025);
        }
        .header {
            background: linear-gradient(135deg, #10b981, #14b8a6);
            padding: 35px 40px;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -0.025em;
        }
        .content {
            padding: 40px;
        }
        .content p {
            color: #475569;
            font-size: 14px;
            line-height: 1.6;
            margin: 0 0 20px 0;
        }
        .content p strong {
            color: #0f172a;
        }
        .button-container {
            text-align: center;
            margin: 30px 0;
        }
        .button {
            display: inline-block;
            background-color: #10b981;
            color: #ffffff !important;
            font-weight: 700;
            font-size: 13px;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2);
            transition: background-color 0.2s;
        }
        .button:hover {
            background-color: #059669;
        }
        .info-box {
            background-color: #f8fafc;
            border-radius: 16px;
            padding: 18px;
            margin-bottom: 24px;
            border: 1px solid #e2e8f0;
            font-size: 12px;
            color: #64748b;
            line-height: 1.5;
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px 40px;
            text-align: center;
            border-top: 1px solid #f1f5f9;
        }
        .footer p {
            margin: 0;
            font-size: 11px;
            color: #94a3b8;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Verifikasi Alamat Email</h1>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $user->name }}</strong>,</p>
            <p>Terima kasih telah mendaftar di platform <strong>{{ $webName }}</strong>. Silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda dan mengaktifkan akun Anda:</p>
            
            <div class="button-container">
                <a href="{{ $url }}" class="button" target="_blank">Verifikasi Alamat Email</a>
            </div>

            <div class="info-box">
                Jika Anda tidak merasa melakukan pendaftaran akun ini, silakan abaikan email ini secara aman. Akun tidak akan aktif sebelum proses verifikasi selesai dilakukan.
            </div>
            
            <p>Salam hangat,<br><strong>Tim {{ $webName }}</strong></p>
        </div>
        <div class="footer">
            <p>Email ini dikirim secara otomatis oleh sistem {{ $webName }}. Mohon tidak membalas email ini secara langsung.</p>
            <p style="margin-top: 6px;">&copy; {{ date('Y') }} {{ $webName }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
