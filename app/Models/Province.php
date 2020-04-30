<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Province extends Model
{
    protected $fillable = ["pName, pCode"];
    public  function user(){
        return $this->hasMany(Users::class,"uprovince_id","id");
    }
}
