<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discount extends Model
{
    protected $table = 'discounts';
    protected $fillable = [
        'discount_no',
        'admission_id',
        'mr_code',
        'visit_no',
        'amount_discount',
        'user_id'
    ]; 
     
    public function user()
    {
    return $this->belongsTo(User::class);
    }
 
}