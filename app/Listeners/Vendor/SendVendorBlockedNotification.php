<?php

namespace App\Listeners\Vendor;

use App\Events\Vendor\VendorBlocked;
use App\Notifications\Vendor\VendorBlockedNotification;

class SendVendorBlockedNotification
{
    public function __construct()
    {
        //
    }

    public function handle(VendorBlocked $event): void
    {
        $vendor = $event->vendor->loadMissing('user');
        $user = $vendor->user;

        if (! $user) {
            return;
        }

        $user->notify(new VendorBlockedNotification($vendor));
    }
}

