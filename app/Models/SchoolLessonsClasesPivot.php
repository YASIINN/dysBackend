<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SchoolLessonsClasesPivot extends Pivot
{
    protected $table = 'school_lessons_clases_pivots';
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lessons::class);
    }
    public function clases()
    {
        return $this->belongsTo(Clases::class);
    }
}
