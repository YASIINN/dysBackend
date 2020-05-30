<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SchoolClasesBranchesPivot as SCB;

class SchoolClasesPivot extends Model
{
    protected $table = "school_clases_pivots";
    protected $fillable = ["school_id,clases_id"];
    protected $primaryKey = "scid";

     public function school()
     {
         return $this->belongsTo(School::class, "school_id", "id");
     }

     public function clases()
    {
         return $this->belongsTo(Clases::class, "clases_id", "id");
     }

     //adem
     public static function boot()
     {
         parent::boot();
         static::created(function ($sc) {
             $sName = $sc->school->sName;
             $sCode = $sc->school->sCode;
             $cName = $sc->clases->cName;
             $cCode = $sc->clases->cCode;
             $g = new Group;
             $g->name = $sName . ' ' . $cName;
             $g->code = $sCode . ' ' . $cCode;
             $sc->group()->save($g);
         });

         static::deleted(function ($sc) {
            $sc->group()->delete();
            $scbs = SCB::where("school_id",$sc->school->id)->where("clases_id",$sc->clases->id)->get();
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
