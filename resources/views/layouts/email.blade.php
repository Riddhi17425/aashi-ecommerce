<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Aashi Ecommerce')</title>
</head>

<body style="margin: 0; padding: 0; background-color: #f4f6f9; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0"
        style="background-color: #f4f6f9; width: 100%; margin: 0 auto;">

        <tr>
            <td align="center" style="padding: 40px 10px;">

                <table width="600" cellpadding="0" cellspacing="0" border="0"
                    style="max-width: 600px; width: 100%; background-color: #ffffff; border: 1px solid #e4e4e4;">

                    {{-- ================= HEADER ================= --}}
                    <tr>
                        <td align="center" style="background-color: #ffffff;">

                            <img
                                src="{{ asset('images/aashi-email-banner.webp') }}"
                                alt="Aashi Ecommerce Banner"
                                width="600"
                                style="width: 100%; max-width: 600px; display: block; border: 0;"
                            >
                        </td>
                    </tr>

                    {{-- ================= DYNAMIC CONTENT ================= --}}
                    <tr>
                        <td style="padding: 40px 35px; color: #444444; font-size: 16px; line-height: 1.6;">

                            @yield('content')

                        </td>
                    </tr>


                    {{-- ================= FOOTER ================= --}}
                    <tr>
                        <td align="center"
                            style="background-color: #2c3e50; padding: 30px 20px; color: #bdc3c7; font-size: 14px; line-height: 1.6;">

                            <p style="margin: 0 0 10px 0;">
                                &copy; {{ date('Y') }} Aashi Ecommerce. All rights reserved.
                            </p>

                            <table width="100%" cellpadding="0" cellspacing="0" border="0"
                                style="margin-top: 15px;">

                                <tr>
                                    <td align="center">

                                        <span style="display: inline-block; margin: 0 10px;">
                                            📧
                                            <a href="mailto:support@aashiecommerce.com"
                                                style="color: #5db845; text-decoration: none;">
                                                support@aashiecommerce.com
                                            </a>
                                        </span>

                                        <span style="display: inline-block; margin: 0 10px;">
                                            📞
                                            <a href="tel:+919898592812"
                                                style="color: #5db845; text-decoration: none;">
                                                +91 98985 92812
                                            </a>
                                        </span>

                                    </td>
                                </tr>

                            </table>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>

    </table>

</body>
</html>