<?php

namespace App\Events\Vendor;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VendorApproved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $vendor;

    public function __construct($vendor)
    {
        $this->vendor = $vendor;
    }
}

