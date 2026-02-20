<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class InventoryItems extends Model
{
    use HasFactory;

    protected $table = 'items';
    protected $fillable = [
        'item_code',
        'item_name',
        'category',
        'generic_name',
        'brand_name',
        'dosage_form',
        'strength',
        'unit',
        'minimal_stock',
        'is_active'
    ];

    public function batches()
    {
        return $this->hasMany(InventoryBatch::class, 'item_id');
    }

    public function stockMovements()
    {
        return $this->hasMany(InventoryStockMovement::class, 'item_id');
    }
}
