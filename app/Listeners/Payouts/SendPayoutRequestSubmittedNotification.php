<?php

namespace App\Listeners\Payouts;

use App\Events\PayoutRequestSubmitted;
use App\Models\PayoutRequest;
use App\Models\User;
use App\Models\Vendor;
use App\Notifications\PayoutRequestSubmittedToSuperAdminNotification;
use Illuminate\Support\Facades\Notification;

class SendPayoutRequestSubmittedNotification
{
    public function __construct()
    {
        //
    }

    public function handle(PayoutRequestSubmitted $event): void
    {
        $payoutRequest = PayoutRequest::query()
            ->with(['vendor.user'])
            ->find($event->payoutRequestId);

        if (! $payoutRequest) {
            return;
        }

        $superAdminEmail = $this->resolveSuperAdminEmail();

        if (! $superAdminEmail) {
            return;
        }

        Notification::route('mail', $superAdminEmail)->notify(
            new PayoutRequestSubmittedToSuperAdminNotification($payoutRequest)
        );
    }

    private function resolveSuperAdminEmail(): ?string
    {
        $superAdminEmail = User::query()
            ->role('Super Admin')
            ->whereNotNull('email')
            ->value('email');

        if ($superAdminEmail) {
            return $superAdminEmail;
        }

        return Vendor::query()
            ->where('vendor_type', 'super_admin')
            ->with('user:id,email')
            ->first()
            ?->user
            ?->email;
    }
}

