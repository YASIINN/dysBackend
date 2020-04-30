<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    protected $table = "team";

    public function sporclub()
    {
        return $this->belongsTo(SporClub::class, "spor_club_id", "id");
    }
}
