<?php

// app/Models/Payment.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model{

    protected $fillable = ['invoice_id', 'user_id', 'method', 'amount', 'reference', 'meta'];

    protected $casts = ['meta' => 'array'];

    public function invoice(){
        return $this->belongsTo(Invoice::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
