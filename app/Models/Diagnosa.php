<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Diagnosa extends Model
{
    use SoftDeletes;

    protected $table = 'diagnosa';

    protected $fillable = [
        'admission_id',
        'mr_code',
        'visit_no',
        'anamnesa_dokter',
        'pemeriksaan_fisik',
        'diagnosa',
        'diagnosa_icd',
        'tindakan_icd',
        'rencana_tindak_lanjut',
        'kontrol_kembali',
        'recorded_by',
        'recorded_at',
        'is_final',
    ];

    protected $casts = [
        'kontrol_kembali' => 'date',
        'recorded_at'     => 'datetime',
        'is_final'        => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function admission()
    {
        return $this->belongsTo(Admission::class);
    }

    public function recorder()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    public function finalize()
    {
        $this->update([
            'is_final' => true,
            'recorded_at' => now(),
        ]);
    }
}
