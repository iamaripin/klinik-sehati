<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BillItem extends Model
{
    protected $table = 'bill_items';
    protected $fillable = [
        'bill_id',
        'item_type',
        'reference_id',
        'description',
        'qty',
        'price',
        'subtotal'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    // Optional: kalau reference ke patient_order_detail
    public function patientOrderDetail()
    {
        return $this->belongsTo(PatientOrderDetail::class, 'reference_id');
    }
}
