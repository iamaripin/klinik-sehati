<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryBatch extends Model {

    protected $table = 'batches';
    protected $fillable = [
        'item_id',
        'batch_number',
        'expired_date',
        'purchase_price',
        'sell_price',
        'stock_qty'
    ];

    protected $casts = [
        'expired_date' => 'date'
    ];

    public function item()
    {
        return $this->belongsTo(InventoryItems::class);
    }
 
    public function stockMovements()
    {
        return $this->hasMany(InventoryStockMovement::class);
    }
}
