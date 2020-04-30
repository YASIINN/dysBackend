<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class SchoolClasesBranchesPivot extends Pivot
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
        //adem

}
