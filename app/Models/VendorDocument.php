<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorDocument extends Model
{
    protected $table = 'vendor_documents';

    protected $fillable = [
        'vendor_id',
        'document_type',
        'document_number',
        'document_file_path',
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    
}
