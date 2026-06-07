@extends('emails.layout.master')

@section('email-title', 'Order Status Updated')

@section('email-content')
    @php
        $orderReference = $orderNumber ?: ('#' . $orderId);
    @endphp

    <p style="margin:0 0 10px 0; color:#1d4ed8; font-size:12px; font-weight:700; letter-spacing:0.4px; text-transform:uppercase;">
        Order Update
    </p>

    <h3 style="margin:0 0 14px 0; color:#0f172a; font-size:20px; line-height:1.3;">
        Your order status has changed
    </h3>

    <p style="margin:0 0 16px 0; color:#334155; font-size:15px;">
        Hi {{ $customerName ?: 'Customer' }}, your order <strong>{{ $orderReference }}</strong> is now <strong>{{ $newStatus }}</strong>.
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 16px 0; border:1px solid #e2e8f0; border-radius:10px; overflow:hidden; background-color:#f8fafc;">
        <tr>
            <td style="padding:14px 16px; color:#334155; font-size:14px; line-height:1.7;">
                <strong style="color:#0f172a;">Order Number:</strong> {{ $orderReference }}<br>
                @if ($oldStatus)
                    <strong style="color:#0f172a;">Previous Status:</strong> {{ $oldStatus }}<br>
                @endif
                <strong style="color:#0f172a;">Current Status:</strong> {{ $newStatus }}<br>
                <strong style="color:#0f172a;">Order Total:</strong> {{ number_format((float) $grandTotal, 2) }}
            </td>
        </tr>
    </table>

    <p style="margin:0; color:#64748b; font-size:13px;">
        If you have any questions, please contact support.
    </p>
@endsection

