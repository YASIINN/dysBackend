<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Files extends Model
{
    use SoftDeletes;
    //
    public  function user(){
        return $this->hasMany(Users::class,"ufile_id","id");
    }
    public  function student(){
        return $this->hasOne(Student::class,"file_id","id");
    }
}
