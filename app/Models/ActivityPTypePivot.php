<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityPTypePivot extends Model
{
    protected $table = "activity_p_type";

    public function activity()
    {
        return $this->belongsTo(Activity::class);
    }
    public function period()
    {
        return $this->belongsTo(Period::class);
    }
    public function p_type()
    {
        return $this->belongsTo(PType::class);
    }
}
