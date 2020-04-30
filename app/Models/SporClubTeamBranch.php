<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SporClubTeamBranch extends Model
{
    protected $table = "spor_club_team_branch";


    public function club()
    {
        return $this->belongsTo(SporClub::class, "spor_club_id", "id");
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
    public function branch()
    {
        return $this->belongsTo(SporClubBranch::class, "sbranch_id", "id");
    }
}
