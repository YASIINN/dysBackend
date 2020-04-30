<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ActivityStudentPivot extends Pivot
{
    protected $table = 'activity_student';
    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }

    public function isActive(){
        return $this->belongsTo(Activity::class, "activity_period");
    }


    public function period()
    {
        return $this->belongsTo(Period::class);
    }
    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
    public function students(){
        return $this->belongsToMany(Student::class, "activity_student", "id", "student_id");
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
