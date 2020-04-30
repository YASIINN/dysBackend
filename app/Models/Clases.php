<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Clases extends Model
{
    protected $fillable = ["cName","cCode"];
    public function schoolclasesbranchespivot()
     {
         return $this->belongsTo(SchoolClasesBranchesPivot::class, "clases_id", "id");
    }
     public  function  schoolclases(){
         return $this->belongsTo(SchoolClasesPivot::class, "clases_id", "id");
     }
    public  function   users(){
        return $this->belongsToMany(Users::class, "users_schools_clases");
    }
}
