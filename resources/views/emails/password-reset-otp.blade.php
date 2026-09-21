@extends('layouts.email')

@section('title', 'Your Password Reset OTP')

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
        We received a request to reset the password for your Aashi account.
        Please use the One-Time Password (OTP) below to continue.
    </p>

    <!-- OTP Box -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:30px 0;">
        <tr>
            <td align="center">
                <div style="
                    display:inline-block;
                    padding:18px 40px;
                    background-color:#f6f7fb;
                    border:1px dashed #5db845;
                    border-radius:6px;
                    font-size:32px;
                    font-weight:bold;
                    letter-spacing:8px;
                    color:#2c3e50;
                ">
                    {{ $otp }}
                </div>
            </td>
        </tr>
    </table>

    <p style="
        margin:0 0 20px;
        color:#666666;
        font-size:15px;
        text-align:center;
    ">
        This OTP is valid for <strong>10 minutes</strong>.
    </p>

    <p style="
        margin:0 0 20px;
        color:#666666;
        font-size:15px;
    ">
        If you did not request a password reset, you can safely ignore this email.
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

@endsection