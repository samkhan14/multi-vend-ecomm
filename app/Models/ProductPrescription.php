<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ProductPrescription extends Model
{
    protected $table = 'product_prescriptions';

    protected $fillable = [
        'prescriptionable_type',
        'prescriptionable_id',
        'right_axis',
        'right_spherical',
        'right_cylindrical',
        'left_axis',
        'left_spherical',
        'left_cylindrical',
        'prescription_type',
        'notes',
        'prescription_image',
    ];

    protected $casts = [
        'right_axis' => 'decimal:2',
        'right_spherical' => 'decimal:2',
        'right_cylindrical' => 'decimal:2',
        'left_axis' => 'decimal:2',
        'left_spherical' => 'decimal:2',
        'left_cylindrical' => 'decimal:2',
    ];

    /**
     * Get the parent prescriptionable model (cart or order item)
     */
    public function prescriptionable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Format prescription as text
     */
    public function getFormattedAttribute(): string
    {
        return "OD: {$this->right_spherical}/{$this->right_cylindrical}×{$this->right_axis}° | OS: {$this->left_spherical}/{$this->left_cylindrical}×{$this->left_axis}°";
    }

    /**
     * Check if prescription is complete
     */
    public function isComplete(): bool
    {
        return !is_null($this->right_axis) && 
               !is_null($this->right_spherical) && 
               !is_null($this->right_cylindrical) &&
               !is_null($this->left_axis) && 
               !is_null($this->left_spherical) && 
               !is_null($this->left_cylindrical);
    }
}