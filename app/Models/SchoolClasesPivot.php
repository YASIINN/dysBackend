<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolClasesPivot extends Model
{
    protected $fillable = ["school_id,clases_id"];

     public function school()
     {
         return $this->belongsTo(School::class, "school_id", "id");
     }

     public function clases()
    {
         return $this->belongsTo(Clases::class, "clases_id", "id");
     }
}
