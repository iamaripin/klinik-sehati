<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Validation\Rules\In;

class InventoryStockMovement extends Model
{

    protected $table = 'stock_movements';
    protected $fillable = [
        'item_id',
        'batch_id',
        'type',
        'qty',
        'reference_number',
        'notes',
        'user_id'
    ];

    public function item()
    {
        return $this->belongsTo(InventoryItems::class);
    }

    public function batch()
    {
        return $this->belongsTo(InventoryBatch::class);
    }
}
