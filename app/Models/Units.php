<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Units extends Model
{
    protected $fillable = ["uName, uCode"];
    public  function user(){
        return $this->hasMany(Users::class,"uunits_id","id");
    }
}
