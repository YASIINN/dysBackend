<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SDetail extends Model
{
    protected $casts = [
        'id' => 'int',
        'illness' => 'array',
        'medicines' => 'array',
        'allergy'=>'array',
        'chronic_disease'=>'array',
        'scholarship'=>'array',
   ];
    protected $table = "sdetails";
    public function student()
        {
            return $this->belongsTo(Student::class);
        }


}
