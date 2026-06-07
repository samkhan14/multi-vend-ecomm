<?php

namespace App\Listeners\Vendor;

use App\Events\Vendor\VendorUnblocked;
use App\Notifications\Vendor\VendorUnblockedNotification;

class SendVendorUnblockedNotification
{
    public function __construct()
    {
        //
    }

    public function handle(VendorUnblocked $event): void
    {
        $vendor = $event->vendor->loadMissing('user');
        $user = $vendor->user;

        if (! $user) {
            return;
        }

        $user->notify(new VendorUnblockedNotification($vendor));
    }
}

