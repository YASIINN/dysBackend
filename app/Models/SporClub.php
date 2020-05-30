<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SporClub extends Model
{
    protected $fillable = ["scName,scCode,company_id"];
    protected $table = "spor_club";

    public function getCompanies()
    {
        return $this->belongsTo(Company::class, "company_id", "id");
    }

    public function users()
    {
        return $this->belongsToMany(Users::class, "user_spor_club");
    }

    public function team()
    {
        return $this->hasMany(Team::class, "spor_club_id", "id");
    }

       //adem

    public function branches()
    {
        return $this->hasMany(SporClubTeamBranch::class, "spor_club_id", "id");
    }
    public static function boot()
    {
        parent::boot();
        static::created(function ($club) {
            $g = new Group;
            $g->name = $club->scName;
            $g->code = $club->scCode;
            $club->group()->save($g);
        });

        static::deleting(function ($club) { // before delete() method call this
            $club->group()->delete();
            foreach ($club->team as $key => $team) {
                $team->group()->delete();
            }
            foreach ($club->branches as $key => $branch) {
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
