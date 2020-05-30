<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\SchoolClasesPivot as SC;
use App\Models\SchoolClasesBranchesPivot as SCB;

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

    public static function boot()
    {
        parent::boot();
        static::created(function ($school) {
            $g = new Group;
            $g->name = $school->sName;
            $g->code = $school->sCode;
            $school->group()->save($g);
        });
        static::deleting(function ($school) { // before delete() method call this
            $school->group()->delete();
            $a = $school->id;
            $scs = SC::where("school_id",$school->id)->get();
            foreach ($scs as $key => $sc) {
                $sc->group()->delete();
            }
            $scbs = SCB::where("school_id",$school->id)->get();
            foreach ($scbs as $key => $scb) {
                $scb->group()->delete();
            }
        });
      
    }


    public function group()
    {
        return $this->morphOne(Group::class, 'groupable');
    }
}
