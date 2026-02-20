<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Nurse extends Model
{
    use HasFactory;

    protected $table = 'anamnesa';

    protected $fillable = [
        'mr_code',
        'visit_no',
        'bp_systolic',
        'bp_diastolic',
        'temperature',
        'weight_kg',
        'height_cm',
        'bmi',
        'anamnesa',
        'recorded_by',
        'updated_by',
    ];
 
    /**
     * Relasi user pembuat data
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi user pengubah data
     */
    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
