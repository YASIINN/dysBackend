<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Title extends Model
{
    protected $fillable = ["tName, tCode"];
    public  function user(){
        return $this->hasMany(Users::class,"utitle_id","id");
    }
}
