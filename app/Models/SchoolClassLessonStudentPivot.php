<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use DB;
class SchoolClassLessonStudentPivot extends Pivot
{
    protected $table = 'school_clases_lesson_student';
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function clases()
    {
        return $this->belongsTo(Clases::class);
    }
    public function lessons()
    {
        return $this->belongsTo(Lessons::class);
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}