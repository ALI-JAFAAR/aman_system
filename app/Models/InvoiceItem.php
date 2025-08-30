<?php

// app/Models/InvoiceItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model{

    protected $fillable = [
        'invoice_id', 'item_type', 'reference_type', 'reference_id', 'organization_id',
        'description', 'qty', 'unit_price', 'line_total',
        'partner_share', 'host_share', 'platform_share', 'distribution_snapshot',
    ];

    protected $casts = [
        'distribution_snapshot' => 'array',
    ];

    public function invoice(){
        return $this->belongsTo(Invoice::class);
    }

    public function reference(){
        return $this->morphTo();
    }
}
