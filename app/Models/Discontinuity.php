<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discontinuity extends Model
{
    public function contentable()
    {
        return $this->morphTo();
    }


    public  function  dtype(){
        return $this->belongsToMany(DType::class, "discontinuities", 'id', 'd_type_id');
    }

    public function student()
    {
        return $this->belongsToMany(Student::class, "discontinuities", 'id', 'student_id')
            ->with(["schools", "clases", "file", "branches",'grades','activities','periods']);
    }
}
