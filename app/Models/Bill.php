<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $table = 'bills';
protected $fillable = [
'bill_no',
'mr_code',
'visit_no',
'subtotal',
'discount',
'tax',
'grand_total',
'status',
'bill_date',
'user_id'
];

protected $casts = [
'bill_date' => 'datetime',
'subtotal' => 'decimal:2',
'discount' => 'decimal:2',
'tax' => 'decimal:2',
'grand_total' => 'decimal:2',
];

/*
|--------------------------------------------------------------------------
| RELATIONS
|--------------------------------------------------------------------------
*/

public function items()
{
return $this->hasMany(BillItem::class);
}

public function payments()
{
return $this->hasMany(Payment::class);
}

public function patientOrders()
{
return $this->hasMany(PatientOrder::class, 'visit_no', 'visit_no');
}

public function user()
{
return $this->belongsTo(User::class);
}

/*
|--------------------------------------------------------------------------
| HELPERS
|--------------------------------------------------------------------------
*/

public function getTotalPaidAttribute()
{
return $this->payments()->sum('amount_paid');
}

public function getRemainingAttribute()
{
return $this->grand_total - $this->total_paid;
}
}