<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersSchoolsClasesBranches extends Model
{
    // public static function boot()
    // {
    //     parent::boot();
    //     static::created(function ($school) {
    //         $g = new Group;
    //         $g->name = $school->sName;
    //         $g->code = $school->sCode;
    //         return $school->group()->save($g);
    //     });
    //     static::deleting(function ($school) { // before delete() method call this
    //         $school->group()->delete();
    //     });
    // }
}
