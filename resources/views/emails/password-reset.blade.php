@extends('layouts.email')

@section('title', 'Reset Your Password')

@section('content')

    <h2 style="
        margin:0 0 15px;
        color:#2c3e50;
        font-size:24px;
        font-weight:600;
    ">
        Hello!
    </h2>

    <p style="
        margin:0 0 20px;
        color:#666666;
        font-size:15px;
    ">
        You are receiving this email because we received a password
        reset request for your Aashi account.
    </p>

    <!-- Reset Button -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:30px 0;">
        <tr>
            <td align="center">

                <a href="{{ $url }}"
                   target="_blank"
                   style="
                       display:inline-block;
                       padding:14px 32px;
                       background-color:#5db845;
                       color:#ffffff;
                       text-decoration:none;
                       font-size:15px;
                       font-weight:bold;
                       border-radius:5px;
                   ">
                    RESET PASSWORD
                </a>

            </td>
        </tr>
    </table>

    <p style="
        margin:0 0 20px;
        color:#666666;
        font-size:15px;
    ">
        This password reset link will expire in
        <strong>60 minutes</strong>.
    </p>

    <p style="
        margin:0 0 20px;
        color:#666666;
        font-size:15px;
    ">
        If you did not request a password reset, no further action is required.
    </p>

    <p style="
        margin:0;
        color:#666666;
        font-size:15px;
    ">
        Regards,<br>

        <strong style="color:#2c3e50;">
            Aashi Team
        </strong>
    </p>

    <!-- Divider -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0"
           style="margin-top:30px;">
        <tr>
            <td style="border-top:1px solid #eeeeee;"></td>
        </tr>
    </table>

    <!-- Fallback URL -->
    <p style="
        margin:25px 0 0;
        color:#888888;
        font-size:13px;
        line-height:1.6;
    ">
        If you're having trouble clicking the
        <strong>"RESET PASSWORD"</strong> button,
        copy and paste the following URL into your web browser:
    </p>

    <p style="
        margin:10px 0 0;
        word-break:break-all;
        font-size:12px;
    ">
        <a href="{{ $url }}"
           style="color:#5db845; text-decoration:none;">
            {{ $url }}
        </a>
    </p>

@endsection
