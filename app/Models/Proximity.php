<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Proximity extends Model
{
    //
    public  function user(){
        return $this->hasMany(Users::class,"uproximities_id","id");
    }
}
