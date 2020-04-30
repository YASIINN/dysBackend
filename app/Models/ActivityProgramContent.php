<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityProgramContent extends Model
{
    protected $table = "activity_program_contents";

    public function hour()
    {
        return $this->belongsTo(ActivityHour::class, "activity_hour_id", "id");

    }

    public function day()
    {
        return $this->belongsTo(ActivityDay::class, "activity_day_id", "id");
    }

    public function activityprogram()
    {
        {
            return $this->belongsTo(ActivityProgram::class, "activity_program_id", "id")->with(['getActivityProgramType', 'grade']);
        }
    }

    public function lesson()
    {
        return $this->belongsTo(Lessons::class, "lesson_id");
    }


    public function grade()
    {
        return $this->activityprogram();
    }

    // public function users()
    //     {
    //       return $this->hasMany(ActivityProgramContentUserPivot::class, "ap_content_id", "id");

    //     }

    public function users()
    {
        return $this->belongsToMany(Users::class, "activity_program_content_user", "ap_content_id", "user_id");

    }


    /*Yasin*/


    public function getProgram()
    {
        return $this->belongsTo(ActivityProgram::class, "activity_program_id")->with(['getGrades', 'getActivityProgramType']);
        /*    ->with(['getClases', 'getBranches', 'getSchoolProgramType']);*/
    }

    public function getUsers()
    {
        return $this->belongsToMany(Users::class, "activity_program_content_user", "ap_content_id", "user_id");
        /* return $this->hasMany(SchoolProgramContentUserPivot::class, "school_program_content_id")->with("getUser");*/
    }

    public function getLesson()
    {
        return $this->belongsTo(Lessons::class, "lesson_id");
    }


    public function discounts()
    {
        return $this->morphMany(Discontinuity::class, 'contentable');
    }

    /*Yasin*/
}
