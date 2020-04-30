<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use DB;
class ActivityPeriodLessonStudentPivot extends Pivot
{
    protected $table = 'activity_period_lesson_student';
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
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