<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubPTypePivot extends Model
{

    protected $table = "club_p_type";

    public function sporclub()
    {
        return $this->belongsTo(SporClub::class);
    }


    public function spor_club()
    {
        return $this->belongsTo(SporClub::class);
    }

    public function p_type()
    {
        return $this->belongsTo(PType::class);
    }
}
