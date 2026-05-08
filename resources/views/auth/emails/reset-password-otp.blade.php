<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - Rantangku</title>
</head>

<body style="font-family: 'Instrument Sans', Arial, sans-serif; background: #fff7ed; padding: 20px; margin: 0;">

    <div
        style="max-width: 520px; margin: auto; background: white; padding: 32px 28px; border-radius: 16px; border: 1px solid #fed7aa;">

        <!-- Logo -->
        <div style="text-align: center; margin-bottom: 24px;">
            <div
                style="display: inline-block; width: 44px; height: 44px; background: #f97316; border-radius: 12px; line-height: 44px; text-align: center;">
                <span style="color: white; font-size: 22px; font-weight: 700;">R</span>
            </div>
            <div style="font-size: 20px; font-weight: 700; color: #1e293b; margin-top: 8px;">Rantangku</div>
        </div>

        <!-- Greeting -->
        <h2 style="color: #1e293b; margin-bottom: 8px; font-size: 20px; font-weight: 600;">
            Halo, {{ $name }} 👋
        </h2>

        <p style="color: #64748b; font-size: 14px; line-height: 1.6; margin-bottom: 20px;">
            Kami menerima permintaan untuk mengatur ulang kata sandi akun Rantangku kamu. Gunakan kode OTP berikut:
        </p>

        <!-- OTP Code -->
        <div style="margin: 28px 0; text-align: center;">
            <div
                style="
                display: inline-block;
                font-size: 32px;
                font-weight: 700;
                letter-spacing: 8px;
                color: #f97316;
                background: #fff7ed;
                padding: 14px 28px;
                border-radius: 12px;
                border: 2px dashed #fed7aa;
            ">
                {{ $otp }}
            </div>
        </div>

        <!-- Warning Box -->
        <div
            style="background: #fef2f2; border: 1px solid #fecaca; border-radius: 10px; padding: 12px 16px; margin-bottom: 20px;">
            <table>
                <tr>
                    <td style="vertical-align: top; padding-right: 10px;">
                        <span style="font-size: 16px;">⚠️</span>
                    </td>
                    <td>
                        <p style="color: #991b1b; font-size: 13px; margin: 0; line-height: 1.5;">
                            <strong>Penting:</strong> Kode ini hanya berlaku <strong>15 menit</strong>. Jangan berikan
                            kode ini kepada siapa pun, termasuk pihak yang mengaku dari Rantangku.
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Info Box -->
        <div
            style="background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: 12px 16px; margin-bottom: 24px;">
            <table>
                <tr>
                    <td style="vertical-align: top; padding-right: 10px;">
                        <span style="font-size: 16px;">🔒</span>
                    </td>
                    <td>
                        <p style="color: #475569; font-size: 13px; margin: 0; line-height: 1.5;">
                            Demi keamanan akun kamu, setelah password di-reset, semua sesi yang sedang aktif akan
                            otomatis logout.
                        </p>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Steps -->
        <div style="margin-bottom: 24px;">
            <p style="color: #475569; font-size: 13px; font-weight: 600; margin-bottom: 10px;">Langkah selanjutnya:</p>
            <table style="width: 100%;">
                <tr>
                    <td style="width: 28px; vertical-align: top; padding-bottom: 8px;">
                        <div
                            style="width: 22px; height: 22px; background: #f97316; color: white; border-radius: 50%; text-align: center; line-height: 22px; font-size: 12px; font-weight: 700;">
                            1</div>
                    </td>
                    <td style="font-size: 13px; color: #475569; padding-bottom: 8px;">Masukkan kode OTP di halaman reset
                        password</td>
                </tr>
                <tr>
                    <td style="width: 28px; vertical-align: top; padding-bottom: 8px;">
                        <div
                            style="width: 22px; height: 22px; background: #f97316; color: white; border-radius: 50%; text-align: center; line-height: 22px; font-size: 12px; font-weight: 700;">
                            2</div>
                    </td>
                    <td style="font-size: 13px; color: #475569; padding-bottom: 8px;">Buat kata sandi baru yang kuat
                    </td>
                </tr>
                <tr>
                    <td style="width: 28px; vertical-align: top;">
                        <div
                            style="width: 22px; height: 22px; background: #f97316; color: white; border-radius: 50%; text-align: center; line-height: 22px; font-size: 12px; font-weight: 700;">
                            3</div>
                    </td>
                    <td style="font-size: 13px; color: #475569;">Login dengan kata sandi baru kamu</td>
                </tr>
            </table>
        </div>

        <!-- Divider -->
        <hr style="margin: 24px 0; border: none; border-top: 1px solid #f1f5f9;">

        <!-- Didn't request -->
        <p style="color: #94a3b8; font-size: 12px; line-height: 1.5;">
            Jika kamu <strong>tidak</strong> meminta reset password, abaikan email ini. Akun kamu tetap aman dan tidak
            ada perubahan yang dilakukan.
        </p>

    </div>

    <!-- Footer -->
    <div style="max-width: 520px; margin: 16px auto; text-align: center;">
        <p style="font-size: 11px; color: #94a3b8; margin: 0;">
            © {{ date('Y') }} Rantangku. All rights reserved.
        </p>
        <p style="font-size: 11px; color: #cbd5e1; margin: 4px 0 0;">
            Food delivery made easy 🍜
        </p>
    </div>

</body>

</html>
