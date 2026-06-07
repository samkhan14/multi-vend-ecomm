@extends('emails.layout.master')

@section('email-title', 'Store Approved')

@section('email-content')
    @php
        $ownerName = optional($vendor->user)->name ?? ($vendor->store_name ?? 'Vendor');
    @endphp

    <h2 style="margin:0 0 14px 0; color:#0f172a; font-size:20px; line-height:1.3;">
        Store Approved
    </h2>

    <p style="margin:0 0 16px 0; color:#334155; font-size:15px;">
        Hi {{ $ownerName }}, congratulations. Your store has been approved by our admin team.
    </p>

    <p style="margin:0 0 16px 0; color:#475569; font-size:14px;">
        Store Name: <strong style="color:#0f172a;">{{ $vendor->store_name }}</strong>
    </p>

    <table cellpadding="0" cellspacing="0" border="0" style="margin:0 0 18px 0;">
        <tr>
            <td align="center" bgcolor="#1d4ed8" style="border-radius:8px;">
                <a href="{{ route('vendor.login') }}" style="display:inline-block; padding:11px 18px; color:#ffffff; text-decoration:none; font-size:14px; font-weight:700;">
                    Vendor Login
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0; color:#64748b; font-size:13px;">
        You can now log in and start managing your products and orders.
    </p>
@endsection

