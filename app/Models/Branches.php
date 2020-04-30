<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Branches extends Model
{
    protected $fillable = ["bName","bCode"];

    public function getSchoolClasesBranches()
    {
        return $this->belongsTo(SchoolClasesBranchesPivot::class, "branches_id", "id");
    }
}
