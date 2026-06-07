<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'image',
        'banner_video',
        'banner_video_status',
        'type',
        'description',
        'link',
        'tagline',
        'title',
        'alt',
        'status',
        'mob_banner_image',
        'start_date',
        'end_date',
    ];

    /**
     * Scope for Offer Banners only
     */
    public function scopeOfferBanners($query)
    {
        return $query->where('type', 'Offer Banner');
    }

    /**
     * Scope for active dated offer banners (Priority 1)
     */
    public function scopeActiveDatedOffer($query)
    {
        $now = now();
        return $query->offerBanners()
            ->whereNotNull('start_date')
            ->whereNotNull('end_date')
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where('status', 1);
    }

    /**
     * Scope for default offer banners (Priority 2)
     */
    public function scopeDefaultOffer($query)
    {
        return $query->offerBanners()
            ->whereNull('start_date')
            ->whereNull('end_date')
            ->where('status', 1);
    }

    /**
     * Check if banner has active date range
     */
    public function isActiveDated()
    {
        if (!$this->start_date || !$this->end_date) {
            return false;
        }
        
        $now = now();
        return $this->start_date <= $now && $this->end_date >= $now;
    }

    /**
     * Get countdown end timestamp for JavaScript
     */
    public function getCountdownEndTimestamp()
    {
        if ($this->end_date) {
            return strtotime($this->end_date);
        }
        return null;
    }
}