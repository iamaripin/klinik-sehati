<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Icd10 extends Model
{
    protected $table = 'icd10';

    protected $fillable = [
        'code',
        'description',
        'chapter',
        'block',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scope
    |--------------------------------------------------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where('code', 'like', "%{$keyword}%")
            ->orWhere('description', 'like', "%{$keyword}%");
    }
}
