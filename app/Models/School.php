<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    protected $fillable = ["sName,sCode,company_id"];

    public function ptype()
    {
        return $this->belongsToMany(School::class, "school_p_type", "school_id", "p_type_id");
    }

    public function getCompanies()
    {
        return $this->belongsTo(Company::class, "company_id", "id");
    }

    public function users()
    {
        return $this->belongsToMany(Users::class, "users_schools", "school_id", "users_id");
    }

    public function schoolclasesbranchespivot()
    {
        return $this->belongsTo(SchoolClasesBranchesPivot::class, "school_id", "id");
    }

    public  function students(){
        return $this->belongsToMany(Student::class, "school_student", "school_id", "student_id");

    }

    public function schoolclases()
    {
        return $this->belongsTo(SchoolClasesPivot::class, "school_id", "id");
    }
    public function ptypes(){
        {
            return $this->belongsToMany(PType::class, "school_p_type","school_id", "p_type_id");
        }
    }
}
