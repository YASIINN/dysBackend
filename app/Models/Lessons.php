<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lessons extends Model
{
    protected $fillable = ["lName, lCode,parent_id,type"];

    public function periods(){
        return $this->belongsToMany(Period::class, "activity_period_lesson");
    }
    public function activities(){
        return $this->belongsToMany(Activity::class, "activity_period_lesson");
    }
    public function users()
    {
        return $this->belongsToMany(Users::class, "users_lessons","lesson_id","users_id");
    }

    public function getProgramContent()
    {
        return $this->belongsTo(SchoolProgramContent::class, "lesson_id");
    }
}
