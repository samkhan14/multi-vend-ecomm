<?php

namespace App\Listeners\Vendor;

use App\Events\Vendor\VendorRejected;
use App\Notifications\Vendor\VendorRejectedNotification;

class SendVendorRejectedNotification
{
    public function __construct()
    {
        //
    }

    public function handle(VendorRejected $event): void
    {
        $vendor = $event->vendor->loadMissing('user');
        $user = $vendor->user;

        if (! $user) {
            return;
        }

        $user->notify(new VendorRejectedNotification($vendor));
    }
}

