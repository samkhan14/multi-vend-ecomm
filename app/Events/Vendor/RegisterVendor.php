<?php

namespace App\Events\Vendor;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class RegisterVendor
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $vendor;

    public function __construct($vendor)
    {
        $this->vendor = $vendor;
    }

}
