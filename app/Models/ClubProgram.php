<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubProgram extends Model
{
    protected $table = "club_program";

    public function getTeams()
    {
        return $this->belongsTo(Team::class, "team_id", "id");
    }

    public function getBranches()
    {
        return $this->belongsTo(SporClubBranch::class, "spor_club_branch_id", "id");
    }

    public function getContent()
    {
        return $this->hasMany(ClubProgramContent::class, "club_program_id");
    }

    public function getClubProgramType()
    {
        return $this->belongsTo(ClubPTypePivot::class,
            "club_p_type_id", "id")
            ->with(['spor_club', 'p_type']);

    }

    public function spor_club()
    {
        return $this->belongsTo(SporClub::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }


    public function spor_club_branch()
    {
        return $this->belongsTo(SporClubBranch::class);
    }

    public function ccontents()
    {
        return $this->hasMany(ClubProgramContent::class)->with(["users"]);

    }
}
