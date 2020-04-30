<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolProgramContentUserPivot extends Model
{
    //
    protected $table = "school_program_content_user";

    public function getContent(){
        return $this->belongsTo(SchoolProgramContent::class,"school_program_content_id");
    }

    public  function  getUser(){
        return $this->belongsTo(Users::class,"user_id");
    }
}
