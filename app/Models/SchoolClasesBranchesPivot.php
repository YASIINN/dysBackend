<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SchoolClasesBranchesPivot extends Model
{
    // protected $fillable = ["school_id,clases_id,branches_id"];

        // public  function  school(){
        //     return $this->belongsTo(School::class,"school_id","id");
        // }

        // public  function  clases(){
        //     return $this->belongsTo(Clases::class,"clases_id","id");
        // }

        // public  function  branches(){
        //     return $this->belongsTo(Branches::class,"branches_id","id");
        // }

        //adem
        protected $table = "school_clases_branches_pivots";
        protected $primaryKey = "scbid";
        public function school()
        {
            return $this->belongsTo(School::class);
        }
    
        public function clases()
        {
            return $this->belongsTo(Clases::class);
        }
        public function branches()
        {
            return $this->belongsTo(Branches::class);
        }
        public static function boot()
        {
            parent::boot();
            static::created(function ($scb) {
                $sName = $scb->school->sName;
                $sCode = $scb->school->sCode;
                $cName = $scb->clases->cName;
                $cCode = $scb->clases->cCode;
                $bName = $scb->branches->bName;
                $bCode = $scb->branches->bCode;
                $g = new Group;
                $g->name = $sName . ' ' . $cName . ' ' . $bName;
                $g->code = $sCode . ' ' . $cCode . ' ' . $bCode;
                $scb->group()->save($g);
            });
            static::deleted(function ($scb) {
                $scb->group()->delete();
            });
        }
    
    
        public function group()
        {
            return $this->morphOne(Group::class, 'groupable');
        }
        //adem

}
