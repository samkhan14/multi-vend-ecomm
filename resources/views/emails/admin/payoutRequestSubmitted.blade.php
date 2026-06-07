@extends('emails.layout.master')

@section('email-title', 'New Payout Request')

@section('email-content')
    <p style="margin:0 0 10px 0; color:#1d4ed8; font-size:12px; font-weight:700; letter-spacing:0.4px; text-transform:uppercase;">
        Admin Alert
    </p>

    <h2 style="margin:0 0 14px 0; color:#0f172a; font-size:24px; line-height:1.3;">
        New Vendor Payout Request
    </h2>

    <p style="margin:0 0 16px 0; color:#334155; font-size:15px;">
        A vendor has submitted a new payout request for review.
    </p>

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 16px 0; border:1px solid #e2e8f0; border-radius:10px; overflow:hidden; background-color:#f8fafc;">
        <tr>
            <td style="padding:14px 16px; color:#334155; font-size:14px; line-height:1.8;">
                <strong style="color:#0f172a;">Request ID:</strong> #PR-{{ str_pad((string) $payoutRequest->id, 6, '0', STR_PAD_LEFT) }}<br>
                <strong style="color:#0f172a;">Vendor:</strong> {{ $vendorName }}<br>
                <strong style="color:#0f172a;">Amount:</strong> {{ number_format((float) $amount, 2) }}<br>
                <strong style="color:#0f172a;">Status:</strong> {{ ucfirst($payoutRequest->status) }}
            </td>
        </tr>
    </table>

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 16px 0; border:1px solid #dbeafe; border-radius:10px; overflow:hidden; background-color:#eff6ff;">
        <tr>
            <td style="padding:14px 16px; color:#1e3a8a; font-size:13px; line-height:1.7;">
                <strong style="display:block; margin-bottom:6px; font-size:14px;">Vendor Request Note</strong>
                {{ filled($requestNote) ? $requestNote : 'No note provided by vendor.' }}
            </td>
        </tr>
    </table>

    <p style="margin:0; color:#64748b; font-size:13px;">
        Please review this request from the admin payout panel.
    </p>
@endsection

