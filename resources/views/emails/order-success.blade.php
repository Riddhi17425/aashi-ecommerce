@extends('layouts.email')

@section('title', 'Order Confirmation')

@section('content')

    <h2 style="color: #2c3e50; margin-top: 0; font-size: 24px;">
        Hello {{ $order->first_name ?? '' }} {{ $order->last_name ?? '' }},
    </h2>

    <p style="color: #666666; font-size: 15px; margin-bottom: 10px;">
        Thank you for your order!
    </p>

    <p style="color: #666666; font-size: 15px;">
        Your order
        <strong style="color: #333333;">
            {{ $order->order_number ?? ''}}
        </strong>
        has been successfully placed.
    </p>


    <!-- ORDER SUMMARY -->
    <h3 style="
        color: #2c3e50;
        font-size: 18px;
        margin-top: 30px;
        margin-bottom: 15px;">
        Order Summary
    </h3>

    <table
        width="100%"
        cellpadding="0"
        cellspacing="0"
        border="0"
        style="border-collapse: collapse; width: 100%;">

        <!-- Header -->
        <tr>
            <th
                align="left"
                style="
                    padding: 12px;
                    background-color: #f8fafc;
                    border-bottom: 2px solid #5db845;
                    color: #333333;
                    font-size: 13px;
                ">
                PRODUCT
            </th>

            <th
                align="center"
                style="
                    padding: 12px;
                    background-color: #f8fafc;
                    border-bottom: 2px solid #5db845;
                    color: #333333;
                    font-size: 13px;
                ">
                QTY
            </th>

            <th
                align="right"
                style="
                    padding: 12px;
                    background-color: #f8fafc;
                    border-bottom: 2px solid #5db845;
                    color: #333333;
                    font-size: 13px;
                ">
                PRICE
            </th>
        </tr>

        <!-- Products -->

        @foreach($order->cart_info as $item)
            <tr>
                <td align="left"
                    style="
                        padding: 15px;
                        border-bottom: 1px solid #eeeeee;
                        color: #555555;
                        font-size: 14px;
                    ">

                    {{-- Product Name --}}
                    <strong style="color:#2c3e50;">
                        {{ $item->product->title ?? 'Product' }}
                    </strong>

                    {{-- Size --}}
                    @php
                        $sizeData = json_decode($item->size_price, true);
                    @endphp

                    @if(is_array($sizeData) && !empty($sizeData['size']))
                        <br>
                        <span style="font-size: 12px; color: #888888;">
                            Size: {{ $sizeData['size'] ?? '' }}
                        </span>
                    @endif

                    {{-- Color --}}
                    @if($item->color)
                        <br>
                        <span style="font-size: 12px; color: #888888;">
                            Color: {{ $item->color->color_name ?? '' }}
                        </span>
                    @endif

                </td>

                <td
                    align="center"
                    style="
                        padding: 14px 12px;
                        border-bottom: 1px solid #eeeeee;
                        color: #555555;
                        font-size: 14px;
                    ">
                    {{ $item->quantity ?? '' }}
                </td>

                <td
                    align="right"
                    style="
                        padding: 14px 12px;
                        border-bottom: 1px solid #eeeeee;
                        color: #555555;
                        font-size: 14px;
                    ">
                    ₹{{ number_format($item->amount ?? ($item->price * $item->quantity), 2) }}
                </td>
            </tr>
        @endforeach
    </table>

    <!-- ORDER TOTALS -->

    <table
        width="100%"
        cellpadding="0"
        cellspacing="0"
        border="0"
        style="margin-top: 15px;">

        <tr>
            <td
                align="right"
                style="
                    padding: 7px;
                    color: #666666;
                    font-size: 14px;
                    font-weight: bold;
                ">
                Subtotal
            </td>

            <td
                align="right"
                width="120"
                style="
                    padding: 7px;
                    color: #555555;
                    font-size: 14px;
                ">
                ₹{{ number_format($order->sub_total, 2) }}
            </td>
        </tr>

        <tr>
            <td
                align="right"
                style="
                    padding: 7px;
                    color: #666666;
                    font-size: 14px;
                    font-weight: bold;
                ">
                Shipping
            </td>

            <td
                align="right"
                style="
                    padding: 7px;
                    color: #555555;
                    font-size: 14px;
                ">
                ₹{{ number_format($order->shiping_charges ?? 0, 2) }}
            </td>
        </tr>

        @if($order->coupon)
            <tr>
                <td
                    align="right"
                    style="
                        padding: 7px;
                        color: #666666;
                        font-size: 14px;
                    ">
                    Discount
                </td>

                <td
                    align="right"
                    style="
                        padding: 7px;
                        color: #555555;
                        font-size: 14px;
                    ">
                    -₹{{ number_format($order->coupon, 2) }}
                </td>
            </tr>
        @endif

        <tr>
            <td
                align="right"
                style="
                    padding: 12px 7px;
                    border-top: 1px solid #dddddd;
                    color: #333333;
                    font-size: 17px;
                    font-weight: bold;
                ">
                TOTAL
            </td>

            <td
                align="right"
                style="
                    padding: 12px 7px;
                    border-top: 1px solid #dddddd;
                    color: #5db845;
                    font-size: 18px;
                    font-weight: bold;
                ">
                ₹{{ number_format($order->total_amount, 2) }}
            </td>
        </tr>
    </table>

    <!-- VIEW ORDER BUTTON -->

    <table
        width="100%"
        cellpadding="0"
        cellspacing="0"
        border="0"
        style="margin-top: 30px;">

        <tr>
            <td align="center">
                <a
                    href="{{ route('order.dertails', $order->id) }}"
                    target="_blank"
                    style="
                        display: inline-block;
                        padding: 14px 35px;
                        background-color: #5db845;
                        color: #ffffff;
                        text-decoration: none;
                        font-size: 15px;
                        font-weight: bold;
                        border-radius: 5px;
                    ">
                    VIEW ORDER
                </a>
            </td>
        </tr>
    </table>

    <!-- DELIVERY INFORMATION -->

    <!-- Customer Details -->
    <table width="100%" cellpadding="0" cellspacing="0" border="0"
        style="margin-top: 30px; border-collapse: collapse;">

        <tr>
            <td colspan="2"
                style="
                    padding: 14px 15px;
                    background-color: #f8fafc;
                    border-bottom: 2px solid #5db845;
                    color: #2c3e50;
                    font-size: 16px;
                    font-weight: bold;
                ">
                Customer Information
            </td>
        </tr>

        <tr>
            <td width="35%"
                style="
                    padding: 12px 15px;
                    border-bottom: 1px solid #eeeeee;
                    color: #555555;
                    font-weight: bold;
                    font-size: 14px;
                ">
                Name
            </td>

            <td
                style="
                    padding: 12px 15px;
                    border-bottom: 1px solid #eeeeee;
                    color: #555555;
                    font-size: 14px;
                ">
                {{ $order->first_name ?? ''}} {{ $order->last_name ?? ''}}
            </td>
        </tr>

        <tr>
            <td
                style="
                    padding: 12px 15px;
                    border-bottom: 1px solid #eeeeee;
                    color: #555555;
                    font-weight: bold;
                    font-size: 14px;
                ">
                Email
            </td>

            <td
                style="
                    padding: 12px 15px;
                    border-bottom: 1px solid #eeeeee;
                    color: #555555;
                    font-size: 14px;
                ">
                {{ $order->email ?? ''}}
            </td>
        </tr>

        <tr>
            <td
                style="
                    padding: 12px 15px;
                    border-bottom: 1px solid #eeeeee;
                    color: #555555;
                    font-weight: bold;
                    font-size: 14px;
                ">
                Phone
            </td>

            <td
                style="
                    padding: 12px 15px;
                    border-bottom: 1px solid #eeeeee;
                    color: #555555;
                    font-size: 14px;
                ">
                {{ $order->phone ?? '' }}
            </td>
        </tr>

        <tr>
            <td
                style="
                    padding: 12px 15px;
                    border-bottom: 1px solid #eeeeee;
                    color: #555555;
                    font-weight: bold;
                    font-size: 14px;
                ">
                Address
            </td>

            <td
                style="
                    padding: 12px 15px;
                    border-bottom: 1px solid #eeeeee;
                    color: #555555;
                    font-size: 14px;
                ">
                {{ $order->address1 ?? '' }}

                @if($order->address2)
                    , {{ $order->address2 ?? '' }}
                @endif
            </td>
        </tr>

        <tr>
            <td
                style="
                    padding: 12px 15px;
                    border-bottom: 1px solid #eeeeee;
                    color: #555555;
                    font-weight: bold;
                    font-size: 14px;
                ">
                City
            </td>

            <td
                style="
                    padding: 12px 15px;
                    border-bottom: 1px solid #eeeeee;
                    color: #555555;
                    font-size: 14px;
                ">
                {{ $order->city ?? ''}}
            </td>
        </tr>

        <tr>
            <td
                style="
                    padding: 12px 15px;
                    border-bottom: 1px solid #eeeeee;
                    color: #555555;
                    font-weight: bold;
                    font-size: 14px;
                ">
                State
            </td>

            <td
                style="
                    padding: 12px 15px;
                    border-bottom: 1px solid #eeeeee;
                    color: #555555;
                    font-size: 14px;
                ">
                {{ $order->state ?? ''}}
            </td>
        </tr>

        <tr>
            <td
                style="
                    padding: 12px 15px;
                    color: #555555;
                    font-weight: bold;
                    font-size: 14px;
                ">
                Pincode
            </td>

            <td
                style="
                    padding: 12px 15px;
                    color: #555555;
                    font-size: 14px;
                ">
                {{ $order->post_code ?? ''}}
            </td>
        </tr>
    </table>

    <p
        style="
            color: #666666;
            font-size: 14px;
            margin-top: 25px;
        ">
        We'll keep you updated about your order.
    </p>

    <p
        style="
            color: #666666;
            font-size: 14px;
            line-height: 1.6;
        ">
        Regards,<br>

        <strong style="color: #333333;">
            Aashi Ecommerce Team
        </strong>
    </p>
@endsection