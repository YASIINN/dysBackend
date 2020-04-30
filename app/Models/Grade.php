<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grade extends Model
{
    protected $fillable = ["gName", "gCode"];
    public function activities(){
        return $this->belongsToMany(Activity::class, "activity_period");
    }
    public function periods(){
        return $this->belongsToMany(Period::class, "activity_period");
    }
    public function apschedules()
    {
        return $this->belongsToMany(ActivityPTypePivot::class, "activity_program", "id", "activity_p_type_id");
    }

}
