<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    protected $fillable = ["cName"];
    // protected $table = "companies";
     public  function getSchool(){
         return $this->hasMany(School::class,"company_id","id");
     }
     public  function  getSporClub(){
         return $this->hasMany(SporClub::class,"company_id","id");
     }
}
