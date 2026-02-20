<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    protected $table = 'patients';

    protected $primaryKey = 'mr_code';
    public $incrementing = false; // karena mr_code bukan auto increment
    protected $keyType = 'string';

    protected $fillable = [
        'mr_code',
        'patient_nik',
        'patient_card_number',
        'patient_name',
        'patient_sex',
        'patient_dob',
        'patient_contact',
        'patient_address',
        'patient_religion',
        'patient_job',
        'patient_status',
        'patient_blood',
        'patient_relation_name',
        'patient_emergency_contact',
        'patient_alergy',
        'patient_notes'
    ];

    protected $casts = [
        'patient_dob' => 'date',
    ];

    /**
     * RELATION: Patient → hasMany PatientRelation
     * Key: mr_code → relation_code
     */
    public function relations()
    {
        return $this->hasMany(PatientRelation::class, 'relation_code', 'mr_code');
    }
}
