<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubProgramContent extends Model
{
    protected $table = "club_program_contents";

    public function hour()
    {
        return $this->belongsTo(ClubHour::class, "club_hour_id", "id");
    }

    public function day()
    {
        return $this->belongsTo(ClubDay::class, "club_day_id", "id");
    }

    public function getProgram()
    {
        return $this->belongsTo(ClubProgram::class, "club_program_id")
            ->with(['getTeams', 'getBranches', 'getClubProgramType']);

    }

    public function clubprogram()
    {
        {
            return $this->belongsTo(ClubProgram::class, "club_program_id", "id")->with(['getClubProgramType']);
        }
    }

    public function cp()
    {
        return $this->clubprogram();
    }

    public function users()
    {
        return $this->belongsToMany(Users::class, "club_program_content_user", "club_content_id", "user_id");
    }

}
