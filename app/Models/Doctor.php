<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Doctor extends Model
{
    use HasFactory;

    protected $table = 'doctors';

    protected $fillable = [
        'doctor_code',
        'doctor_nik',
        'doctor_tittle',
        'doctor_name',
        'doctor_suffix',
        'doctor_prefix',
        'doctor_sex',
        'doctor_dob',
        'doctor_phone',
        'doctor_address',
        'medical_code',
        'doctor_email',
        'doctor_photo',
        'is_active',
        'specialist',
        'sip_number',
        'sip_expiry',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'doctor_dob' => 'date',
        'sip_expiry' => 'date',
        'is_active' => 'boolean',
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
