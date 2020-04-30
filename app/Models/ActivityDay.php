<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityDay extends Model
{
    public function activity_p_type()
    {
        return $this->belongsTo(ActivityPTypePivot::class);
    }
}
