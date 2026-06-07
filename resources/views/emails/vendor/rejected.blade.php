@extends('emails.layout.master')

@section('email-title', 'Store Rejected')

@section('email-content')
    @php
        $ownerName = optional($vendor->user)->name ?? ($vendor->store_name ?? 'Vendor');
    @endphp

    <p style="margin:0 0 10px 0; color:#b91c1c; font-size:12px; font-weight:700; letter-spacing:0.4px; text-transform:uppercase;">
        Application Status: Rejected
    </p>

    <h2 style="margin:0 0 14px 0; color:#0f172a; font-size:20px; line-height:1.3;">
        Store Application Update
    </h2>

    <p style="margin:0 0 18px 0; color:#334155; font-size:15px;">
        Hi {{ $ownerName }}, after review, we were unable to approve your store application at this time.
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 16px 0; border:1px solid #e2e8f0; border-radius:10px; overflow:hidden; background-color:#f8fafc;">
        <tr>
            <td style="padding:14px 16px; color:#334155; font-size:14px; line-height:1.7;">
                <strong style="color:#0f172a;">Store Name:</strong> {{ $vendor->store_name }}<br>
                <strong style="color:#0f172a;">Review Result:</strong> Not Approved
            </td>
        </tr>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 16px 0; border:1px solid #fecaca; border-radius:10px; overflow:hidden; background-color:#fef2f2;">
        <tr>
            <td style="padding:14px 16px; color:#7f1d1d; font-size:13px; line-height:1.8;">
                <strong style="display:block; margin-bottom:6px; font-size:14px;">Recommended next steps</strong>
                1. Review your submitted business details and documents.<br>
                2. Correct any missing or unclear information.<br>
                3. Contact support if you need clarification before resubmitting.
            </td>
        </tr>
    </table>

    <p style="margin:0; color:#64748b; font-size:13px;">
        You can resubmit your request after updates are completed.
    </p>
@endsection
