<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="color-scheme" content="light">
  <title>Verifikasi Email - Orbii</title>
</head>

<body
  style="margin:0;padding:0;background-color:#fafafa;font-family:'Figtree',Helvetica,Arial,sans-serif;-webkit-text-size-adjust:100%;">

  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
    style="background-color:#fafafa;">
    <tr>
      <td align="center" style="padding:40px 16px;">

        <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0"
          style="width:100%;max-width:600px;background-color:#ffffff;border-radius:16px;border:1px solid #e6e6e6;overflow:hidden;">

          <tr>
            <td height="4"
              style="height:4px;line-height:4px;font-size:0;background-color:#476cff;background-image:linear-gradient(90deg,#476cff,#62aef0);">
              &nbsp;
            </td>
          </tr>

          <!-- Header -->
          <tr>
            <td align="center" style="background-color:#ffffff;padding:32px 30px 24px;">

              <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                <tr>

                  <!-- Logo Orbii -->
                  <td valign="middle" style="padding-right:12px;">
                    <img src="{{ asset('images/orbii-v2.png') }}" alt="Orbii Logo"
                      style="display:block;border:0; width: auto; height: 42px;">
                  </td>

                </tr>
              </table>

            </td>
          </tr>

          <tr>
            <td align="center" style="padding:24px 40px 8px;">

              <!-- Logo Orbii -->
              <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                <tr>

                  <td align="center" width="72" height="72"
                    style="width:72px;height:72px;border-radius:16px;">

                    <img src="{{ asset('images/orbii-1.png') }}" width="36" height="36" alt="Orbii Logo"
                      style="display:block;border:0;margin:0 auto;">

                  </td>

                </tr>
              </table>

              <h2 style="margin:20px 0 0;font-size:22px;line-height:1.3;font-weight:700;color:#000000;">
                Selamat datang, <span style="color:#476cff;">{{ $user->name ?? 'Explorer' }}</span>!
              </h2>

              <p style="margin:14px 0 0;font-size:15px;line-height:1.6;color:#615d59;">
                Terima kasih telah bergabung dengan Orbii. Verifikasi alamat email Anda untuk mulai menjelajahi
                komunitas hobi yang cocok untuk Anda.
              </p>

            </td>
          </tr>

          <tr>
            <td align="center" style="padding:24px 40px 8px;">

              <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                <tr>

                  <td align="center" style="background-color:#476cff;border-radius:100px;">

                    <a href="{{ $verificationUrl }}" target="_blank"
                      style="display:inline-block;padding:14px 36px;font-size:15px;font-weight:600;letter-spacing:0.2px;color:#ffffff;text-decoration:none;border-radius:100px;">
                      Verifikasi Email
                    </a>

                  </td>
                </tr>
              </table>

            </td>
          </tr>

          <tr>
            <td style="padding:20px 40px 0;">

              <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                <tr>
                  <td height="1" style="height:1px;line-height:1px;font-size:0;background-color:#e6e6e6;">
                    &nbsp;
                  </td>
                </tr>
              </table>

            </td>
          </tr>

          <tr>
            <td align="center" style="padding:18px 40px 32px;">

              <p style="margin:0;font-size:13px;line-height:1.5;color:#a39e98;">
                Jika Anda tidak membuat akun di Orbii, abaikan email ini.
              </p>

            </td>
          </tr>

          <tr>
            <td align="center" style="background-color:#fafafa;border-top:1px solid #e6e6e6;padding:20px 20px;">

              <p style="margin:0;font-size:12px;line-height:1.5;color:#a39e98;">
                &copy; {{ date('Y') }} Orbii. Hak cipta dilindungi.
              </p>

            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>

</body>

</html>
