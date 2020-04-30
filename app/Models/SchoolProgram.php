<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolProgram extends Model
{
    protected $table = "school_program";

    public function getContent()
    {
        return $this->hasMany(SchoolProgramContent::class, "school_program_id");
    }

    public function getClases()
    {
        return $this->belongsTo(Clases::class, "clases_id", "id");
    }

    public function getBranches()
    {
        return $this->belongsTo(Branches::class, "branches_id", "id");
    }
    public  function getSchoolProgramType(){
        return $this->belongsTo(SchoolPType::class, "school_p_type_id", "id")->with(['school','p_type']);

    }

    //adem
    public function scontents(){
        {
                return $this->hasMany(SchoolProgramContent::class)->with(["users", "lesson", "discounts"]);
        }
    }
    //
}
