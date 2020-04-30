<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Relations\Pivot;
use DB;

class SchoolStudentPivot extends Pivot
{
    protected $table = 'school_student';
    public function school()
        {
            return $this->belongsTo(School::class);
        }
    
        public function clases()
        {
            return $this->belongsTo(Clases::class);
        }
        public function branches()
        {
            return $this->belongsTo(Branches::class);
        }
        public function student()
        {
            return $this->belongsTo(Student::class);
        }
}