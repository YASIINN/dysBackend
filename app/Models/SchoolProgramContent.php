<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolProgramContent extends Model
{
    //
    protected $table = "school_program_contents";


    public function hour()
    {
        return $this->belongsTo(SchoolHour::class, "school_hour_id", "id");
    }

    public function day()
    {
        return $this->belongsTo(SchoolDay::class, "school_day_id", "id");
    }

    public function getProgram()
    {
        return $this->belongsTo(SchoolProgram::class, "school_program_id")->with(['getClases', 'getBranches', 'getSchoolProgramType']);
    }

    public function getUsers()
    {
        return $this->belongsToMany(Users::class, "school_program_content_user", "school_program_content_id", "user_id");
    }

    public function getLesson()
    {
        return $this->belongsTo(Lessons::class, "lesson_id");
    }

    //adem
    public function users()
    {
        return $this->belongsToMany(Users::class, "school_program_content_user", "school_program_content_id", "user_id");

    }

    public function lesson()
    {
        return $this->belongsTo(Lessons::class, "lesson_id");
    }

    public function discounts()
    {
        return $this->morphMany(Discontinuity::class, 'contentable');
    }
    //Adem
}
