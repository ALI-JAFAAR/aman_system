<?php
// app/Models/Invoice.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id','issuer_employee_id','number','issued_at',
        'subtotal','discount_type','discount_value','discount_funded_by',
        'discount_amount','total','paid','balance','status','currency','meta','notes',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'meta' => 'array',
    ];

    public function items()   {
        return $this->hasMany(InvoiceItem::class);
    }
    public function payments(){
        return $this->hasMany(Payment::class);
    }
    public function user()    {
        return $this->belongsTo(User::class);
    }
    public function issuer()  {
        return $this->belongsTo(Employee::class,'issuer_employee_id');
    }
}
