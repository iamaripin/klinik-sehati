<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PatientRelation extends Model
{
    protected $table = 'patient_relations';

    protected $fillable = [
        'relation_code',
        'relation_nik',
        'relation_name',
        'relation_status',
        'relation_sex',
        'relation_dob',
        'relation_phone',
        'relation_address',
        'relation_blood',
    ];

    protected $casts = [
        'relation_dob' => 'date',
    ];

    /**
     * RELATION: Relation → belongsTo Patient
     * Key: relation_code → mr_code
     */
    public function patient()
    {
        return $this->belongsTo(Patient::class, 'relation_code', 'mr_code');
    }
}
