<?php

namespace App\Listeners\Vendor;

use App\Events\Vendor\RegisterVendor;
use App\Notifications\Vendor\RegisterStoreNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendVendorRegisterNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(RegisterVendor $event): void
    {
        $vendor = $event->vendor;
        $user = $vendor->user;

        $user->notify(
            new RegisterStoreNotification($vendor)
        );
    }
}
