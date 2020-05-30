<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomGroup extends Model
{
         //adem
         public static function boot()
         {
             parent::boot();
             static::created(function ($cg) {
                 $g = new Group;
                 $g->name = $cg->name;
                 $g->code = "custom";
                 $cg->group()->save($g);
             });
             static::deleting(function ($cg) { // before delete() method call this
                 $cg->group()->delete();
             });
             static::updated(function ($cg) { // before delete() method call this
                $g = Group::find($cg->group->id);
                $g->name = $cg->name;
                $cg->group()->save($g);
            });
         }
    
      public function group()
      {
          return $this->morphOne(Group::class, 'groupable');
      }
}
