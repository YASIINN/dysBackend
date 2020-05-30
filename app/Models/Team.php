<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Team extends Model
{
    protected $table = "team";

    public function sporclub()
    {
        return $this->belongsTo(SporClub::class, "spor_club_id", "id");
    }

       //adem

       public function branches()
       {
           return $this->hasMany(SporClubTeamBranch::class, "team_id", "id");
       }
       public static function boot()
       {
           parent::boot();
           static::created(function ($team) {
               $club = $team->sporclub;
               $g = new Group;
               $g->name = $club->scName . ' ' . $team->stName;
               $g->code = $club->scCode . ' ' . $team->stCode;
               $team->group()->save($g);
           });
           static::deleting(function ($team) { // before delete() method call this
               $team->group()->delete();
               foreach ($team->branches as $key => $branch) {
                $branch->group()->delete();
            }
           });
       }
       public function group()
       {
           return $this->morphOne(Group::class, 'groupable');
       }
       //adem
}
