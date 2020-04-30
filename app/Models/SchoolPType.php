<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolPType extends Model
{
    protected $table = "school_p_type";

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function p_type()
    {
        return $this->belongsTo(PType::class);
    }
}
