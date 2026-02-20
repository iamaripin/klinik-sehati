<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeneralQueue extends Model
{
    protected $table = 'general_queue';

    protected $fillable = [
        'mr_code',
        'visit_no',
        'visit_date',
        'doctor_code',
        'queue_no',
        'poli',
        'queue_status',
        'queue_prefix',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'visit_date' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class, 'mr_code', 'mr_code');
    }

    public function admission()
    {
        return $this->belongsTo(Admission::class, 'visit_no', 'visit_no');
    }
}
