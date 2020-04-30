<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Model;

class ActivityProgram extends Model
{
    protected $table = "activity_program";


    public function getContent()
    {
        return $this->hasMany(ActivityProgramContent::class, "activity_program_id");
    }

    public function getGrades()
    {
        return $this->belongsTo(Grade::class, "grade_id", "id");
    }

    /*    public function getBranches()
        {
            return $this->belongsTo(Branches::class, "branches_id", "id");
        }
        */
    public function getActivityProgramType()
    {
        return $this->belongsTo(ActivityPTypePivot::class, "activity_p_type_id", "id")->with(['activity', 'period', 'p_type']);

    }


    public function grade()
    {
        return $this->belongsTo(Grade::class);
    }
    // public function contents()
    // {
    //         return $this->hasMany(ActivityProgramContent::class)->with(["users.user", "lesson"]);
    // }
    public function acontents()
    {
        {
            return $this->hasMany(ActivityProgramContent::class)->with(["users", "lesson"]);
        }
    }
}
