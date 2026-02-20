<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
        protected $table = 'suppliers';
    protected $fillable = [
        'supplier_code',
        'supplier_name',
        'contact_name',
        'phone',
        'address',
        'email',
    ];
}
