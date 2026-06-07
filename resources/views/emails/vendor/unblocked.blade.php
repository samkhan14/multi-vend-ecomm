@extends('emails.layout.master')

@section('email-title', 'Store Unblocked')

@section('email-content')
    @php
        $ownerName = optional($vendor->user)->name ?? ($vendor->store_name ?? 'Vendor');
        $isApproved = (int) ($vendor->status ?? 0) === 1;
    @endphp

    <p style="margin:0 0 10px 0; color:#15803d; font-size:12px; font-weight:700; letter-spacing:0.4px; text-transform:uppercase;">
        Account Status: Unblocked
    </p>

    <h2 style="margin:0 0 14px 0; color:#0f172a; font-size:26px; line-height:1.3;">
        Store Access Restored
    </h2>

    <p style="margin:0 0 18px 0; color:#334155; font-size:15px;">
        Hi {{ $ownerName }}, your vendor account has been unblocked by the admin team.
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 16px 0; border:1px solid #d1fae5; border-radius:10px; overflow:hidden; background-color:#f0fdf4;">
        <tr>
            <td style="padding:14px 16px; color:#14532d; font-size:14px; line-height:1.7;">
                <strong style="color:#14532d;">Store Name:</strong> {{ $vendor->store_name }}<br>
                <strong style="color:#14532d;">Access:</strong>
                {{ $isApproved ? 'Vendor login is now active' : 'Account is unblocked but still pending approval' }}
            </td>
        </tr>
    </table>

    @if ($isApproved)
        <table cellpadding="0" cellspacing="0" border="0" style="margin:0 0 16px 0;">
            <tr>
                <td align="center" bgcolor="#1d4ed8" style="border-radius:8px;">
                    <a href="{{ route('vendor.login') }}" style="display:inline-block; padding:11px 18px; color:#ffffff; text-decoration:none; font-size:14px; font-weight:700;">
                        Vendor Login
                    </a>
                </td>
            </tr>
        </table>
    @endif

    <p style="margin:0; color:#64748b; font-size:13px;">
        If you need help, please contact the admin team.
    </p>
@endsection

