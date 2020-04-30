<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class ClubTeamBranchStudentPivot extends Pivot
{
    protected $table = 'club_team_branch_student';
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
        return $this->belongsTo(SporClubBranch::class, "spor_club_branch_id", "id");
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
