@extends('layouts.email')

@section('title', 'Back in stock')

@section('content')

    <h2 style="margin:0 0 15px; color:#2c3e50; font-size:24px; font-weight:600;">
        Good news!
    </h2>

    <p style="margin:0 0 20px; color:#666666; font-size:15px;">
        The product you asked us about is back in stock:
    </p>

    <p style="margin:0 0 25px; color:#2c3e50; font-size:18px; font-weight:600;">
        {{ $product->title }}
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 25px;">
        <tr>
            <td align="center">
                <a href="{{ route('product-detail', $product->slug) }}"
                   style="display:inline-block; padding:12px 32px; background-color:#5db845; color:#ffffff; text-decoration:none; border-radius:4px; font-size:15px; font-weight:600;">
                    View Product
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 20px; color:#666666; font-size:14px;">
        Stock is limited, so order soon if you would like it.
    </p>

    <p style="margin:0; color:#666666; font-size:15px;">
        Regards,<br>
        <strong style="color:#2c3e50;">Aashi Team</strong>
    </p>

@endsection
