<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use DB;
class ActivityPeriodLessonPivot extends Pivot
{
    protected $table = 'activity_period_lesson';
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
}