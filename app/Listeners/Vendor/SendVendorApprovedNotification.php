<?php

namespace App\Listeners\Vendor;

use App\Events\Vendor\VendorApproved;
use App\Notifications\Vendor\VendorApprovedNotification;

class SendVendorApprovedNotification
{
    public function __construct()
    {
        //
    }

    public function handle(VendorApproved $event): void
    {
        $vendor = $event->vendor->loadMissing('user');
        $user = $vendor->user;

        if (! $user) {
            return;
        }

        $user->notify(new VendorApprovedNotification($vendor));
    }
}

