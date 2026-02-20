<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientOrder extends Model
{
    protected $fillable = [
        'order_no',
        'mr_code',
        'visit_no',
        'order_date',
        'total_amount',
        'status',
        'user_id'
    ];

    protected $casts = [
        'order_date' => 'datetime',
    ];

    public function details()
    {
        return $this->hasMany(PatientOrderDetail::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
