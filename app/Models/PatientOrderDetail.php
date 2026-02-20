<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientOrderDetail extends Model
{
    protected $fillable = [
        'patient_order_id',
        'item_id',
        'batch_id',
        'qty',
        'sell_price',
        'subtotal'
    ];

    public function order()
    {
        return $this->belongsTo(PatientOrder::class, 'patient_order_id');
    }

    public function item()
    {
        return $this->belongsTo(InventoryItems::class);
    }

    public function batch()
    {
        return $this->belongsTo(InventoryBatch::class);
    }

    public function billItem()
    {
        return $this->hasOne(BillItem::class, 'reference_id');
    }
}
