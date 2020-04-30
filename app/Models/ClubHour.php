<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubHour extends Model
{
    public function club_p_type()
    {
        return $this->belongsTo(ClubPTypePivot::class);
    }
}
