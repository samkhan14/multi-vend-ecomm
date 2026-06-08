<?php

namespace App\Enums;

enum NowPaymentsStatus: string
{
    case Waiting = 'waiting';
    case Confirming = 'confirming';
    case Confirmed = 'confirmed';
    case Sending = 'sending';
    case PartiallyPaid = 'partially_paid';
    case Finished = 'finished';
    case Failed = 'failed';
    case Refunded = 'refunded';
    case Expired = 'expired';

    public function isPaid(): bool
    {
        return in_array($this, [self::Finished, self::Confirmed], true);
    }

    public function isFailed(): bool
    {
        return in_array($this, [self::Failed, self::Expired, self::Refunded], true);
    }
}
