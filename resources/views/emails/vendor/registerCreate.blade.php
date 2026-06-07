@extends('emails.layout.master')

@section('email-title', 'Store Registration Successful')

@section('email-content')
    @php
        $ownerName = optional($vendor->user)->name ?? ($vendor->store_name ?? 'Vendor');
    @endphp

    <h2 style="margin:0 0 14px 0; color:#0f172a; font-size:24px; line-height:1.3;">
        Registration Received
    </h2>

    <p style="margin:0 0 16px 0; color:#334155; font-size:15px;">
        Hi {{ $ownerName }}, your store has been registered successfully.
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 18px 0; border:1px solid #dbe7ff; border-radius:10px; overflow:hidden; background-color:#f8fbff;">
        <tr>
            <td style="padding:16px 18px; color:#1e293b; font-size:14px; line-height:1.8;">
                <strong style="display:block; margin-bottom:8px; font-size:15px; color:#0f172a;">What happens next?</strong>
                <span style="display:block;">1. Admin team will review your submitted details.</span>
                <span style="display:block;">2. After approval, you can log in and start managing products.</span>
                <span style="display:block;">3. You will receive another email once your account is approved.</span>
            </td>
        </tr>
    </table>

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
        If you did not submit this request, please ignore this email.
    </p>
@endsection
