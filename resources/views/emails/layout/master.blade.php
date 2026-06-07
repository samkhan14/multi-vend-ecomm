<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('email-title', 'Diginotive')</title>
    <style>
        @media only screen and (max-width: 640px) {
            .email-outer {
                padding: 14px 8px !important;
            }

            .email-content-cell {
                padding: 22px 18px 16px 18px !important;
                font-size: 14px !important;
            }

            .email-footer-cell {
                padding: 14px 18px 20px 18px !important;
            }

            .email-logo {
                width: 150px !important;
                max-width: 150px !important;
                height: auto !important;
            }
        }
    </style>
</head>
<body style="margin:0; padding:0; background-color:#eef2f7; font-family:Helvetica, Arial, sans-serif;">
    @php
        $settings = siteSetting();
        $customLogoRelative = $settings && $settings->website_logo
            ? ltrim($settings->website_logo, '/')
            : null;

        $customLogoPath = $customLogoRelative
            ? public_path('storage/' . $customLogoRelative)
            : null;

        $fallbackLogoPath = public_path('assets/images/others/logo.png');
        $legacyLogoPath = public_path('diginotive.png');

        if ($customLogoPath && file_exists($customLogoPath)) {
            $logoPath = $customLogoPath;
            $logoUrl = asset('storage/' . $customLogoRelative);
        } elseif (file_exists($fallbackLogoPath)) {
            $logoPath = $fallbackLogoPath;
            $logoUrl = asset('assets/images/others/logo.png');
        } else {
            $logoPath = $legacyLogoPath;
            $logoUrl = asset('diginotive.png');
        }

        $emailLogo = (isset($message) && is_object($message) && method_exists($message, 'embed') && file_exists($logoPath))
            ? $message->embed($logoPath)
            : $logoUrl;
    @endphp
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="email-outer" style="background-color:#eef2f7; padding:28px 12px;">
        <tr>
            <td align="center">
                <table width="100%" cellpadding="0" cellspacing="0" border="0" style="width:100%; max-width:620px; background-color:#ffffff; border:1px solid #dbe3ef; border-radius:14px; overflow:hidden;">
                    <tr>
                        <td style="background-color:#ffffff; border-bottom:1px solid #e5e7eb; padding:22px 24px 18px 24px; text-align:center;">
                            <img src="{{ $emailLogo }}" width="180" alt="Diginotive" class="email-logo" style="display:block; margin:0 auto 10px auto; width:180px; max-width:180px; height:auto;">
                            <p style="margin:0; color:#64748b; font-size:12px; letter-spacing:0.35px; text-transform:uppercase;">
                                Multi Vendor Marketplace
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td class="email-content-cell" style="padding:30px 28px 18px 28px; color:#1f2937; font-size:15px; line-height:1.7;">
                            @yield('email-content')
                        </td>
                    </tr>

                    <tr>
                        <td class="email-footer-cell" style="padding:18px 28px 26px 28px; border-top:1px solid #edf2f7; color:#64748b; font-size:12px; line-height:1.6; text-align:center;">
                            <p style="margin:0 0 8px 0;">This is an automated email from Diginotive.</p>
                            <p style="margin:0;">(c) {{ date('Y') }} Diginotive. All rights reserved.</p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
