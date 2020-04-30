<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PType extends Model
{
    public  function   school()
    {
        return $this->belongsToMany(School::class, "school_p_type","p_type_id","school_id")->withPivot([
            'id',
        ]);;
    }
}
