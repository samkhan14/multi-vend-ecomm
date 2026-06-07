@extends('emails.layout.master')

@section('email-title', 'Store Blocked')

@section('email-content')
    @php
        $ownerName = optional($vendor->user)->name ?? ($vendor->store_name ?? 'Vendor');
    @endphp

    <p style="margin:0 0 10px 0; color:#b91c1c; font-size:12px; font-weight:700; letter-spacing:0.4px; text-transform:uppercase;">
        Account Status: Blocked
    </p>

    <h2 style="margin:0 0 14px 0; color:#0f172a; font-size:26px; line-height:1.3;">
        Store Access Temporarily Disabled
    </h2>

    <p style="margin:0 0 18px 0; color:#334155; font-size:15px;">
        Hi {{ $ownerName }}, your vendor account has been temporarily blocked by the admin team.
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 16px 0; border:1px solid #e2e8f0; border-radius:10px; overflow:hidden; background-color:#f8fafc;">
        <tr>
            <td style="padding:14px 16px; color:#334155; font-size:14px; line-height:1.7;">
                <strong style="color:#0f172a;">Store Name:</strong> {{ $vendor->store_name }}<br>
                <strong style="color:#0f172a;">Access:</strong> Vendor login is currently restricted
            </td>
        </tr>
    </table>

    <p style="margin:0; color:#64748b; font-size:13px;">
        For assistance, please contact support or the admin team.
    </p>
@endsection

