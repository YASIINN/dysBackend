<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class StudentUserPivot extends Pivot
{
    protected $table = "student_user";
    public function users()
    {
        return $this->belongsTo(Users::class);
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
