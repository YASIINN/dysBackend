<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Activity;

class Period extends Model
{
    protected $fillable = ["pName", "pCode"];

    public function activities(){
        return $this->belongsToMany(Activity::class);
    }
    public function uniqactivities(){
        return $this->belongsToMany(Activity::class);
       }
    public function grades(){
        return $this->belongsToMany(Grade::class);
    }
    public  function lessons(){
        return $this->belongsToMany(Lessons::class);
    }


}
