<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    protected $table = 'newsletter_subscribers';

    protected $fillable = [
        'email',
        'status',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Status constants
    const STATUS_SUBSCRIBED = 'subscribed';
    const STATUS_UNSUBSCRIBED = 'unsubscribed';

    /**
     * Get subscriber by email
     */
    public static function findByEmail($email)
    {
        return self::where('email', $email)->first();
    }

    /**
     * Check if email already subscribed
     */
    public static function isSubscribed($email)
    {
        return self::where('email', $email)
                    ->where('status', self::STATUS_SUBSCRIBED)
                    ->exists();
    }

    /**
     * Scope for active subscribers
     */
    public function scopeSubscribed($query)
    {
        return $query->where('status', self::STATUS_SUBSCRIBED);
    }

    /**
     * Scope for unsubscribed
     */
    public function scopeUnsubscribed($query)
    {
        return $query->where('status', self::STATUS_UNSUBSCRIBED);
    }
}