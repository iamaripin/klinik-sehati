<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    protected $table = 'admissions';

    protected $fillable = [
        'mr_code',
        'visit_no',
        'visit_date',
        'visit_time',
        'poli',
        'doctor_code',
        'visit_type',
        'payment_type',
        'diagnosis',
        'complaint',
        'reservation_code',
        'status',
        'payment_type',
        'insurance_used',
        'insurance_number_at_visit',
        'insurance_company_at_visit',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'insurance_used' => 'boolean',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'mr_code', 'mr_code');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, 'doctor_code', 'doctor_code');
    }

    public function generalQueue()
    {
        return $this->hasMany(GeneralQueue::class, 'visit_no', 'visit_no');
    }
}
